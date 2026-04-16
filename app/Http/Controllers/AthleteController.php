<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\Team;
use App\Models\Branch;
use App\Models\PerformanceRecord;
use App\Models\AiGeneratedContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AthleteController extends Controller
{
    /**
     * Display a listing of athletes.
     */
    public function index(Request $request)
    {
        try {
            $query = Athlete::with(['team', 'branch', 'user']);

            // Search
            if ($request->filled('search')) {
                $query->where('full_name', 'like', '%' . $request->search . '%');
            }

            // Filter by team
            if ($request->filled('team_id')) {
                $query->where('team_id', $request->team_id);
            }

            // Filter by branch
            if ($request->filled('branch_id')) {
                $query->where('branch_id', $request->branch_id);
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('is_active', $request->status === 'active');
            }

            $athletes = $query->latest()->paginate(20);
            $teams = Team::orderBy('name')->get();
            $branches = Branch::orderBy('name')->get();
        } catch (\Exception $e) {
            // Se não houver tenant ativo ou tabelas não existirem, usar valores padrão
            $athletes = new \Illuminate\Pagination\LengthAwarePaginator(
                collect([]),
                0,
                20,
                1
            );
            $teams = collect([]);
            $branches = collect([]);
        }

        return view('athletes.index', compact('athletes', 'teams', 'branches'));
    }

    /**
     * Show the form for creating a new athlete.
     */
    public function create()
    {
        try {
            $teams = Team::orderBy('name')->get();
            $branches = Branch::orderBy('name')->get();
        } catch (\Exception $e) {
            $teams = collect([]);
            $branches = collect([]);
        }
        
        return view('athletes.create', compact('teams', 'branches'));
    }

    /**
     * Store a newly created athlete.
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'birth_date' => 'required|date|before:today',
            'position' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'bio' => 'nullable|string|max:1000',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_contact' => 'nullable|string|max:20',
            'guardian_email' => 'nullable|email|max:255',
            'team_id' => 'nullable|exists:teams,id',
            'branch_id' => 'nullable|exists:branches,id',
            'jersey_number' => 'nullable|string|max:10',
            'height' => 'nullable|numeric|min:0|max:300',
            'weight' => 'nullable|numeric|min:0|max:500',
            'emergency_contact' => 'nullable|string|max:20',
            'medical_conditions' => 'nullable|array',
            'allergies' => 'nullable|array',
            'insurance_info' => 'nullable|string|max:500',
            'create_user_account' => 'boolean',
            'user_email' => 'required_if:create_user_account,true|email|unique:users,email',
            'user_password' => 'required_if:create_user_account,true|string|min:8',
        ]);

        $athlete = new Athlete($request->except(['profile_picture', 'create_user_account', 'user_email', 'user_password']));
        
        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('athletes', 'public');
            $athlete->profile_picture_url = $path;
        }

        $athlete->save();

        // Create user account if requested
        if ($request->boolean('create_user_account')) {
            $user = User::create([
                'name' => $athlete->full_name,
                'email' => $request->user_email,
                'password' => Hash::make($request->user_password),
                'role' => 'athlete',
                'athlete_id' => $athlete->id,
                'is_active' => true,
            ]);
        }

        return redirect()->route('admin.athletes.show', $athlete)
            ->with('success', 'Atleta criado com sucesso!');
    }

    /**
     * Display the specified athlete.
     */
    public function show(Athlete $athlete)
    {
        $athlete->load(['team', 'branch', 'user', 'performanceRecords.recordedBy']);
        
        return view('athletes.show', compact('athlete'));
    }

    /**
     * Show the form for editing the athlete.
     */
    public function edit(Athlete $athlete)
    {
        $teams = Team::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();
        
        return view('athletes.edit', compact('athlete', 'teams', 'branches'));
    }

    /**
     * Update the specified athlete.
     */
    public function update(Request $request, Athlete $athlete)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'birth_date' => 'required|date|before:today',
            'position' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'bio' => 'nullable|string|max:1000',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_contact' => 'nullable|string|max:20',
            'guardian_email' => 'nullable|email|max:255',
            'team_id' => 'nullable|exists:teams,id',
            'branch_id' => 'nullable|exists:branches,id',
            'jersey_number' => 'nullable|string|max:10',
            'height' => 'nullable|numeric|min:0|max:300',
            'weight' => 'nullable|numeric|min:0|max:500',
            'emergency_contact' => 'nullable|string|max:20',
            'medical_conditions' => 'nullable|array',
            'allergies' => 'nullable|array',
            'insurance_info' => 'nullable|string|max:500',
        ]);

        $athlete->update($request->except(['profile_picture']));

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            // Delete old picture
            if ($athlete->profile_picture_url) {
                Storage::disk('public')->delete($athlete->profile_picture_url);
            }
            
            $path = $request->file('profile_picture')->store('athletes', 'public');
            $athlete->update(['profile_picture_url' => $path]);
        }

        return redirect()->route('admin.athletes.show', $athlete)
            ->with('success', 'Atleta atualizado com sucesso!');
    }

    /**
     * Remove the specified athlete.
     */
    public function destroy(Athlete $athlete)
    {
        // Delete profile picture
        if ($athlete->profile_picture_url) {
            Storage::disk('public')->delete($athlete->profile_picture_url);
        }

        $athlete->delete();

        return redirect()->route('athletes.index')
            ->with('success', 'Atleta removido com sucesso!');
    }

    /**
     * Show athlete performance page.
     */
    public function performance(Athlete $athlete)
    {
        $athlete->load(['performanceRecords.recordedBy']);
        
        $performance_records = $athlete->performanceRecords()
            ->with('recordedBy')
            ->orderBy('recorded_at', 'desc')
            ->paginate(20);

        return view('athletes.performance', compact('athlete', 'performance_records'));
    }

    /**
     * Store a new performance record.
     */
    public function storePerformance(Request $request, Athlete $athlete)
    {
        $request->validate([
            'metric' => 'required|string|max:255',
            'value' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'recorded_at' => 'required|date',
        ]);

        PerformanceRecord::create([
            'athlete_id' => $athlete->id,
            'metric' => $request->metric,
            'value' => $request->value,
            'notes' => $request->notes,
            'recorded_by' => auth()->id(),
            'recorded_at' => $request->recorded_at,
        ]);

        return redirect()->route('admin.athletes.show', $athlete)
            ->with('success', 'Registro de performance adicionado com sucesso!');
    }

    /**
     * Show athlete financial page.
     */
    public function financial(Athlete $athlete)
    {
        $athlete->load(['orders.orderItems.product']);
        
        $orders = $athlete->orders()
            ->with(['orderItems.product'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('athletes.financial', compact('athlete', 'orders'));
    }

    /**
     * Show athlete AI plans page.
     */
    public function aiPlans(Athlete $athlete)
    {
        $athlete->load(['aiGeneratedContent']);
        
        $ai_plans = $athlete->aiGeneratedContent()
            ->orderBy('generated_at', 'desc')
            ->paginate(20);

        return view('athletes.ai-plans', compact('athlete', 'ai_plans'));
    }

    /**
     * Get athlete performance data for charts.
     *
     * @param Request $request
     * @param Athlete $athlete
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPerformanceData(Request $request, Athlete $athlete)
    {
        $period = $request->get('period', '6months'); // 1month, 3months, 6months, 1year
        
        $startDate = match($period) {
            '1month' => now()->subMonth(),
            '3months' => now()->subMonths(3),
            '6months' => now()->subMonths(6),
            '1year' => now()->subYear(),
            default => now()->subMonths(6),
        };

        $performanceRecords = PerformanceRecord::where('athlete_id', $athlete->id)
            ->where('recorded_at', '>=', $startDate)
            ->orderBy('recorded_at', 'asc')
            ->get();

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

    /**
     * Get athlete financial history.
     *
     * @param Request $request
     * @param Athlete $athlete
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFinancialHistory(Request $request, Athlete $athlete)
    {
        $orders = $athlete->orders()
            ->with(['orderItems.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        $history = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'date' => $order->created_at->format('d/m/Y'),
                'total' => $order->total_amount,
                'status' => $order->status,
                'status_label' => $order->status_label,
                'items' => $order->orderItems->map(function ($item) {
                    return [
                        'product_name' => $item->product->name ?? 'Produto removido',
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'total' => $item->total,
                    ];
                }),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $history,
        ]);
    }

    /**
     * Get athlete AI plans.
     *
     * @param Request $request
     * @param Athlete $athlete
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAiPlans(Request $request, Athlete $athlete)
    {
        $type = $request->get('type'); // workout_plan, meal_plan, recovery_plan
        
        $query = $athlete->aiGeneratedContent()
            ->orderBy('generated_at', 'desc');

        if ($type) {
            $query->where('type', $type);
        }

        $plans = $query->get()->map(function ($plan) {
            return [
                'id' => $plan->id,
                'type' => $plan->type,
                'type_label' => match($plan->type) {
                    'workout_plan' => 'Plano de Treino',
                    'meal_plan' => 'Plano Nutricional',
                    'recovery_plan' => 'Plano de Recuperação',
                    default => 'Plano',
                },
                'generated_at' => $plan->generated_at->format('d/m/Y H:i'),
                'is_favorite' => $plan->is_favorite,
                'tokens_used' => $plan->tokens_used,
                'cost' => $plan->cost,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $plans,
        ]);
    }
}
