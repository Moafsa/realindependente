<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\Team;
use App\Models\Branch;
use App\Models\Order;
use App\Models\PerformanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show the dashboard.
     */
    public function index()
    {
        try {
            // Get basic statistics
            $stats = [
                'total_athletes' => Athlete::count(),
                'active_athletes' => Athlete::where('is_active', true)->count(),
                'total_teams' => Team::count(),
                'total_branches' => Branch::count(),
                'total_orders' => Order::count(),
                'total_revenue' => Order::where('status', 'paid')->sum('total_amount'),
            ];

            // Get recent athletes
            $recent_athletes = Athlete::with(['team', 'branch'])
                ->latest()
                ->take(5)
                ->get();

            // Get team statistics
            $team_stats = Team::withCount('athletes')
                ->orderBy('athletes_count', 'desc')
                ->take(5)
                ->get();

            // Get performance trends (last 6 months)
            $performance_trends = PerformanceRecord::select(
                    DB::raw('DATE_TRUNC(\'month\', recorded_at) as month'),
                    DB::raw('COUNT(*) as records_count')
                )
                ->where('recorded_at', '>=', now()->subMonths(6))
                ->groupBy('month')
                ->orderBy('month')
                ->get();
            // Get birthday athletes (next 7 days)
            $birthday_athletes = Athlete::whereRaw('EXTRACT(DOY FROM birth_date) BETWEEN ? AND ?', [
                now()->dayOfYear,
                now()->addDays(7)->dayOfYear
            ])->get();

            // Get recent orders
            $recent_orders = Order::with(['user', 'athlete'])
                ->latest()
                ->take(5)
                ->get();

            // Get monthly revenue (last 6 months)
            $revenue_trends = Order::select(
                    DB::raw('DATE_TRUNC(\'month\', created_at) as month'),
                    DB::raw('SUM(total_amount) as revenue')
                )
                ->where('status', 'paid')
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        } catch (\Exception $e) {
            // Se não houver tenant ativo ou tabelas não existirem, usar valores padrão
            $stats = [
                'total_athletes' => 0,
                'active_athletes' => 0,
                'total_teams' => 0,
                'total_branches' => 0,
                'total_orders' => 0,
                'total_revenue' => 0,
            ];
            $recent_athletes = collect([]);
            $team_stats = collect([]);
            $performance_trends = collect([]);
            $birthday_athletes = collect([]);
            $recent_orders = collect([]);
            $revenue_trends = collect([]);
        }

        return view('dashboard.index', compact(
            'stats',
            'recent_athletes',
            'team_stats',
            'performance_trends',
            'birthday_athletes',
            'recent_orders',
            'revenue_trends'
        ));
    }

    /**
     * Get dashboard metrics (API endpoint).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMetrics(Request $request)
    {
        try {
            $metrics = [
                'total_athletes' => Athlete::count(),
                'active_athletes' => Athlete::where('is_active', true)->count(),
                'total_teams' => Team::count(),
                'total_branches' => Branch::count(),
                'total_orders' => Order::count(),
                'total_revenue' => Order::where('status', 'paid')->sum('total_amount'),
                'monthly_revenue' => Order::where('status', 'paid')
                    ->whereMonth('created_at', now()->month)
                    ->sum('total_amount'),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'birthday_athletes_count' => Athlete::whereRaw('EXTRACT(DOY FROM birth_date) BETWEEN ? AND ?', [
                    now()->dayOfYear,
                    now()->addDays(7)->dayOfYear
                ])->count(),
            ];
        } catch (\Exception $e) {
            $metrics = [
                'total_athletes' => 0,
                'active_athletes' => 0,
                'total_teams' => 0,
                'total_branches' => 0,
                'total_orders' => 0,
                'total_revenue' => 0,
                'monthly_revenue' => 0,
                'pending_orders' => 0,
                'birthday_athletes_count' => 0,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $metrics,
        ]);
    }

    /**
     * Get recent payments (API endpoint).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecentPayments(Request $request)
    {
        try {
            $limit = $request->get('limit', 10);
            
            $payments = Order::where('status', 'paid')
                ->with(['user', 'athlete'])
                ->latest()
                ->take($limit)
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'date' => $order->paid_at ? $order->paid_at->format('d/m/Y H:i') : $order->created_at->format('d/m/Y H:i'),
                        'amount' => $order->total_amount,
                        'formatted_amount' => $order->formatted_total,
                        'customer' => $order->user->name ?? $order->athlete->full_name ?? 'Cliente',
                        'status' => $order->status,
                    ];
                });
        } catch (\Exception $e) {
            $payments = collect([]);
        }

        return response()->json([
            'success' => true,
            'data' => $payments,
        ]);
    }

    /**
     * Get athlete evolution data for charts (API endpoint).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAthleteEvolution(Request $request)
    {
        try {
            $period = $request->get('period', '6months');
            
            $startDate = match($period) {
                '1month' => now()->subMonth(),
                '3months' => now()->subMonths(3),
                '6months' => now()->subMonths(6),
                '1year' => now()->subYear(),
                default => now()->subMonths(6),
            };

            // Get new athletes per month
            $evolution = Athlete::select(
                    DB::raw('DATE_TRUNC(\'month\', created_at) as month'),
                    DB::raw('COUNT(*) as count')
                )
                ->where('created_at', '>=', $startDate)
                ->groupBy('month')
                ->orderBy('month')
                ->get()
                ->map(function ($item) {
                    return [
                        'month' => $item->month->format('Y-m'),
                        'month_label' => $item->month->format('M/Y'),
                        'count' => $item->count,
                    ];
                });
        } catch (\Exception $e) {
            $evolution = collect([]);
        }

        return response()->json([
            'success' => true,
            'data' => $evolution,
            'period' => $period,
        ]);
    }
}
