<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Athlete;
use App\Models\AiPlan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class AthleteController extends Controller
{
    /**
     * Display a listing of athletes (public).
     */
    public function publicIndex(Request $request): JsonResponse
    {
        $athletes = Athlete::where('is_active', true)
            ->with(['team'])
            ->when($request->team_id, function ($query, $teamId) {
                return $query->where('team_id', $teamId);
            })
            ->when($request->position, function ($query, $position) {
                return $query->where('position', $position);
            })
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $athletes,
        ]);
    }

    /**
     * Display the specified athlete (public).
     */
    public function publicShow(Athlete $athlete): JsonResponse
    {
        if (!$athlete->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Athlete not found',
            ], 404);
        }

        $athlete->load(['team']);

        return response()->json([
            'success' => true,
            'data' => $athlete,
        ]);
    }

    /**
     * Display a listing of athletes (admin).
     */
    public function index(Request $request): JsonResponse
    {
        $athletes = Athlete::with(['team', 'user'])
            ->when($request->search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                           ->orWhere('email', 'like', "%{$search}%");
            })
            ->when($request->team_id, function ($query, $teamId) {
                return $query->where('team_id', $teamId);
            })
            ->when($request->position, function ($query, $position) {
                return $query->where('position', $position);
            })
            ->when($request->is_active !== null, function ($query, $isActive) {
                return $query->where('is_active', $isActive);
            })
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'data' => $athletes,
        ]);
    }

    /**
     * Store a newly created athlete.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:athletes,email',
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'required|date|before:today',
            'position' => 'required|in:goalkeeper,defender,midfielder,forward',
            'height' => 'nullable|integer|min:100|max:250',
            'weight' => 'nullable|numeric|min:20|max:200',
            'team_id' => 'nullable|exists:teams,id',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'medical_info' => 'nullable|array',
            'physical_attributes' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $athlete = Athlete::create($request->all());

        $athlete->load(['team', 'user']);

        return response()->json([
            'success' => true,
            'message' => 'Athlete created successfully',
            'data' => $athlete,
        ], 201);
    }

    /**
     * Display the specified athlete.
     */
    public function show(Athlete $athlete): JsonResponse
    {
        $athlete->load(['team', 'user']);

        return response()->json([
            'success' => true,
            'data' => $athlete,
        ]);
    }

    /**
     * Update the specified athlete.
     */
    public function update(Request $request, Athlete $athlete): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:athletes,email,' . $athlete->id,
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'sometimes|required|date|before:today',
            'position' => 'sometimes|required|in:goalkeeper,defender,midfielder,forward',
            'height' => 'nullable|integer|min:100|max:250',
            'weight' => 'nullable|numeric|min:20|max:200',
            'team_id' => 'nullable|exists:teams,id',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'medical_info' => 'nullable|array',
            'physical_attributes' => 'nullable|array',
            'performance_data' => 'nullable|array',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $athlete->update($request->all());
        $athlete->load(['team', 'user']);

        return response()->json([
            'success' => true,
            'message' => 'Athlete updated successfully',
            'data' => $athlete,
        ]);
    }

    /**
     * Remove the specified athlete.
     */
    public function destroy(Athlete $athlete): JsonResponse
    {
        $athlete->delete();

        return response()->json([
            'success' => true,
            'message' => 'Athlete deleted successfully',
        ]);
    }

    /**
     * Toggle athlete status.
     */
    public function toggleStatus(Athlete $athlete): JsonResponse
    {
        $athlete->update(['is_active' => !$athlete->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Athlete status updated successfully',
            'data' => $athlete,
        ]);
    }

    /**
     * Get athlete performance data.
     */
    public function getPerformance(Athlete $athlete): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'performance_data' => $athlete->performance_data,
                'physical_attributes' => $athlete->physical_attributes,
                'last_updated' => $athlete->updated_at,
            ],
        ]);
    }

    /**
     * Update athlete performance data.
     */
    public function updatePerformance(Request $request, Athlete $athlete): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'performance_data' => 'required|array',
            'physical_attributes' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $athlete->update([
            'performance_data' => $request->performance_data,
            'physical_attributes' => $request->physical_attributes ?? $athlete->physical_attributes,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Performance data updated successfully',
            'data' => $athlete,
        ]);
    }

    /**
     * Get athlete AI plans.
     */
    public function getAiPlans(Athlete $athlete): JsonResponse
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
}
