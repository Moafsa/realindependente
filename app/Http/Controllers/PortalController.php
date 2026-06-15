<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\PerformanceRecord;
use App\Models\Order;
use App\Models\User;
use App\Models\AiGeneratedContent;
use App\Models\Training;
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
            $user = Auth::user();
            $athlete = $user->athlete;
            
            // Se for admin ou coach e não tiver atleta, pegamos o primeiro atleta do clube como preview
            if (!$athlete && ($user->isAdmin() || $user->isCoach())) {
                $athlete = Athlete::first();
            }
            
            if (!$athlete) {
                return redirect()->route('login')
                    ->with('error', 'Usuário não possui atleta associado ou não há atletas cadastrados.');
            }

            $athlete->load(['team', 'branch', 'user', 'performanceRecords' => function ($query) {
                $query->latest()->take(10);
            }]);

            // Get latest training and diet plans separately
            $latestWorkout = AiGeneratedContent::where('athlete_id', $athlete->id)
                ->where('type', 'workout_plan')
                ->where('status', 'active')
                ->latest()
                ->first();
                
            $latestDiet = AiGeneratedContent::where('athlete_id', $athlete->id)
                ->where('type', 'meal_plan')
                ->where('status', 'active')
                ->latest()
                ->first();
                
            $recentAiContent = collect();
            if ($latestWorkout) $recentAiContent->push($latestWorkout);
            if ($latestDiet) $recentAiContent->push($latestDiet);

            $skillsData = PerformanceRecord::where('athlete_id', $athlete->id)
                ->orderBy('recorded_at', 'desc')
                ->get()
                ->groupBy(function($item) {
                    return mb_convert_case($item->metric, MB_CASE_TITLE, 'UTF-8');
                })
                ->map(function ($records) {
                    return (int) round($records->avg('value'));
                })
                ->take(6);

            $skills = [
                'labels' => $skillsData->keys()->toArray(),
                'values' => $skillsData->values()->toArray()
            ];

            // Real AI Insight Logic
            $recentRecords = $athlete->performanceRecords()->latest()->take(15)->get();
            $recentAvg = $recentRecords->avg('value') ?? 0;
            $bestMetric = $skillsData->sortDesc()->keys()->first() ?? 'técnica';
            
            $aiInsight = "Analisando seus últimos registros, identificamos um nível de " . round($recentAvg) . "% de aproveitamento geral. ";
            if ($recentAvg > 80) {
                $aiInsight .= "Destaque positivo para sua capacidade em {$bestMetric}. Mantenha o foco em treinos de explosão.";
            } elseif ($recentAvg > 60) {
                $aiInsight .= "Sua evolução em {$bestMetric} é visível. Recomendamos intensificar fundamentos técnicos para o próximo jogo.";
            } else {
                $aiInsight .= "Detectamos uma oscilação na consistência. Foco total em descanso e nutrição para evitar lesões.";
            }

            // Check for active subscription
            $activeSubscription = $athlete->orders()
                ->where('status', 'paid')
                ->whereHas('orderItems.product', function ($query) {
                    $query->where('type', 'subscription');
                })
                ->exists();

            // Get pending subscription order if any
            $pendingOrder = \App\Models\Order::where(function ($query) use ($athlete) {
                $query->where('athlete_id', $athlete->id)
                    ->orWhere(function ($q) use ($athlete) {
                        $q->where('user_id', $athlete->user_id)
                          ->whereNull('athlete_id');
                    });
            })
            ->where('status', 'pending')
            ->whereHas('orderItems.product', function ($query) {
                $query->where('type', 'subscription');
            })
            ->latest()
            ->first();

            $teamId = $athlete->team_id;
            $upcomingTrainings = Training::where(function($q) use ($teamId) {
                    if ($teamId) {
                        $q->where('team_id', $teamId)->orWhereNull('team_id');
                    } else {
                        $q->whereNotNull('id'); 
                    }
                })
                ->where('date', '>=', now()->format('Y-m-d'))
                ->orderBy('date', 'asc')
                ->orderBy('time', 'asc')
                ->take(5)
                ->get();
                
            $soonestTraining = $upcomingTrainings->first();

            $stats = [
                'total_plans' => AiGeneratedContent::where('athlete_id', $athlete->id)->count(),
                'active_plans' => AiGeneratedContent::where('athlete_id', $athlete->id)
                    ->where('status', 'active')
                    ->count(),
                'pending_plans' => AiGeneratedContent::where('athlete_id', $athlete->id)
                    ->where('status', 'pending')
                    ->count(),
                'total_trainings' => Training::where(function($q) use ($teamId) {
                        if ($teamId) {
                            $q->where('team_id', $teamId)->orWhereNull('team_id');
                        } else {
                            $q->whereNotNull('id');
                        }
                    })->count(),
                'completed_trainings' => Training::where(function($q) use ($teamId) {
                        if ($teamId) {
                            $q->where('team_id', $teamId)->orWhereNull('team_id');
                        } else {
                            $q->whereNotNull('id');
                        }
                    })
                    ->where('date', '<', now()->format('Y-m-d'))
                    ->count(),
                'upcoming_trainings' => Training::where(function($q) use ($teamId) {
                        if ($teamId) {
                            $q->where('team_id', $teamId)->orWhereNull('team_id');
                        } else {
                            $q->whereNotNull('id');
                        }
                    })
                    ->where('date', '>=', now()->format('Y-m-d'))
                    ->count(),
                'performance_score' => $this->calculatePerformanceScore($athlete),
                'performance_change' => $this->calculatePerformanceChange($athlete),
                'achieved_goals' => 0,
                'in_progress_goals' => 0,
            ];

            // Se for coach, carregar o extrato de pagamentos
            $coachPayments = collect();
            $coachBalance = 0;
            if ($user->isCoach()) {
                $query = \App\Models\CashFlow::where('recipient_id', $user->id);
                
                if (request('start_date')) {
                    $query->where('date', '>=', request('start_date'));
                }
                if (request('end_date')) {
                    $query->where('date', '<=', request('end_date'));
                }

                $coachPayments = $query->orderBy('date', 'desc')
                    ->take(50)
                    ->get();
                    
                $coachBalance = \App\Models\CashFlow::where('recipient_id', $user->id)
                    ->where('status', 'completed')
                    ->sum('amount');
            }
            // Busca o token do Mapbox do banco central (superadmin)
            $mapboxSetting = \Illuminate\Support\Facades\DB::connection('pgsql')
                ->table('site_settings')
                ->where('key', 'mapbox_public_token')
                ->first();

            $settings = [
                'mapbox_public_token' => $mapboxSetting?->value ?? '',
            ];

        } catch (\Exception $e) {
            Log::error('PortalController@dashboard Error: ' . $e->getMessage());
            return redirect()->route('login')
                ->with('error', 'Erro ao carregar dados do atleta. Por favor, tente novamente.');
        }

        return view('portal.dashboard', compact('athlete', 'recentAiContent', 'skills', 'stats', 'upcomingTrainings', 'soonestTraining', 'activeSubscription', 'pendingOrder', 'aiInsight', 'coachPayments', 'coachBalance', 'settings'));
    }

    /**
     * Show detailed AI plan.
     */
    public function showAiPlan($id)
    {
        try {
            $athlete = Auth::user()->athlete;
            $plan = AiGeneratedContent::where('athlete_id', $athlete->id)->findOrFail($id);

            return view('portal.ai-plans.show', compact('athlete', 'plan'));
        } catch (\Exception $e) {
            return redirect()->route('portal.dashboard')->with('error', 'Plano não encontrado.');
        }
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
            ->get()
            ->avg(function ($record) {
                return (float) $record->value;
            });

        $previous = PerformanceRecord::where('athlete_id', $athlete->id)
            ->where('recorded_at', '>=', now()->subWeeks(2))
            ->where('recorded_at', '<', now()->subWeek())
            ->get()
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
        $user = Auth::user();
        $athlete = $user->athlete;
        
        // Se for admin ou coach e não tiver atleta associado, carregamos o perfil básico do usuário
        if (!$athlete && ($user->isAdmin() || $user->isCoach())) {
            return view('portal.profile', compact('user', 'athlete'));
        }

        if (!$athlete) {
            return redirect()->route('login')
                ->with('error', 'Usuário não possui atleta associado.');
        }

        $athlete->load(['team', 'branch', 'user']);

        return view('portal.profile', compact('athlete', 'user'));
    }

    /**
     * Update athlete profile.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $athlete = $user->athlete;
        
        // Atualização para Admin ou Coach
        if (in_array($user->role, ['admin', 'coach'])) {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'password' => 'nullable|min:6|confirmed',
                'settings' => 'nullable|array',
                'phone' => 'nullable|string|max:255',
                'bio' => 'nullable|string|max:2000',
                'education' => 'nullable|string|max:2000',
                'experience' => 'nullable|string|max:2000',
                'specialties' => 'nullable|string|max:255',
                'certificate_files.*' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:5120',
                'certificate_names.*' => 'nullable|string|max:255',
                'profile_picture' => 'nullable|image|max:5120',
            ]);

            $userData = $request->only(['name', 'email']);
            
            if ($request->hasFile('profile_picture')) {
                $path = $request->file('profile_picture')->store('avatars', 'public');
                $userData['avatar'] = $path;
            }
            
            if ($user->isCoach()) {
                $userData = array_merge($userData, $request->only(['phone', 'bio', 'education', 'experience', 'specialties']));
            }

            $user->update($userData);
            
            if ($request->filled('password')) {
                $user->update(['password' => bcrypt($request->password)]);
            }

            // Handle Certificates for Coach
            if ($user->isCoach()) {
                $certificates = $user->certificates ?? [];
                
                if ($request->has('remove_certificate')) {
                    $indexToRemove = $request->remove_certificate;
                    if (isset($certificates[$indexToRemove])) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($certificates[$indexToRemove]['path']);
                        unset($certificates[$indexToRemove]);
                        $certificates = array_values($certificates);
                    }
                }

                if ($request->hasFile('certificate_files')) {
                    foreach ($request->file('certificate_files') as $key => $file) {
                        if ($file && $file->isValid()) {
                            $name = $request->certificate_names[$key] ?? $file->getClientOriginalName();
                            $path = $file->storeOptimized('coach_certificates', 'public');
                            $certificates[] = [
                                'name' => $name,
                                'path' => $path,
                                'date' => now()->format('Y-m-d')
                            ];
                        }
                    }
                }
                $user->update(['certificates' => $certificates]);
            }

            if ($user->isAdmin() && $request->has('settings')) {
                foreach ($request->settings as $key => $value) {
                    \App\Models\SiteSetting::set($key, $value, 'text', null, true);
                }
            }

            return redirect()->route('portal.profile')->with('success', 'Perfil atualizado com sucesso!');
        }

        if (!$athlete) {
            return redirect()->route('login')
                ->with('error', 'Usuário não possui atleta associado.');
        }

        $request->validate([
            'full_name' => 'required|string|max:255',
            'birth_date' => 'required|date|before:today',
            'position' => 'nullable|string|max:255',
            'positions' => 'nullable|array',
            'positions.*' => 'string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'medical_certificate' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:5120',
            'athlete_document' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:5120',
            'residence_proof' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:5120',
            'guardian_document_file' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:5120',
            'bio' => 'nullable|string|max:1000',
            'jersey_number' => 'nullable|string|max:10',
            'height' => 'nullable|numeric|min:0|max:300',
            'weight' => 'nullable|numeric|min:0|max:500',
            'document' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_contact' => 'nullable|string|max:255',
            'guardian_document' => 'nullable|string|max:255',
            'guardian_email' => 'nullable|email|max:255',
            'dominant_limb' => 'nullable|string|in:Destro,Canhoto,Ambidestro',
            'instagram_url' => 'nullable|url|max:255',
            'facebook_url' => 'nullable|url|max:255',
            'tiktok_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            'x_url' => 'nullable|url|max:255',
        ]);

        $athlete->fill($request->except(['profile_picture', 'medical_certificate', 'athlete_document', 'residence_proof', 'guardian_document_file']));

        // Handle profile picture upload
        if ($request->hasFile('profile_picture')) {
            $path = $request->file('profile_picture')->storeOptimized('athletes');
            $athlete->profile_picture_url = $path;
        }

        // Handle medical certificate upload
        if ($request->hasFile('medical_certificate')) {
            $path = $request->file('medical_certificate')->storeOptimized('medical_certificates');
            $athlete->medical_certificate_path = $path;
        }

        // Handle athlete document upload
        if ($request->hasFile('athlete_document')) {
            $path = $request->file('athlete_document')->storeOptimized('athlete_documents');
            $athlete->athlete_document_path = $path;
        }

        // Handle residence proof upload
        if ($request->hasFile('residence_proof')) {
            $path = $request->file('residence_proof')->storeOptimized('residence_proofs');
            $athlete->residence_proof_path = $path;
        }

        // Handle guardian document upload
        if ($request->hasFile('guardian_document_file')) {
            $path = $request->file('guardian_document_file')->storeOptimized('guardian_documents');
            $athlete->guardian_document_path = $path;
        }

        $athlete->save();

        return redirect()->route('portal.profile')
            ->with('success', 'Perfil atualizado com sucesso!');
    }

    public function updateHistory(\Illuminate\Http\Request $request)
    {
        $athlete = \Illuminate\Support\Facades\Auth::user()->athlete;
        
        if (!$athlete) {
            return redirect()->route('login')->with('error', 'Usuário não possui atleta associado.');
        }

        // Handle new history rows
        if ($request->has('new_history') && is_array($request->new_history)) {
            foreach ($request->new_history as $id => $data) {
                if (!empty($data['club_name']) && !empty($data['start_date'])) {
                    $logoUrl = null;
                    if (isset($data['logo']) && $data['logo'] instanceof \Illuminate\Http\UploadedFile) {
                        $logoUrl = $data['logo']->storeOptimized('club_logos');
                    }
                    
                    \App\Models\AthleteHistory::create([
                        'athlete_id' => $athlete->id,
                        'club_name' => $data['club_name'],
                        'club_logo_url' => $logoUrl,
                        'start_date' => $data['start_date'],
                        'end_date' => !empty($data['end_date']) ? $data['end_date'] : null,
                    ]);
                }
            }
        }

        // Handle existing history updates
        if ($request->has('history') && is_array($request->history)) {
            foreach ($request->history as $historyId => $data) {
                $history = \App\Models\AthleteHistory::where('id', $historyId)->where('athlete_id', $athlete->id)->first();
                if ($history) {
                    if (isset($data['logo']) && $data['logo'] instanceof \Illuminate\Http\UploadedFile) {
                        $data['club_logo_url'] = $data['logo']->storeOptimized('club_logos');
                    }
                    unset($data['logo']);
                    if (empty($data['end_date'])) {
                        $data['end_date'] = null;
                    }
                    $history->update($data);
                }
            }
        }

        return redirect()->route('portal.profile')->with('success', 'Histórico de clubes atualizado com sucesso!');
    }

    public function deleteHistory($id)
    {
        $athlete = \Illuminate\Support\Facades\Auth::user()->athlete;
        if (!$athlete) {
            return redirect()->route('login')->with('error', 'Usuário não possui atleta associado.');
        }

        $history = \App\Models\AthleteHistory::where('id', $id)->where('athlete_id', $athlete->id)->first();
        if ($history) {
            $history->delete();
            return redirect()->route('portal.profile')->with('success', 'Histórico removido com sucesso!');
        }

        return redirect()->route('portal.profile')->with('error', 'Histórico não encontrado.');
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
            ->paginate(10);

        // Get performance metrics summary
        $metrics = PerformanceRecord::where('athlete_id', $athlete->id)
            ->selectRaw('metric, COUNT(*) as count, AVG(CAST(value AS DECIMAL)) as avg_value, MAX(recorded_at) as last_recorded')
            ->groupBy('metric')
            ->get();

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

        return view('portal.performance', compact('athlete', 'performanceRecords', 'metrics', 'sparklineData'));
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

        // Nutrition data for comparative dashboard
        $activeMealPlan = AiGeneratedContent::where('athlete_id', $athlete->id)
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

        return view('portal.ai-plans', compact('athlete', 'aiContent', 'activeMealPlan', 'nutritionDailyTotals', 'mealLogs'));
    }

    /**
     * Request new AI plan.
     */
    public function requestPlan(Request $request)
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
            'goal' => 'nullable|string|max:500',
        ]);

        try {
            // Check if there is already a pending request of the same type
            $pending = \App\Models\AiGeneratedContent::where('athlete_id', $athlete->id)
                ->where('type', $request->type)
                ->where('status', 'pending')
                ->first();

            if ($pending) {
                return response()->json([
                    'success' => false,
                    'message' => 'Você já possui uma solicitação pendente para este tipo de plano.'
                ], 400);
            }

            $content = \App\Models\AiGeneratedContent::create([
                'athlete_id' => $athlete->id,
                'type' => $request->type,
                'status' => 'pending',
                'goal' => $request->goal,
                'generated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Solicitação enviada com sucesso! Seu coach será notificado.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao processar solicitação: ' . $e->getMessage()
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
     * Show athlete subscriptions and plans.
     */
    public function subscriptions(Request $request)
    {
        $athlete = Auth::user()->athlete;
        
        if (!$athlete && (Auth::user()->role === 'admin' || Auth::user()->role === 'coach')) {
            $athlete = Athlete::find($request->query('athlete_id'));
        }

        if (!$athlete && Auth::user()->role === 'guardian') {
             $athlete = Athlete::where('guardian_email', Auth::user()->email)->first();
        }

        if (!$athlete) {
            return redirect()->route('portal.dashboard')->with('error', 'Usuário não possui atleta associado.');
        }

        $plan = $athlete->subscriptionPlan;
        $orders = Order::where('athlete_id', $athlete->id)
            ->whereHas('orderItems.product', function ($query) {
                $query->where('type', 'subscription');
            })
            ->latest()
            ->get();
            
        $availablePlans = \App\Models\Product::where('type', 'subscription')
            ->where('is_active', true)
            ->orderBy('price', 'asc')
            ->get();
        
        return view('portal.subscriptions', compact('athlete', 'plan', 'orders', 'availablePlans'));
    }

    /**
     * Show athlete invoices/payments.
     */
    public function invoices(Request $request)
    {
        $athlete = Auth::user()->athlete;
        
        if (!$athlete && (Auth::user()->role === 'admin' || Auth::user()->role === 'coach')) {
            $athlete = Athlete::find($request->query('athlete_id'));
        }

        if (!$athlete && Auth::user()->role === 'guardian') {
             $athlete = Athlete::where('guardian_email', Auth::user()->email)->first();
        }

        if (!$athlete) {
            return redirect()->route('portal.dashboard')->with('error', 'Usuário não possui atleta associado.');
        }

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
            ->paginate(15);

        return view('portal.invoices', compact('athlete', 'orders'));
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

        // Category (Team) Average
        $categoryAvgQuery = PerformanceRecord::whereHas('athlete', function($q) use ($athlete) {
                $q->where('team_id', $athlete->team_id);
            })
            ->where('recorded_at', '>=', $startDate);

        if ($metric && $metric !== 'all') {
            $categoryAvgQuery->where('metric', $metric);
        }

        $categoryRecords = $categoryAvgQuery->get();

        // Agrupar dados do atleta
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

        // Agrupar médias da categoria
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
                
                // Ordenar por data
                usort($data[$metricKey]['category_avg'], function($a, $b) {
                    return strcmp($a['date'], $b['date']);
                });
            }
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'period' => $period,
            'athlete_id' => $athlete->id,
            'team_id' => $athlete->team_id
        ]);
    }

    /**
     * Get recent notifications for the athlete.
     */
    public function getNotifications()
    {
        $user = Auth::user();
        $athlete = $user->athlete;
        
        if (!$athlete && ($user->role === 'admin' || $user->role === 'coach')) {
            $athlete = \App\Models\Athlete::first();
        }

        if (!$athlete) {
            return response()->json(['success' => false, 'message' => 'Athlete not found']);
        }

        // For now, let's return some mock notifications or fetch from a real table if it exists
        // Checking if a Notification model exists or using generic logic
        $notifications = []; // Placeholder

        // Example: check for upcoming trainings as notifications
        $trainings = \App\Models\Training::where('team_id', $athlete->team_id)
            ->where('date', '>=', now()->format('Y-m-d'))
            ->orderBy('date', 'asc')
            ->take(3)
            ->get();

        foreach ($trainings as $training) {
            $notifications[] = [
                'id' => 'training_' . $training->id,
                'title' => 'Próximo Treino',
                'message' => $training->title . ' às ' . $training->time,
                'type' => 'training',
                'created_at' => $training->created_at ? $training->created_at->diffForHumans() : 'Agora'
            ];
        }

        // Add Mural Notices
        $muralNotices = \App\Models\MuralNotice::where('team_id', $athlete->team_id)
            ->orWhereNull('team_id')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        foreach ($muralNotices as $notice) {
            $notifications[] = [
                'id' => 'mural_' . $notice->id,
                'title' => 'Aviso no Mural',
                'message' => "De " . ($notice->sender->name ?? 'Sistema') . ": {$notice->title}",
                'type' => 'mural',
                'url' => route('communication.index') . '?tab=mural',
                'created_at' => $notice->created_at->diffForHumans()
            ];
        }

        // Add unread messages to notifications
        $lastUnreadMsg = \App\Models\Message::where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->latest()
            ->first();

        $unreadMessagesCount = \App\Models\Message::where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->count();
            
        if ($unreadMessagesCount > 0) {
            $msgPreview = $lastUnreadMsg ? $lastUnreadMsg->content : '';
            if (!$msgPreview && $lastUnreadMsg && $lastUnreadMsg->attachment_path) {
                $msgPreview = $lastUnreadMsg->attachment_type === 'image' ? '📸 Enviou uma foto' : '📄 Enviou um documento';
            }

            $notifications[] = [
                'id' => 'messages',
                'title' => 'Novas Mensagens',
                'message' => "Você tem {$unreadMessagesCount} nova(s) mensagem(ns). " . ($msgPreview ? "Última: {$msgPreview}" : ""),
                'type' => 'message',
                'url' => route('communication.index'),
                'created_at' => 'Agora'
            ];
        }

        $lastMuralView = \Illuminate\Support\Facades\Cache::get('user_mural_view_' . Auth::id(), now()->subMonths(3));
        $muralCount = \App\Models\MuralNotice::where(function($q) use ($athlete) {
                $q->where('team_id', $athlete->team_id)->orWhereNull('team_id');
            })
            ->where('created_at', '>', $lastMuralView)
            ->count();

        $totalNotifications = $unreadMessagesCount + $muralCount;

        return response()->json([
            'success' => true,
            'count' => $totalNotifications,
            'totalNotifications' => $totalNotifications,
            'notifications' => $notifications
        ]);
    }

    /**
     * Mark mural as viewed by the athlete.
     */
    public function trainings()
    {
        $user = Auth::user();
        $athlete = $user->athlete;
        
        if (!$athlete && ($user->role === 'admin' || $user->role === 'coach')) {
            $athlete = \App\Models\Athlete::first();
        }

        if (!$athlete) {
            return redirect()->route('portal.dashboard')
                ->with('error', 'Usuário não possui atleta associado.');
        }

        $athlete->load(['team', 'branch']);

        $query = \App\Models\Training::orderBy('date', 'desc');
        if ($athlete->team_id) {
            $query->where('team_id', $athlete->team_id);
        }

        $trainings = $query->paginate(20);

        // Busca o token do Mapbox do banco central (superadmin)
        $mapboxSetting = \Illuminate\Support\Facades\DB::connection('pgsql')
            ->table('site_settings')
            ->where('key', 'mapbox_public_token')
            ->first();

        $settings = [
            'mapbox_public_token' => $mapboxSetting?->value ?? '',
        ];

        return view('portal.trainings', compact('athlete', 'trainings', 'settings'));
    }
    /**
     * Accept an AI generated plan.
     */
    public function acceptAiPlan(Request $request, $planId)
    {
        try {
            $user = Auth::user();
            $athlete = $user->athlete;
            
            if (!$athlete) {
                return response()->json([
                    'success' => false,
                    'message' => 'Atleta não encontrado.'
                ], 404);
            }

            $plan = AiGeneratedContent::where('athlete_id', $athlete->id)->findOrFail($planId);
            
            // Mark plan as active and set acceptance date
            $plan->update([
                'status' => 'active',
                'accepted_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Plano aceito com sucesso! Agora você pode começar seu protocolo.',
                'accepted_at' => $plan->accepted_at->format('d/m/Y H:i')
            ]);

        } catch (\Exception $e) {
            Log::error('PortalController@acceptAiPlan: Erro ao aceitar plano', [
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao aceitar o plano.'
            ], 500);
        }
    }

    /**
     * Toggle favorite status of a plan.
     */
    public function toggleFavorite(Request $request, $planId)
    {
        try {
            $user = Auth::user();
            $athlete = $user->athlete;
            
            if (!$athlete) {
                return response()->json(['success' => false, 'message' => 'Atleta não encontrado.'], 404);
            }

            $plan = AiGeneratedContent::where('athlete_id', $athlete->id)->findOrFail($planId);
            $plan->update(['is_favorite' => !$plan->is_favorite]);

            return response()->json([
                'success' => true,
                'is_favorite' => $plan->is_favorite,
                'message' => $plan->is_favorite ? 'Plano favoritado!' : 'Removido dos favoritos.'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erro ao processar.'], 500);
        }
    }

    /**
     * Get AI plan content in JSON.
     */
    public function getContent(Request $request, $planId)
    {
        try {
            $user = Auth::user();
            $athlete = $user->athlete;
            
            if (!$athlete) {
                return response()->json(['success' => false, 'message' => 'Atleta não encontrado.'], 404);
            }

            $plan = AiGeneratedContent::where('athlete_id', $athlete->id)->findOrFail($planId);

            return response()->json([
                'success' => true,
                'id' => $plan->id,
                'title' => $plan->title,
                'type' => $plan->type,
                'status' => $plan->status,
                'goal' => $plan->goal,
                'duration' => $plan->duration ?? '30 dias',
                'frequency' => $plan->frequency ?? 'N/A',
                'notification_settings' => $plan->notification_settings ?? [],
                'generated_at' => $plan->generated_at,
                'content' => $plan->content
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erro ao buscar conteúdo.'], 500);
        }
    }

    /**
     * Log a meal by analyzing a photo.
     */
    public function generateTeamAiPlan(Request $request, Team $team, \App\Services\AIService $aiService)
    {
        $user = auth()->user();
        if ($user->role === 'coach' && $team->coach_id !== $user->id) {
            abort(403, 'Acesso negado. Você não tem permissão para gerar planos para esta equipe.');
        }

        $request->validate([
            'photo' => 'required|image|max:5120', // Max 5MB
        ]);

        try {
            $user = Auth::user();
            $athlete = $user->athlete;
            
            if (!$athlete) {
                return response()->json(['success' => false, 'message' => 'Atleta não encontrado.'], 404);
            }

            $photo = $request->file('photo');
            $path = $photo->storeOptimized('meals/' . $athlete->id, 'public');
            
            // Get base64 for AI
            $imageBase64 = base64_encode(file_get_contents($photo->getRealPath()));
            
            // Analyze with AI
            $analysis = $this->aiService->analyzeMealPhoto($imageBase64);
            
            // Save log
            $mealLog = \App\Models\AthleteMealLog::create([
                'athlete_id' => $athlete->id,
                'photo_path' => $path,
                'ai_analysis' => $analysis,
                'calories' => $analysis['total']['calories'] ?? 0,
                'proteins' => $analysis['total']['protein'] ?? 0,
                'carbs' => $analysis['total']['carbs'] ?? 0,
                'fats' => $analysis['total']['fat'] ?? 0,
                'consumed_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Refeição analisada e registrada com sucesso!',
                'data' => $analysis,
                'log_id' => $mealLog->id
            ]);

        } catch (\Exception $e) {
            Log::error('PortalController@logMealPhoto: Erro', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao analisar a foto: ' . $e->getMessage()
            ], 500);
        }
    }
}
