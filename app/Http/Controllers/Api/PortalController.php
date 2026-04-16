<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\PerformanceRecord;
use App\Models\AiGeneratedContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PortalController extends Controller
{
    /**
     * Retorna dados de performance do atleta para gráficos.
     */
    public function getPerformanceData(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->athlete) {
            return response()->json(['error' => 'Usuário não possui atleta associado'], 404);
        }

        $athlete = $user->athlete;
        $period = $request->input('period', '6months'); // 1month, 3months, 6months, 1year

        // Calcula a data inicial baseado no período
        $startDate = match($period) {
            '1month' => now()->subMonth(),
            '3months' => now()->subMonths(3),
            '6months' => now()->subMonths(6),
            '1year' => now()->subYear(),
            default => now()->subMonths(6),
        };

        // Busca registros de performance
        $records = PerformanceRecord::where('athlete_id', $athlete->id)
            ->where('recorded_at', '>=', $startDate)
            ->orderBy('recorded_at', 'asc')
            ->get();

        // Agrupa por métrica
        $metrics = $records->groupBy('metric');

        $data = [];
        foreach ($metrics as $metric => $metricRecords) {
            $data[$metric] = $metricRecords->map(function ($record) {
                return [
                    'date' => $record->recorded_at->format('Y-m-d'),
                    'value' => (float) $record->value,
                ];
            })->values();
        }

        // Calcula média da equipe se o atleta tiver equipe
        $teamAverage = null;
        if ($athlete->team_id) {
            $teamAverage = PerformanceRecord::whereHas('athlete', function ($query) use ($athlete) {
                $query->where('team_id', $athlete->team_id);
            })
            ->where('metric', $request->input('metric', 'velocidade_max'))
            ->where('recorded_at', '>=', $startDate)
            ->avg('value');
        }

        return response()->json([
            'athlete_data' => $data,
            'team_average' => $teamAverage ? (float) $teamAverage : null,
            'period' => $period,
            'start_date' => $startDate->format('Y-m-d'),
        ]);
    }

    /**
     * Retorna próximos treinos do atleta.
     */
    public function getUpcomingTrainings(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->athlete) {
            return response()->json(['error' => 'Usuário não possui atleta associado'], 404);
        }

        $athlete = $user->athlete;
        $limit = $request->input('limit', 5);

        // Busca treinos futuros relacionados ao atleta ou sua equipe
        $trainings = \App\Models\Training::where(function ($query) use ($athlete) {
            $query->where('athlete_id', $athlete->id)
                  ->orWhere('team_id', $athlete->team_id);
        })
        ->where('date', '>=', now())
        ->orderBy('date', 'asc')
        ->orderBy('time', 'asc')
        ->limit($limit)
        ->get();

        return response()->json([
            'trainings' => $trainings->map(function ($training) {
                return [
                    'id' => $training->id,
                    'date' => $training->date->format('Y-m-d'),
                    'time' => $training->time,
                    'location' => $training->location,
                    'description' => $training->description,
                    'team_name' => $training->team->name ?? null,
                ];
            }),
        ]);
    }

    /**
     * Retorna notificações do atleta.
     */
    public function getNotifications(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->athlete) {
            return response()->json(['error' => 'Usuário não possui atleta associado'], 404);
        }

        $athlete = $user->athlete;
        $limit = $request->input('limit', 10);
        $unreadOnly = $request->boolean('unread_only', false);

        $query = \App\Models\Notification::where('athlete_id', $athlete->id)
            ->orWhere(function ($q) use ($athlete) {
                $q->where('team_id', $athlete->team_id)
                  ->whereNull('athlete_id');
            });

        if ($unreadOnly) {
            $query->where('read_at', null);
        }

        $notifications = $query->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->title,
                    'message' => $notification->message,
                    'type' => $notification->type,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                ];
            }),
            'unread_count' => \App\Models\Notification::where('athlete_id', $athlete->id)
                ->whereNull('read_at')
                ->count(),
        ]);
    }
}

