<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\Team;
use App\Models\Branch;
use App\Models\User;
use App\Models\Order;
use App\Models\PerformanceRecord;
use App\Models\AiGeneratedContent;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AthleteController extends Controller
{
    /**
     * Display a listing of athletes.
     */
    public function index(Request $request)
    {
        try {
            $query = Athlete::with(['team', 'branch', 'user']);

            $user = auth()->user();
            if ($user->role === 'coach') {
                $coachTeams = \App\Models\Team::where('coach_id', $user->id)->pluck('id')->toArray();
                $query->whereIn('team_id', $coachTeams);
            }

            // Search
            if ($request->filled('search')) {
                $searchTerm = '%' . strtolower($request->search) . '%';
                $query->where(function($q) use ($searchTerm) {
                    $q->whereRaw('LOWER(full_name) LIKE ?', [$searchTerm])
                      ->orWhere('document', 'like', $searchTerm);
                });
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

            // Filter by subcategory
            if ($request->filled('subcategory')) {
                $query->where('subcategory', $request->subcategory);
            }

            $query->withCount(['aiGeneratedContent as pending_requests_count' => function ($q) {
                $q->where('status', 'pending');
            }]);

            // Restrição para Coaches
            $user = auth()->user();
            if ($user->role === 'coach') {
                $query->whereIn('team_id', Team::where('coach_id', $user->id)->pluck('id'));
            }

            $athletes = $query->latest()->paginate(20);
            
            $teamsQuery = Team::orderBy('name');
            if ($user->role === 'coach') {
                $teamsQuery->where('coach_id', $user->id);
            }
            $teams = $teamsQuery->get();
            
            $branches = Branch::orderBy('name')->get();
            $categories = ['Sub-7', 'Sub-9', 'Sub-11', 'Sub-13', 'Sub-15', 'Sub-17', 'Sub-20', 'Sub-23', 'Profissional'];
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
            $categories = [];
        }

        return view('athletes.index', compact('athletes', 'teams', 'branches', 'categories'));
    }

    /**
     * Show the form for creating a new athlete.
     */
    public function create()
    {
        try {
            $user = auth()->user();
            $teamsQuery = Team::orderBy('name');
            if ($user->role === 'coach') {
                $teamsQuery->where('coach_id', $user->id);
            }
            $teams = $teamsQuery->get();
            
            $branches = Branch::orderBy('name')->get();
            $categories = ['Sub-7', 'Sub-9', 'Sub-11', 'Sub-13', 'Sub-15', 'Sub-17', 'Sub-20', 'Sub-23', 'Profissional'];
        } catch (\Exception $e) {
            $teams = collect([]);
            $branches = collect([]);
            $categories = [];
        }
        
        return view('athletes.create', compact('teams', 'branches', 'categories'));
    }

    /**
     * Store a newly created athlete.
     */
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'document' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:1000',
            'birth_date' => 'required|date|before:today',
            'gender' => 'nullable|string|in:masculino,feminino',
            'position' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'bio' => 'nullable|string|max:1000',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_contact' => 'nullable|string|max:20',
            'guardian_email' => 'nullable|email|max:255',
            'guardian_document' => 'nullable|string|max:20',
            'team_id' => 'nullable|exists:teams,id',
            'branch_id' => 'nullable|exists:branches,id',
            'jersey_number' => 'nullable|string|max:10',
            'height' => 'nullable|numeric|min:0|max:300',
            'weight' => 'nullable|numeric|min:0|max:500',
            'emergency_contact' => 'nullable|string|max:20',
            'medical_conditions' => 'nullable|array',
            'allergies' => 'nullable|array',
            'insurance_info' => 'nullable|string|max:500',
            'medical_certificate' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:5120',
            'create_user_account' => 'boolean',
            'user_email' => 'required_if:create_user_account,true|email|unique:users,email',
            'user_password' => 'required_if:create_user_account,true|string|min:8',
        ]);

        $athlete = new Athlete($request->except(['profile_picture', 'medical_certificate', 'create_user_account', 'user_email', 'user_password']));
        
        // Atribuições Automáticas
        if (!$request->filled('subcategory')) {
            $athlete->subcategory = Athlete::calculateSubcategory($request->birth_date);
        }
        
        // Processar campos que podem vir como string mas são arrays
        $athlete->medical_conditions = $request->filled('medical_conditions') 
            ? array_values(array_filter(array_map('trim', explode(',', $request->medical_conditions))))
            : [];
            
        $athlete->allergies = $request->filled('allergies') 
            ? array_values(array_filter(array_map('trim', explode(',', $request->allergies))))
            : [];

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->store('profiles', 'public');
            $athlete->profile_picture_url = $path;
        }

        // Handle medical certificate upload
        if ($request->hasFile('medical_certificate')) {
            $path = $request->file('medical_certificate')->store('medical_certificates', 'public');
            $athlete->medical_certificate_path = $path;
        }

        $athlete->profile_completion = $athlete->getProfileCompletionPercentage();
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

    public function show(Athlete $athlete)
    {
        $user = auth()->user();
        if ($user->role === 'coach') {
            if (!$athlete->team || $athlete->team->coach_id !== $user->id) {
                abort(403, 'Acesso negado. Este atleta não pertence às suas equipes.');
            }
        }

        $athlete->load(['team', 'branch', 'user']);
        
        $performanceRecords = $athlete->performanceRecords()
            ->with('recordedBy')
            ->orderBy('recorded_at', 'desc')
            ->paginate(10, ['*'], 'performance_page')
            ->withQueryString()
            ->fragment('performance');

        // Data for individual metric sparklines
        $sparklineData = $athlete->performanceRecords()
            ->orderBy('recorded_at', 'asc')
            ->get()
            ->groupBy('metric')
            ->map(function ($records) {
                return $records->map(function ($r) {
                    return [
                        'x' => $r->recorded_at->format('Y-m-d'),
                        'y' => (float) $r->value
                    ];
                });
            });
        
        $ai_plans = $athlete->aiGeneratedContent()
            ->orderBy('generated_at', 'desc')
            ->get();
        
        // Nutrition data for comparative dashboard
        $activeMealPlan = $athlete->aiGeneratedContent()
            ->where('type', 'meal_plan')
            ->where('status', 'active')
            ->first();
            
        $mealLogs = \App\Models\AthleteMealLog::where('athlete_id', $athlete->id)
            ->where('consumed_at', '>=', now()->subDays(7))
            ->orderBy('consumed_at', 'desc')
            ->get();
            
        $nutritionDailyTotals = $mealLogs->groupBy(function($log) {
            return $log->consumed_at->format('Y-m-d');
        })->map(function($dayLogs) {
            return [
                'calories' => $dayLogs->sum('calories'),
                'protein' => $dayLogs->sum('proteins'),
                'carbs' => $dayLogs->sum('carbs'),
                'fat' => $dayLogs->sum('fats'),
            ];
        });
        
        return view('athletes.show', compact('athlete', 'performanceRecords', 'sparklineData', 'ai_plans', 'activeMealPlan', 'nutritionDailyTotals', 'mealLogs'));
    }

    /**
     * Show the form for editing the athlete.
     */
    public function edit(Athlete $athlete)
    {
        $user = auth()->user();
        if ($user->role === 'coach') {
            if (!$athlete->team || $athlete->team->coach_id !== $user->id) {
                abort(403, 'Acesso negado. Você não tem permissão para editar este atleta.');
            }
        }
        $teams = Team::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();
        $categories = ['Sub-7', 'Sub-9', 'Sub-11', 'Sub-13', 'Sub-15', 'Sub-17', 'Sub-20', 'Sub-23', 'Profissional'];
        
        return view('athletes.edit', compact('athlete', 'teams', 'branches', 'categories'));
    }

    /**
     * Update the specified athlete.
     */
    public function update(Request $request, Athlete $athlete)
    {
        $user = auth()->user();
        if ($user->role === 'coach') {
            if (!$athlete->team || $athlete->team->coach_id !== $user->id) {
                abort(403, 'Acesso negado. Você não tem permissão para atualizar este atleta.');
            }
        }
        $request->validate([
            'full_name' => 'required|string|max:255',
            'document' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:1000',
            'birth_date' => 'required|date|before:today',
            'gender' => 'nullable|string|in:masculino,feminino',
            'position' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'bio' => 'nullable|string|max:1000',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_contact' => 'nullable|string|max:20',
            'guardian_email' => 'nullable|email|max:255',
            'guardian_document' => 'nullable|string|max:20',
            'team_id' => 'nullable|exists:teams,id',
            'branch_id' => 'nullable|exists:branches,id',
            'jersey_number' => 'nullable|string|max:10',
            'height' => 'nullable|numeric|min:0|max:300',
            'weight' => 'nullable|numeric|min:0|max:500',
            'emergency_contact' => 'nullable|string|max:20',
            'medical_conditions' => 'nullable|string',
            'allergies' => 'nullable|string',
            'insurance_info' => 'nullable|string|max:500',
            'medical_certificate' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:5120',
            'password' => 'nullable|string|min:8|confirmed',
        ], [
            'profile_picture.max' => 'A foto de perfil não pode ser maior que 5MB.',
            'medical_certificate.max' => 'O atestado médico não pode ser maior que 5MB.',
        ]);

        $athlete->fill($request->except(['profile_picture', 'medical_certificate']));
        
        // Processar campos que podem vir como string mas são arrays
        if ($request->filled('medical_conditions') && is_string($request->medical_conditions)) {
            $athlete->medical_conditions = array_map('trim', explode(',', $request->medical_conditions));
        }
        
        if ($request->filled('allergies') && is_string($request->allergies)) {
            $athlete->allergies = array_map('trim', explode(',', $request->allergies));
        }

        if (!$request->filled('subcategory')) {
            $athlete->subcategory = Athlete::calculateSubcategory($request->birth_date);
        }
        $athlete->profile_completion = $athlete->getProfileCompletionPercentage();
        $athlete->save();

        // Atualizar senha do usuário se fornecida
        if ($request->filled('password') && $athlete->user) {
            $athlete->user->update([
                'password' => \Illuminate\Support\Facades\Hash::make($request->password)
            ]);
        }

        // Handle profile picture if present
        if ($request->hasFile('profile_picture')) {
            $oldPath = $athlete->getRawOriginal('profile_picture_url');
            if ($oldPath && !filter_var($oldPath, FILTER_VALIDATE_URL)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
            }
            
            $path = $request->file('profile_picture')->store('profiles', 'public');
            $athlete->profile_picture_url = $path;
            $athlete->save();
        }

        // Handle medical certificate upload
        if ($request->hasFile('medical_certificate')) {
            // Delete old certificate
            if ($athlete->medical_certificate_path) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($athlete->medical_certificate_path);
            }
            
            $path = $request->file('medical_certificate')->store('medical_certificates', 'public');
            $athlete->update(['medical_certificate_path' => $path]);
        }

        return redirect()->route('admin.athletes.show', $athlete)
            ->with('success', 'Atleta atualizado com sucesso!');
    }

    /**
     * Update the documents for the specified athlete.
     */
    public function updateDocuments(Request $request, Athlete $athlete)
    {
        $request->validate([
            'athlete_document' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:5120',
            'residence_proof' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:5120',
            'guardian_document' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:5120',
            'medical_certificate' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:5120',
        ], [
            'max' => 'O arquivo enviado não pode ser maior que 5MB.',
        ]);

        $updates = [];

        $documentTypes = [
            'athlete_document' => 'athlete_document_path',
            'residence_proof' => 'residence_proof_path',
            'guardian_document' => 'guardian_document_path',
            'medical_certificate' => 'medical_certificate_path',
        ];

        foreach ($documentTypes as $inputName => $dbColumn) {
            if ($request->hasFile($inputName)) {
                if ($athlete->$dbColumn) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($athlete->$dbColumn);
                }
                
                $path = $request->file($inputName)->store('athlete_documents', 'public');
                $updates[$dbColumn] = $path;
            }
        }

        if (!empty($updates)) {
            $athlete->update($updates);
        }

        return redirect()->route('admin.athletes.show', $athlete)->withFragment('documents')
            ->with('success', 'Documentos atualizados com sucesso!');
    }

    /**
     * Remove the specified athlete.
     */
    public function destroy(Athlete $athlete)
    {
        if (auth()->user()->role === 'coach') {
            abort(403, 'Acesso negado. Treinadores não podem excluir atletas.');
        }

        // Delete profile picture
        if ($athlete->profile_picture_url) {
            Storage::disk('public')->delete($athlete->profile_picture_url);
        }

        $athlete->delete();

        return redirect()->route('admin.athletes.index')
            ->with('success', 'Atleta removido com sucesso!');
    }

    /**
     * Toggle athlete active status.
     */
    public function toggleStatus(Athlete $athlete)
    {
        $athlete->update(['is_active' => !$athlete->is_active]);
        
        // Se houver usuário vinculado, sincroniza o status
        if ($athlete->user) {
            $athlete->user->update(['is_active' => $athlete->is_active]);
        }

        $status = $athlete->is_active ? 'ativado' : 'desativado';
        
        return redirect()->back()
            ->with('success', "Atleta {$status} com sucesso!");
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
     * @param AIService $aiService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function generateAiPlan(Request $request, Athlete $athlete, AIService $aiService)
    {
        Log::info('AthleteController: Iniciando geração de plano IA', [
            'athlete_id' => $athlete->id,
            'type' => $request->type,
            'goal' => $request->goal,
            'request_id' => $request->request_id
        ]);
        
        $request->validate([
            'type' => 'required|string|in:workout_plan,meal_plan',
            'goal' => 'required|string|max:255',
            'duration' => 'nullable|integer',
            'frequency' => 'nullable|string',
            'notifications' => 'nullable|array',
            'coach_instructions' => 'nullable|string|max:1000',
            'request_id' => 'nullable|exists:ai_generated_content,id',
        ]);

        try {
            $coachInstructions = $request->coach_instructions;
            $aiContent = null;

            if ($request->type === 'workout_plan') {
                $aiContent = $aiService->generateWorkoutPlan($athlete, $coachInstructions);
            } else {
                $aiContent = $aiService->generateMealPlan($athlete, $coachInstructions);
            }

            if ($aiContent) {
                // Se veio de uma solicitação pendente, vinculamos os dados e removemos a solicitação antiga ou atualizamos ela
                if ($request->request_id) {
                    $pendingRequest = AiGeneratedContent::find($request->request_id);
                    if ($pendingRequest) {
                        // Opcional: Transferir dados da solicitação original para o novo plano
                        $aiContent->update([
                            'goal' => $pendingRequest->goal ?? $request->goal,
                            'coach_instructions' => $coachInstructions,
                            'status' => 'waiting_acceptance' // Alterado de active para aguardando aceite
                        ]);
                        
                        // Arquivar ou deletar a solicitação pendente
                        $pendingRequest->delete(); 
                    }
                } else {
                    $aiContent->update(['status' => 'waiting_acceptance']);
                }
                // Se o plano anterior estava ativo, arquivamos ele
                AiGeneratedContent::where('athlete_id', $athlete->id)
                    ->where('type', $request->type)
                    ->where('status', 'active')
                    ->where('id', '!=', $aiContent->id)
                    ->update(['status' => 'archived']);

                // Extrair duração e frequência do conteúdo da IA (se disponível)
                $content = $aiContent->content;
                $duration = $content['duration_days'] ?? $request->duration ?? 30;
                $frequency = $content['frequency_label'] ?? $request->frequency ?? '3x por semana';
                $notifications = $request->notifications ?? $content['notification_suggestions'] ?? ['08:00', '16:00'];

                $aiContent->update([
                    'status' => 'active',
                    'goal' => $request->goal,
                    'start_date' => now(),
                    'end_date' => now()->addDays($duration),
                    'frequency' => $frequency,
                    'notification_settings' => array_filter($notifications),
                ]);
            }

            return redirect()->back()
                ->with('success', 'Plano gerado pela IA com sucesso e já está ativo!')
                ->with('active_tab', 'ai-plans');

        } catch (\Exception $e) {
            Log::error('AthleteController: Erro ao gerar plano IA', [
                'athlete_id' => $athlete->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->with('error', 'Erro ao gerar plano: ' . $e->getMessage())
                ->with('active_tab', 'ai-plans');
        }
    }


    /**
     * Get AI Plans for athlete (JSON).
     */
    public function getAiPlans(Athlete $athlete)
    {
        $plans = $athlete->aiGeneratedContent()
            ->orderBy('generated_at', 'desc')
            ->get()
            ->map(function($plan) {
                return [
                    'id' => $plan->id,
                    'type' => $plan->type,
                    'type_label' => $plan->type === 'workout_plan' ? 'Treino' : 'Dieta',
                    'title' => $plan->title,
                    'status' => $plan->status,
                    'goal' => $plan->goal,
                    'generated_at' => $plan->generated_at->format('d/m/Y H:i'),
                    'is_favorite' => $plan->is_favorite,
                    'content_preview' => $plan->summary,
                    'notification_settings' => $plan->notification_settings,
                ];
            });

        return response()->json([
            'success' => true,
            'plans' => $plans
        ]);
    }

    /**
     * Show full details of an AI Plan.
     */
    public function showAiPlan(Athlete $athlete, $planId)
    {
        $plan = AiGeneratedContent::where('athlete_id', $athlete->id)
            ->findOrFail($planId);

        return view('athletes.ai-plan-show', compact('athlete', 'plan'));
    }

    /**
     * Toggle the suspension status of an AI Plan.
     */
    public function toggleSuspendPlan(Athlete $athlete, $planId)
    {
        $plan = AiGeneratedContent::where('athlete_id', $athlete->id)
            ->findOrFail($planId);

        $plan->status = $plan->status === 'active' ? 'suspended' : 'active';
        $plan->save();

        return response()->json([
            'success' => true,
            'message' => 'Status do plano atualizado com sucesso.',
            'status' => $plan->status
        ]);
    }

    /**
     * Delete an AI Plan.
     */
    public function deletePlan(Athlete $athlete, $planId)
    {
        $plan = AiGeneratedContent::where('athlete_id', $athlete->id)
            ->findOrFail($planId);

        $plan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Plano excluído com sucesso.'
        ]);
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
        $period = $request->get('period', '6months');
        $metric = $request->get('metric', 'all');
        
        $startDate = match($period) {
            '1month' => now()->subMonth(),
            '3months' => now()->subMonths(3),
            '6months' => now()->subMonths(6),
            '1year' => now()->subYear(),
            default => now()->subMonths(6),
        };

        // Athlete's own records
        $performanceRecords = PerformanceRecord::where('athlete_id', $athlete->id)
            ->where('recorded_at', '>=', $startDate)
            ->orderBy('recorded_at', 'asc')
            ->get();

        // Category (Team) Average
        $categoryAvgQuery = PerformanceRecord::whereHas('athlete', function($q) use ($athlete) {
                $q->where('team_id', $athlete->team_id);
            })
            ->where('recorded_at', '>=', $startDate);

        if ($metric && $metric !== 'all') {
            $categoryAvgQuery->where('metric', $metric);
        }

        $categoryRecords = $categoryAvgQuery->get();

        // Group Athlete Data
        $data = [];
        foreach ($performanceRecords as $record) {
            if (!isset($data[$record->metric])) {
                $data[$record->metric] = [
                    'athlete' => [],
                    'category_avg' => []
                ];
            }
            
            $data[$record->metric]['athlete'][] = [
                'date' => $record->recorded_at->format('Y-m-d'),
                'value' => (float) $record->value,
            ];
        }

        // Group Category Average
        $categoryStats = [];
        foreach ($categoryRecords as $record) {
            $date = $record->recorded_at->format('Y-m-d');
            if (!isset($categoryStats[$record->metric][$date])) {
                $categoryStats[$record->metric][$date] = ['sum' => 0, 'count' => 0];
            }
            $categoryStats[$record->metric][$date]['sum'] += (float) $record->value;
            $categoryStats[$record->metric][$date]['count']++;
        }

        foreach ($categoryStats as $metricKey => $dates) {
            if (isset($data[$metricKey])) {
                foreach ($dates as $date => $stats) {
                    $data[$metricKey]['category_avg'][] = [
                        'date' => $date,
                        'value' => round($stats['sum'] / $stats['count'], 2)
                    ];
                }
                
                // Sort by date
                usort($data[$metricKey]['category_avg'], function($a, $b) {
                    return strcmp($a['date'], $b['date']);
                });
            }
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'period' => $period,
        ]);
    }

    /**
     * Store professional athlete evaluation.
     */
    public function evaluate(Request $request, Athlete $athlete)
    {
        $request->validate([
            'metrics' => 'required|array',
            'metrics.*' => 'nullable', // Permitir campos vazios
            'notes' => 'nullable|string|max:1000',
            'recorded_at' => 'required|date',
        ]);

        try {
            $tenantId = tenant('id');
            $tenantName = tenant('name');

            Log::info('AthleteController@evaluate: Iniciando salvamento', [
                'athlete_id' => $athlete->id,
                'metrics_count' => count($request->metrics),
                'tenant_id' => $tenantId
            ]);

            foreach ($request->metrics as $metric => $value) {
                // Se o valor estiver vazio, tentamos buscar o último valor registrado para esta métrica
                if ($value === null || $value === '') {
                    $lastRecord = PerformanceRecord::where('athlete_id', $athlete->id)
                        ->where('metric', $metric)
                        ->orderBy('recorded_at', 'desc')
                        ->first();
                    
                    if ($lastRecord) {
                        $value = $lastRecord->value;
                    } else {
                        // Se não houver valor anterior e o campo estiver vazio, pulamos
                        continue;
                    }
                }

                $record = PerformanceRecord::create([
                    'athlete_id' => $athlete->id,
                    'metric' => $metric,
                    'value' => $value,
                    'notes' => $request->notes,
                    'recorded_by' => auth()->id(),
                    'recorded_at' => $request->recorded_at,
                    'tenant_id' => $tenantId,
                    'tenant_name' => $tenantName,
                ]);

                if (!$record) {
                    Log::error('AthleteController@evaluate: Falha ao criar registro', [
                        'athlete_id' => $athlete->id,
                        'metric' => $metric
                    ]);
                }

                // Sincroniza com o perfil do atleta se for Peso ou Altura
                if ($metric === 'Peso' && $value) {
                    $athlete->update(['weight' => $value]);
                }
                if ($metric === 'Altura' && $value) {
                    $athlete->update(['height' => $value]);
                }
            }

            Log::info('AthleteController@evaluate: Sucesso');

            return redirect()->route('admin.athletes.show', $athlete)
                ->with('success', 'Avaliação registrada com sucesso!');

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao registrar avaliação: ' . $e->getMessage());
        }
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
        $orders = Order::where(function($q) use ($athlete) {
                $q->where('athlete_id', $athlete->id);
                
                if ($athlete->user_id) {
                    $q->orWhere('user_id', $athlete->user_id);
                }
                
                // Inclui pedidos do responsável se houver
                if ($athlete->guardian_email) {
                    $guardian = User::where('email', $athlete->guardian_email)->first();
                    if ($guardian) {
                        $q->orWhere('user_id', $guardian->id);
                    }
                }
            })
            ->with(['orderItems.product'])
            ->orderBy('created_at', 'desc')
            ->get();

        $history = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'date' => $order->created_at->format('d/m/Y'),
                'amount' => (float) $order->total_amount,
                'status' => $order->status,
                'status_label' => $order->status_label ?? ucfirst($order->status),
                'items' => $order->orderItems->map(function ($item) {
                    return [
                        'product_name' => $item->product->name ?? 'Produto removido',
                        'quantity' => $item->quantity,
                        'price' => (float) $item->price,
                        'total' => (float) $item->total,
                    ];
                }),
            ];
        });

        $totalPaid = $orders->where('status', 'paid')->sum('total_amount');
        $totalPending = $orders->whereIn('status', ['pending', 'processing'])->sum('total_amount');

        return response()->json([
            'success' => true,
            'summary' => [
                'total_paid' => (float) $totalPaid,
                'total_pending' => (float) $totalPending,
                'total_orders' => $orders->count(),
            ],
            'orders' => $history,
        ]);
    }

    /**
     * Update an AI generated plan manually.
     */
    public function updateAiPlan(Request $request, Athlete $athlete, $planId)
    {
        $plan = AiGeneratedContent::where('athlete_id', $athlete->id)->findOrFail($planId);
        
        $request->validate([
            'content' => 'required|array',
            'goal' => 'nullable|string|max:255',
            'duration' => 'nullable|string|max:255',
            'frequency' => 'nullable|string|max:255',
            'notifications' => 'nullable|array',
        ]);

        // Merge updated title/description into content JSON
        $content = $plan->content;
        $newContent = array_merge($content, $request->content);

        $plan->update([
            'content' => $newContent,
            'goal' => $request->goal,
            'duration' => $request->duration,
            'frequency' => $request->frequency,
            'notification_settings' => $request->notifications,
            'last_edited_by' => auth()->id(),
            'edited_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Plano atualizado com sucesso!',
            'plan' => $plan->load('editor')
        ]);
    }

    /**
     * Toggle suspend status of an AI generated plan.
     */
    public function toggleSuspendAiPlan(Request $request, Athlete $athlete, $planId)
    {
        $plan = AiGeneratedContent::where('athlete_id', $athlete->id)->findOrFail($planId);
        
        $newStatus = $plan->status === 'suspended' ? 'active' : 'suspended';
        $plan->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => $newStatus === 'suspended' ? 'Plano suspenso com sucesso!' : 'Plano reativado com sucesso!',
            'status' => $newStatus
        ]);
    }

    /**
     * Delete an AI generated plan.
     */
    public function deleteAiPlan(Request $request, Athlete $athlete, $planId)
    {
        $plan = AiGeneratedContent::where('athlete_id', $athlete->id)->findOrFail($planId);
        $plan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Plano excluído com sucesso!'
        ]);
    }
    /**
     * Show nutrition comparison dashboard for athlete.
     */
    public function nutritionComparison(Athlete $athlete)
    {
        $athlete->load(['aiGeneratedContent' => function($q) {
            $q->where('type', 'meal_plan')->where('status', 'active');
        }]);

        $activePlan = $athlete->aiGeneratedContent->first();
        $targetNutrients = [
            'calories' => 0,
            'protein' => 0,
            'carbs' => 0,
            'fat' => 0
        ];

        if ($activePlan && isset($activePlan->content['total'])) {
            $targetNutrients = [
                'calories' => $activePlan->content['total']['calories'] ?? 0,
                'protein' => $activePlan->content['total']['protein'] ?? 0,
                'carbs' => $activePlan->content['total']['carbs'] ?? 0,
                'fat' => $activePlan->content['total']['fat'] ?? 0
            ];
        }

        // Get last 7 days of logs
        $logs = \App\Models\AthleteMealLog::where('athlete_id', $athlete->id)
            ->where('consumed_at', '>=', now()->subDays(7))
            ->orderBy('consumed_at', 'desc')
            ->get();

        $dailyTotals = $logs->groupBy(function($log) {
            return $log->consumed_at->format('Y-m-d');
        })->map(function($dayLogs) {
            return [
                'calories' => $dayLogs->sum('calories'),
                'protein' => $dayLogs->sum('proteins'),
                'carbs' => $dayLogs->sum('carbs'),
                'fat' => $dayLogs->sum('fats'),
                'count' => $dayLogs->count()
            ];
        });

        return view('athletes.nutrition-comparison', compact('athlete', 'activePlan', 'targetNutrients', 'dailyTotals', 'logs'));
    }
}
