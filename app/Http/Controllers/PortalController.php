<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\PerformanceRecord;
use App\Models\AiGeneratedContent;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PortalController extends Controller
{
    private AIService $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Show athlete portal dashboard.
     */
    public function dashboard()
    {
        try {
            $athlete = Auth::user()->athlete;
            
            if (!$athlete) {
                return redirect()->route('login')
                    ->with('error', 'Usuário não possui atleta associado.');
            }

            $athlete->load(['team', 'branch', 'user', 'performanceRecords' => function ($query) {
                $query->latest()->take(10);
            }]);

            // Get recent AI content
            $recentAiContent = AiGeneratedContent::where('athlete_id', $athlete->id)
                ->latest()
                ->take(3)
                ->get();

            // Get performance trends
            $performanceTrends = PerformanceRecord::where('athlete_id', $athlete->id)
                ->selectRaw('metric, AVG(CAST(value AS DECIMAL)) as avg_value, MAX(recorded_at) as last_recorded')
                ->groupBy('metric')
                ->get();

            // Get stats
            $stats = [
                'total_plans' => AiGeneratedContent::where('athlete_id', $athlete->id)->count(),
                'active_plans' => AiGeneratedContent::where('athlete_id', $athlete->id)
                    ->where('status', 'active')
                    ->count(),
                'pending_plans' => AiGeneratedContent::where('athlete_id', $athlete->id)
                    ->where('status', 'pending')
                    ->count(),
                'total_trainings' => 0, // TODO: Implement when Training model exists
                'completed_trainings' => 0,
                'upcoming_trainings' => 0,
                'performance_score' => $this->calculatePerformanceScore($athlete),
                'performance_change' => $this->calculatePerformanceChange($athlete),
                'total_goals' => 0, // TODO: Implement when Goal model exists
                'achieved_goals' => 0,
                'in_progress_goals' => 0,
            ];
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'Erro ao carregar dados do atleta. Por favor, tente novamente.');
        }

        return view('portal.dashboard', compact('athlete', 'recentAiContent', 'performanceTrends', 'stats'));
    }

    /**
     * Calculate athlete's overall performance score.
     */
    private function calculatePerformanceScore(Athlete $athlete): float
    {
        $records = PerformanceRecord::where('athlete_id', $athlete->id)
            ->where('recorded_at', '>=', now()->subMonths(3))
            ->get();

        if ($records->isEmpty()) {
            return 0;
        }

        $avgValue = $records->avg(function ($record) {
            return (float) $record->value;
        });

        // Normalize to 0-100 scale (assuming max value is 100)
        return min(100, max(0, $avgValue));
    }

    /**
     * Calculate performance change percentage.
     */
    private function calculatePerformanceChange(Athlete $athlete): float
    {
        $recent = PerformanceRecord::where('athlete_id', $athlete->id)
            ->where('recorded_at', '>=', now()->subWeek())
            ->avg(function ($record) {
                return (float) $record->value;
            });

        $previous = PerformanceRecord::where('athlete_id', $athlete->id)
            ->where('recorded_at', '>=', now()->subWeeks(2))
            ->where('recorded_at', '<', now()->subWeek())
            ->avg(function ($record) {
                return (float) $record->value;
            });

        if (!$previous || $previous == 0) {
            return 0;
        }

        return round((($recent - $previous) / $previous) * 100, 1);
    }

    /**
     * Show athlete profile.
     */
    public function profile()
    {
        $athlete = Auth::user()->athlete;
        
        if (!$athlete) {
            return redirect()->route('login')
                ->with('error', 'Usuário não possui atleta associado.');
        }

        $athlete->load(['team', 'branch', 'user']);

        return view('portal.profile', compact('athlete'));
    }

    /**
     * Update athlete profile.
     */
    public function updateProfile(Request $request)
    {
        $athlete = Auth::user()->athlete;
        
        if (!$athlete) {
            return redirect()->route('login')
                ->with('error', 'Usuário não possui atleta associado.');
        }

        $request->validate([
            'full_name' => 'required|string|max:255',
            'birth_date' => 'required|date|before:today',
            'position' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'bio' => 'nullable|string|max:1000',
            'jersey_number' => 'nullable|string|max:10',
            'height' => 'nullable|numeric|min:0|max:300',
            'weight' => 'nullable|numeric|min:0|max:500',
        ]);

        $athlete->fill($request->except('profile_picture'));

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('athletes', 'public');
            $athlete->profile_picture_url = $path;
        }

        $athlete->save();

        return redirect()->route('portal.profile')
            ->with('success', 'Perfil atualizado com sucesso!');
    }

    /**
     * Show athlete performance.
     */
    public function performance()
    {
        $athlete = Auth::user()->athlete;
        
        if (!$athlete) {
            return redirect()->route('login')
                ->with('error', 'Usuário não possui atleta associado.');
        }

        $performanceRecords = PerformanceRecord::where('athlete_id', $athlete->id)
            ->with('recordedBy')
            ->orderBy('recorded_at', 'desc')
            ->paginate(20);

        // Get performance metrics summary
        $metrics = PerformanceRecord::where('athlete_id', $athlete->id)
            ->selectRaw('metric, COUNT(*) as count, AVG(CAST(value AS DECIMAL)) as avg_value, MAX(recorded_at) as last_recorded')
            ->groupBy('metric')
            ->get();

        return view('portal.performance', compact('athlete', 'performanceRecords', 'metrics'));
    }

    /**
     * Show athlete AI plans.
     */
    public function aiPlans()
    {
        $athlete = Auth::user()->athlete;
        
        if (!$athlete) {
            return redirect()->route('login')
                ->with('error', 'Usuário não possui atleta associado.');
        }

        $aiContent = AiGeneratedContent::where('athlete_id', $athlete->id)
            ->orderBy('generated_at', 'desc')
            ->paginate(20);

        return view('portal.ai-plans', compact('athlete', 'aiContent'));
    }

    /**
     * Generate new AI plan.
     */
    public function generatePlan(Request $request)
    {
        $athlete = Auth::user()->athlete;
        
        if (!$athlete) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não possui atleta associado.'
            ], 400);
        }

        $request->validate([
            'type' => 'required|in:workout_plan,meal_plan',
        ]);

        try {
            if ($request->type === 'workout_plan') {
                $content = $this->aiService->generateWorkoutPlan($athlete);
            } else {
                $content = $this->aiService->generateNutritionPlan($athlete);
            }

            return response()->json([
                'success' => true,
                'data' => $content,
                'message' => 'Plano gerado com sucesso!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show communication page.
     */
    public function communication()
    {
        $athlete = Auth::user()->athlete;
        
        if (!$athlete) {
            return redirect()->route('login')
                ->with('error', 'Usuário não possui atleta associado.');
        }

        // Get team coach
        $coach = $athlete->team->coach ?? null;

        return view('portal.communication', compact('athlete', 'coach'));
    }

    /**
     * Get athlete's AI content.
     */
    public function getAiContent(Request $request)
    {
        $athlete = Auth::user()->athlete;
        
        if (!$athlete) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não possui atleta associado.'
            ], 400);
        }

        $type = $request->get('type');
        $content = $this->aiService->getAthleteContent($athlete, $type);

        return response()->json([
            'success' => true,
            'data' => $content
        ]);
    }

    /**
     * Toggle favorite AI content.
     */
    public function toggleFavorite(AiGeneratedContent $content)
    {
        // Check if content belongs to authenticated athlete
        if ($content->athlete_id !== Auth::user()->athlete->id) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado.'
            ], 403);
        }

        try {
            $isFavorite = $this->aiService->toggleFavorite($content);

            return response()->json([
                'success' => true,
                'is_favorite' => $isFavorite,
                'message' => $isFavorite ? 'Adicionado aos favoritos' : 'Removido dos favoritos'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get AI content details.
     */
    public function getContent(AiGeneratedContent $content)
    {
        // Check if content belongs to authenticated athlete
        if ($content->athlete_id !== Auth::user()->athlete->id) {
            return response()->json([
                'success' => false,
                'message' => 'Acesso negado.'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $content
        ]);
    }

    /**
     * Get upcoming trainings for athlete.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUpcomingTrainings()
    {
        $athlete = Auth::user()->athlete;
        
        if (!$athlete) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não possui atleta associado.'
            ], 400);
        }

        // TODO: Implement when Training model is created
        // For now, return empty array
        $trainings = [];

        return response()->json([
            'success' => true,
            'data' => $trainings
        ]);
    }

    /**
     * Get notifications for athlete.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNotifications()
    {
        $athlete = Auth::user()->athlete;
        
        if (!$athlete) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não possui atleta associado.'
            ], 400);
        }

        // TODO: Implement when Notification model is created
        // For now, return empty array
        $notifications = [];

        return response()->json([
            'success' => true,
            'data' => $notifications
        ]);
    }

    /**
     * Get performance data for charts (API endpoint).
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPerformanceData(Request $request)
    {
        $athlete = Auth::user()->athlete;
        
        if (!$athlete) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não possui atleta associado.'
            ], 400);
        }

        $period = $request->get('period', '6months');
        $metric = $request->get('metric');
        
        $startDate = match($period) {
            '1month' => now()->subMonth(),
            '3months' => now()->subMonths(3),
            '6months' => now()->subMonths(6),
            '1year' => now()->subYear(),
            default => now()->subMonths(6),
        };

        $query = PerformanceRecord::where('athlete_id', $athlete->id)
            ->where('recorded_at', '>=', $startDate);

        if ($metric && $metric !== 'all') {
            $query->where('metric', $metric);
        }

        $performanceRecords = $query->orderBy('recorded_at', 'asc')->get();

        // Group by metric
        $data = [];
        foreach ($performanceRecords as $record) {
            if (!isset($data[$record->metric])) {
                $data[$record->metric] = [];
            }
            
            $data[$record->metric][] = [
                'date' => $record->recorded_at->format('Y-m-d'),
                'value' => (float) $record->value,
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'period' => $period,
        ]);
    }
}
