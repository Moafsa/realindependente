<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\AiGeneratedContent;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AIController extends Controller
{
    private AIService $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Generate workout plan for an athlete.
     */
    public function generateWorkout(Request $request, Athlete $athlete)
    {
        try {
            $workoutPlan = $this->aiService->generateWorkoutPlan($athlete);
            
            return response()->json([
                'success' => true,
                'data' => $workoutPlan,
                'message' => 'Plano de treino gerado com sucesso!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate nutrition plan for an athlete.
     */
    public function generateNutrition(Request $request, Athlete $athlete)
    {
        try {
            $nutritionPlan = $this->aiService->generateNutritionPlan($athlete);
            
            return response()->json([
                'success' => true,
                'data' => $nutritionPlan,
                'message' => 'Plano nutricional gerado com sucesso!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get athlete's AI content.
     */
    public function getAthleteContent(Athlete $athlete, Request $request)
    {
        $type = $request->get('type'); // 'workout_plan' or 'meal_plan'
        $content = $this->aiService->getAthleteContent($athlete, $type);
        
        return response()->json([
            'success' => true,
            'data' => $content
        ]);
    }

    /**
     * Toggle favorite status of AI content.
     */
    public function toggleFavorite(AiGeneratedContent $content)
    {
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
        return response()->json([
            'success' => true,
            'data' => $content
        ]);
    }

    /**
     * Delete AI content.
     */
    public function deleteContent(AiGeneratedContent $content)
    {
        try {
            $content->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Conteúdo removido com sucesso!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get AI usage statistics.
     */
    public function getUsageStats()
    {
        $stats = [
            'total_generations' => AiGeneratedContent::count(),
            'workout_plans' => AiGeneratedContent::where('type', 'workout_plan')->count(),
            'nutrition_plans' => AiGeneratedContent::where('type', 'meal_plan')->count(),
            'total_tokens' => AiGeneratedContent::sum('tokens_used'),
            'total_cost' => AiGeneratedContent::sum('cost'),
            'favorite_content' => AiGeneratedContent::where('is_favorite', true)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get AI usage statistics by tenant.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUsageByTenant(Request $request)
    {
        // TODO: Implement when tenant tracking is available
        // For now, return current tenant stats
        $stats = [
            'total_generations' => AiGeneratedContent::count(),
            'workout_plans' => AiGeneratedContent::where('type', 'workout_plan')->count(),
            'nutrition_plans' => AiGeneratedContent::where('type', 'meal_plan')->count(),
            'total_tokens' => AiGeneratedContent::sum('tokens_used'),
            'total_cost' => AiGeneratedContent::sum('cost'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get AI costs breakdown.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCosts(Request $request)
    {
        $period = $request->get('period', 'month'); // day, week, month, year
        
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
                COUNT(*) as generations,
                type
            ')
            ->groupBy('date', 'type')
            ->orderBy('date')
            ->get();

        $totalCost = AiGeneratedContent::where('created_at', '>=', $startDate)
            ->sum('cost');

        $totalTokens = AiGeneratedContent::where('created_at', '>=', $startDate)
            ->sum('tokens_used');

        return response()->json([
            'success' => true,
            'data' => [
                'costs' => $costs,
                'total_cost' => $totalCost,
                'total_tokens' => $totalTokens,
                'period' => $period,
            ]
        ]);
    }
}
