<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\AiPlan;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class AIController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Generate workout plan for athlete.
     */
    public function generateWorkout(Request $request, Athlete $athlete): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'goals' => 'required|array',
            'goals.*' => 'string',
            'duration_weeks' => 'integer|min:1|max:12',
            'intensity' => 'in:low,medium,high',
            'restrictions' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $athleteData = [
                'name' => $athlete->name,
                'age' => $athlete->birth_date->age,
                'weight' => $athlete->weight,
                'height' => $athlete->height,
                'position' => $athlete->position,
                'physical_attributes' => $athlete->physical_attributes,
                'performance_data' => $athlete->performance_data,
            ];

            $goals = $request->goals;
            $durationWeeks = $request->duration_weeks ?? 4;
            $intensity = $request->intensity ?? 'medium';
            $restrictions = $request->restrictions ?? [];

            $workoutPlan = $this->aiService->generateTrainingPlan(
                $athleteData,
                $goals,
                $durationWeeks,
                $intensity,
                $restrictions
            );

            // Save the plan to database
            $aiPlan = AiPlan::create([
                'type' => 'training',
                'title' => 'Plano de Treino - ' . $athlete->name,
                'description' => 'Plano de treino personalizado gerado por IA',
                'content' => $workoutPlan,
                'parameters' => [
                    'goals' => $goals,
                    'duration_weeks' => $durationWeeks,
                    'intensity' => $intensity,
                    'restrictions' => $restrictions,
                ],
                'goals' => $goals,
                'restrictions' => $restrictions,
                'duration_weeks' => $durationWeeks,
                'start_date' => now(),
                'athlete_id' => $athlete->id,
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Workout plan generated successfully',
                'data' => $aiPlan,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate workout plan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate nutrition plan for athlete.
     */
    public function generateNutrition(Request $request, Athlete $athlete): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'goals' => 'required|array',
            'goals.*' => 'string',
            'dietary_restrictions' => 'nullable|array',
            'allergies' => 'nullable|array',
            'preferences' => 'nullable|array',
            'meals_per_day' => 'integer|min:3|max:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $athleteData = [
                'name' => $athlete->name,
                'age' => $athlete->birth_date->age,
                'weight' => $athlete->weight,
                'height' => $athlete->height,
                'position' => $athlete->position,
                'physical_attributes' => $athlete->physical_attributes,
                'medical_info' => $athlete->medical_info,
            ];

            $goals = $request->goals;
            $dietaryRestrictions = $request->dietary_restrictions ?? [];
            $allergies = $request->allergies ?? [];
            $preferences = $request->preferences ?? [];
            $mealsPerDay = $request->meals_per_day ?? 4;

            $nutritionPlan = $this->aiService->generateNutritionPlan(
                $athleteData,
                $goals,
                $dietaryRestrictions,
                $allergies,
                $preferences,
                $mealsPerDay
            );

            // Save the plan to database
            $aiPlan = AiPlan::create([
                'type' => 'nutrition',
                'title' => 'Plano Nutricional - ' . $athlete->name,
                'description' => 'Plano nutricional personalizado gerado por IA',
                'content' => $nutritionPlan,
                'parameters' => [
                    'goals' => $goals,
                    'dietary_restrictions' => $dietaryRestrictions,
                    'allergies' => $allergies,
                    'preferences' => $preferences,
                    'meals_per_day' => $mealsPerDay,
                ],
                'goals' => $goals,
                'restrictions' => array_merge($dietaryRestrictions, $allergies),
                'duration_weeks' => 4,
                'start_date' => now(),
                'athlete_id' => $athlete->id,
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Nutrition plan generated successfully',
                'data' => $aiPlan,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate nutrition plan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate recovery plan for athlete.
     */
    public function generateRecovery(Request $request, Athlete $athlete): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'injury_type' => 'nullable|string',
            'recovery_goals' => 'required|array',
            'recovery_goals.*' => 'string',
            'duration_weeks' => 'integer|min:1|max:24',
            'current_pain_level' => 'integer|min:1|max:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $athleteData = [
                'name' => $athlete->name,
                'age' => $athlete->birth_date->age,
                'weight' => $athlete->weight,
                'height' => $athlete->height,
                'position' => $athlete->position,
                'medical_info' => $athlete->medical_info,
                'physical_attributes' => $athlete->physical_attributes,
            ];

            $recoveryPlan = $this->aiService->generateRecoveryPlan(
                $athleteData,
                $request->recovery_goals,
                $request->injury_type,
                $request->duration_weeks ?? 4,
                $request->current_pain_level ?? 5
            );

            // Save the plan to database
            $aiPlan = AiPlan::create([
                'type' => 'recovery',
                'title' => 'Plano de Recuperação - ' . $athlete->name,
                'description' => 'Plano de recuperação personalizado gerado por IA',
                'content' => $recoveryPlan,
                'parameters' => [
                    'recovery_goals' => $request->recovery_goals,
                    'injury_type' => $request->injury_type,
                    'duration_weeks' => $request->duration_weeks ?? 4,
                    'current_pain_level' => $request->current_pain_level ?? 5,
                ],
                'goals' => $request->recovery_goals,
                'restrictions' => [],
                'duration_weeks' => $request->duration_weeks ?? 4,
                'start_date' => now(),
                'athlete_id' => $athlete->id,
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Recovery plan generated successfully',
                'data' => $aiPlan,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate recovery plan',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get athlete AI plans.
     */
    public function getAthletePlans(Athlete $athlete): JsonResponse
    {
        $plans = AiPlan::where('athlete_id', $athlete->id)
            ->with(['createdBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $plans,
        ]);
    }

    /**
     * Get specific AI plan.
     */
    public function getPlan(AiPlan $plan): JsonResponse
    {
        $plan->load(['athlete', 'createdBy']);

        return response()->json([
            'success' => true,
            'data' => $plan,
        ]);
    }

    /**
     * Toggle favorite status for AI plan.
     */
    public function toggleFavorite(AiPlan $plan): JsonResponse
    {
        // This would require a favorites table in a real implementation
        // For now, we'll just return success
        return response()->json([
            'success' => true,
            'message' => 'Favorite status updated',
        ]);
    }

    /**
     * Delete AI plan.
     */
    public function deletePlan(AiPlan $plan): JsonResponse
    {
        $plan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Plan deleted successfully',
        ]);
    }

    /**
     * Get AI usage statistics.
     */
    public function getStats(): JsonResponse
    {
        $stats = [
            'total_plans' => AiPlan::count(),
            'plans_this_month' => AiPlan::whereMonth('created_at', now()->month)->count(),
            'plans_by_type' => AiPlan::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->get()
                ->pluck('count', 'type'),
            'average_rating' => AiPlan::whereNotNull('rating')->avg('rating'),
            'top_performing_plans' => AiPlan::whereNotNull('rating')
                ->orderBy('rating', 'desc')
                ->limit(5)
                ->get(['id', 'title', 'type', 'rating']),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
        ]);
    }
}
