<?php

namespace App\Http\Controllers;

use App\Models\AiGeneratedContent;
use App\Models\Athlete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AIReportController extends Controller
{
    /**
     * Display AI usage report.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $period = $request->get('period', 'month');
        
        $startDate = match($period) {
            'day' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };

        // Overall statistics
        $stats = [
            'total_generations' => AiGeneratedContent::where('created_at', '>=', $startDate)->count(),
            'total_tokens' => AiGeneratedContent::where('created_at', '>=', $startDate)->sum('tokens_used'),
            'total_cost' => AiGeneratedContent::where('created_at', '>=', $startDate)->sum('cost'),
            'workout_plans' => AiGeneratedContent::where('created_at', '>=', $startDate)
                ->where('type', 'workout_plan')->count(),
            'nutrition_plans' => AiGeneratedContent::where('created_at', '>=', $startDate)
                ->where('type', 'meal_plan')->count(),
        ];

        // Usage by athlete
        $usageByAthlete = AiGeneratedContent::where('created_at', '>=', $startDate)
            ->select('athlete_id', DB::raw('COUNT(*) as count'), DB::raw('SUM(cost) as total_cost'))
            ->groupBy('athlete_id')
            ->with('athlete')
            ->orderBy('count', 'desc')
            ->take(10)
            ->get();

        // Usage by type
        $usageByType = AiGeneratedContent::where('created_at', '>=', $startDate)
            ->select('type', DB::raw('COUNT(*) as count'), DB::raw('SUM(cost) as total_cost'))
            ->groupBy('type')
            ->get();

        // Daily usage trend
        $dailyTrend = AiGeneratedContent::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(cost) as cost')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('ai.reports.index', compact('stats', 'usageByAthlete', 'usageByType', 'dailyTrend', 'period'));
    }

    /**
     * Export AI usage report.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv'); // csv, json
        
        $period = $request->get('period', 'month');
        
        $startDate = match($period) {
            'day' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };

        $data = AiGeneratedContent::where('created_at', '>=', $startDate)
            ->with('athlete')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($content) {
                return [
                    'id' => $content->id,
                    'type' => $content->type,
                    'athlete' => $content->athlete->full_name ?? 'N/A',
                    'tokens_used' => $content->tokens_used,
                    'cost' => $content->cost,
                    'created_at' => $content->created_at->format('Y-m-d H:i:s'),
                ];
            });

        if ($format === 'json') {
            return response()->json([
                'success' => true,
                'data' => $data,
                'period' => $period,
            ]);
        }

        // CSV format
        $filename = 'ai-usage-report-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, ['ID', 'Tipo', 'Atleta', 'Tokens', 'Custo', 'Data']);
            
            // Data
            foreach ($data as $row) {
                fputcsv($file, [
                    $row['id'],
                    $row['type'],
                    $row['athlete'],
                    $row['tokens_used'],
                    $row['cost'],
                    $row['created_at'],
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get AI costs report (API endpoint).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCostsReport(Request $request)
    {
        $period = $request->get('period', 'month');
        
        $startDate = match($period) {
            'day' => now()->startOfDay(),
            'week' => now()->startOfWeek(),
            'month' => now()->startOfMonth(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };

        $costs = AiGeneratedContent::where('created_at', '>=', $startDate)
            ->selectRaw('
                DATE(created_at) as date,
                SUM(cost) as total_cost,
                SUM(tokens_used) as total_tokens,
                COUNT(*) as generations
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $totalCost = AiGeneratedContent::where('created_at', '>=', $startDate)
            ->sum('cost');

        return response()->json([
            'success' => true,
            'data' => [
                'costs' => $costs,
                'total_cost' => $totalCost,
                'period' => $period,
            ]
        ]);
    }
}

