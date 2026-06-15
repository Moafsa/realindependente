<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\Team;
use App\Models\Branch;
use App\Models\Order;
use App\Models\PerformanceRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;
use App\Models\Plan;

class DashboardController extends Controller
{
    /**
     * Show the dashboard.
     */
    public function index()
    {
        $host = request()->getHost();
        
        // Log para depuração (visível no laravel.log se necessário)
        // \Log::info("Dashboard access", ['host' => $host, 'tenant' => tenant('id')]);

        // 1. Se o tenant já foi inicializado pelo middleware, usamos ele obrigatoriamente
        if (tenant()) {
            return $this->tenantDashboard();
        }

        // 2. Se estivermos em um subdomínio (qualquer coisa que não seja exatamente localhost ou o domínio central configurado)
        // Tentamos forçar a inicialização.
        $centralDomains = config('tenancy.central_domains', ['localhost', 'Nexts.test']);
        
        if (!in_array($host, $centralDomains)) {
            $domain = \Stancl\Tenancy\Database\Models\Domain::where('domain', $host)->first();
            if ($domain) {
                tenancy()->initialize($domain->tenant_id);
                return $this->tenantDashboard();
            }
        }

        // 3. Se chegamos aqui, ou estamos no domínio central ou o subdomínio não existe
        if (auth()->check()) {
            $user = auth()->user();

            // Se for Super Admin, mostra o Dashboard Global
            if ($user->isSuperAdmin() && !tenant()) {
                return $this->superAdminDashboard();
            }

            // Se for um admin de clube (não super admin) tentando acessar o global, 
            // redirecionamos para o tenant dele se existir.
            $tenant = \App\Models\Tenant::where('email', $user->email)->first();
            if ($tenant) {
                return redirect()->to($tenant->url . '/dashboard');
            }

            // Caso contrário, bloqueia acesso
            auth()->logout();
            return redirect()->route('login')->with('error', 'Você não tem permissão para acessar o Painel Global.');
        }

        return redirect()->route('login');
    }

    /**
     * Lógica para o dashboard do Tenant (Clube)
     */
    protected function tenantDashboard()
    {
        $tenant = tenant();

        // Se for atleta, redirecionar para o portal
        if (auth()->check() && (auth()->user()->role === 'athlete' || auth()->user()->role === 'guardian')) {
            return redirect()->route('portal.dashboard');
        }

        try {
            // Períodos para comparação
            $thisMonth = now()->startOfMonth();
            $lastMonth = now()->subMonth()->startOfMonth();

            // Filtro por Coach (Se logado)
            $user = auth()->user();
            $isCoach = $user->role === 'coach';
            $coachTeams = $isCoach ? Team::where('coach_id', $user->id)->pluck('id')->toArray() : [];

            // Atletas e Tendência
            $athleteQuery = Athlete::query();
            if ($isCoach) {
                $athleteQuery->whereIn('team_id', $coachTeams);
            }
            $totalAthletes = $athleteQuery->count();
            
            $lastMonthAthletesQuery = Athlete::where('created_at', '<', $thisMonth);
            if ($isCoach) {
                $lastMonthAthletesQuery->whereIn('team_id', $coachTeams);
            }
            $lastMonthAthletes = $lastMonthAthletesQuery->count();
            
            $athleteTrend = $lastMonthAthletes > 0 ? (($totalAthletes - $lastMonthAthletes) / $lastMonthAthletes) * 100 : 0;

            // Atletas Ativos
            $activeAthletesQuery = Athlete::where('is_active', true);
            if ($isCoach) {
                $activeAthletesQuery->whereIn('team_id', $coachTeams);
            }
            $activeAthletes = $activeAthletesQuery->count();

            // Receita e Tendência (Apenas para Admin)
            $totalRevenue = 0;
            $thisMonthRevenue = 0;
            $revenueTrend = 0;

            if (!$isCoach) {
                $totalRevenue = Order::where('status', 'paid')->sum('total_amount');
                $thisMonthRevenue = Order::where('status', 'paid')
                    ->where('created_at', '>=', $thisMonth)
                    ->sum('total_amount');
                $lastMonthRevenue = Order::where('status', 'paid')
                    ->whereBetween('created_at', [$lastMonth, $thisMonth->copy()->subSecond()])
                    ->sum('total_amount');
                
                $revenueTrend = $lastMonthRevenue > 0 ? (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 : ($thisMonthRevenue > 0 ? 100 : 0);
            }

            $pendingAiRequestsQuery = \App\Models\AiGeneratedContent::where('status', 'pending');
            if ($isCoach) {
                $pendingAiRequestsQuery->whereHas('athlete', function($q) use ($coachTeams) {
                    $q->whereIn('team_id', $coachTeams);
                });
            }
            $pendingAiRequests = $pendingAiRequestsQuery->count();

            $stats = [
                'total_athletes' => $totalAthletes,
                'athlete_trend' => round($athleteTrend, 1),
                'active_athletes' => $activeAthletes,
                'total_teams' => Athlete::whereNotNull('subcategory')->distinct('subcategory')->count('subcategory'),
                'total_branches' => $isCoach ? Branch::whereHas('teams', function($q) use ($user) { $q->where('coach_id', $user->id); })->count() : Branch::count(),
                'total_orders' => !$isCoach ? Order::count() : 0,
                'total_revenue' => $totalRevenue,
                'this_month_revenue' => $thisMonthRevenue,
                'revenue_trend' => round($revenueTrend, 1),
                'pending_ai_requests' => $pendingAiRequests,
            ];

            $recent_ai_requests_query = \App\Models\AiGeneratedContent::with('athlete')
                ->where('status', 'pending');
            if ($isCoach) {
                $recent_ai_requests_query->whereHas('athlete', function($q) use ($coachTeams) {
                    $q->whereIn('team_id', $coachTeams);
                });
            }
            $recent_ai_requests = $recent_ai_requests_query->latest()->take(5)->get();

            // Get recent athletes
            $recent_athletes_query = Athlete::with(['team', 'branch']);
            if ($isCoach) {
                $recent_athletes_query->whereIn('team_id', $coachTeams);
            }
            $recent_athletes = $recent_athletes_query->latest()->take(5)->get();

            // Get top performing athletes snapshot
            $top_athletes_query = PerformanceRecord::with('athlete.team')
                ->where('metric', '!=', 'Biotipo');
            if ($isCoach) {
                $top_athletes_query->whereHas('athlete', function($q) use ($coachTeams) {
                    $q->whereIn('team_id', $coachTeams);
                });
            }
            $top_athletes = $top_athletes_query->select('athlete_id', DB::raw('AVG(CAST(value AS NUMERIC)) as avg_score'))
                ->groupBy('athlete_id')
                ->orderBy('avg_score', 'desc')
                ->take(5)
                ->get();

            // Get team statistics
            $team_stats_query = Team::withCount('athletes');
            if ($isCoach) {
                $team_stats_query->where('coach_id', $user->id);
            }
            $team_stats = $team_stats_query->orderBy('athletes_count', 'desc')
                ->take(5)
                ->get();

            // Get performance trends (last 6 months)
            $performance_trends = PerformanceRecord::select(
                    DB::raw('DATE_TRUNC(\'month\', recorded_at) as month'),
                    DB::raw('AVG(CAST(value AS NUMERIC)) as avg_score')
                )
                ->where('metric', '!=', 'Biotipo')
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

            $revenue_trends = Order::select(
                    DB::raw('DATE_TRUNC(\'month\', created_at) as month'),
                    DB::raw('SUM(total_amount) as revenue')
                )
                ->where('status', 'paid')
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy('month')
                ->orderBy('month')
                ->get();

            return view('dashboard.index', compact(
                'stats',
                'recent_athletes',
                'top_athletes',
                'team_stats',
                'performance_trends',
                'birthday_athletes',
                'recent_orders',
                'revenue_trends',
                'recent_ai_requests',
                'tenant'
            ));
        } catch (\Exception $e) {
            \Log::error('Dashboard Error: ' . $e->getMessage());
            
            return view('dashboard.index', [
                'stats' => [
                    'total_athletes' => 0,
                    'athlete_trend' => 0,
                    'active_athletes' => 0,
                    'total_teams' => 0,
                    'total_branches' => 0,
                    'total_orders' => 0,
                    'total_revenue' => 0,
                    'this_month_revenue' => 0,
                    'revenue_trend' => 0,
                    'pending_ai_requests' => 0,
                ],
                'recent_athletes' => collect([]),
                'top_athletes' => collect([]),
                'team_stats' => collect([]),
                'performance_trends' => collect([]),
                'birthday_athletes' => collect([]),
                'recent_orders' => collect([]),
                'revenue_trends' => collect([]),
                'recent_ai_requests' => collect([]),
                'tenant' => $tenant
            ]);
        }
    }

    /**
     * Show the Super Admin global dashboard.
     */
    protected function superAdminDashboard()
    {
        $stats = [
            'total_tenants' => Tenant::count(),
            'active_tenants' => Tenant::where('status', 'active')->count(),
            'pending_tenants' => Tenant::where('status', 'pending')->count(),
            'total_plans' => Plan::count(),
            'revenue_monthly' => Tenant::join('plans', 'tenants.plan_id', '=', 'plans.id')
                ->where('tenants.status', 'active')
                ->sum('plans.price_monthly'),
        ];

        $recentTenants = Tenant::with('plan')->latest()->take(5)->get();
        $tenantsByPlan = Plan::withCount(['tenants' => function($q) {
            $q->where('status', 'active');
        }])->get();

        // Growth metrics (last 6 months)
        $growth = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $growth[] = [
                'month' => $date->format('M'),
                'count' => Tenant::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count()
            ];
        }

        // Top AI Consumers
        $usageService = app(\App\Services\AIUsageService::class);
        $topAiTenants = [];
        foreach (Tenant::where('status', 'active')->take(5)->get() as $tenant) {
            $usage = $usageService->getTenantUsage($tenant);
            if ($usage['count'] > 0) {
                $topAiTenants[] = [
                    'name' => $tenant->name,
                    'requests' => $usage['count'],
                    'cost' => $usage['costs']
                ];
            }
        }
        
        usort($topAiTenants, fn($a, $b) => $b['requests'] <=> $a['requests']);

        return view('admin.dashboard_global', compact('stats', 'recentTenants', 'tenantsByPlan', 'growth', 'topAiTenants'));
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
            $thisMonth = now()->startOfMonth();
            $lastMonth = now()->subMonth()->startOfMonth();

            $totalAthletes = Athlete::count();
            $lastMonthAthletes = Athlete::where('created_at', '<', $thisMonth)->count();
            $athleteTrend = $lastMonthAthletes > 0 ? (($totalAthletes - $lastMonthAthletes) / $lastMonthAthletes) * 100 : 0;

            $thisMonthRevenue = Order::where('status', 'paid')
                ->where('created_at', '>=', $thisMonth)
                ->sum('total_amount');
            $lastMonthRevenue = Order::where('status', 'paid')
                ->whereBetween('created_at', [$lastMonth, $thisMonth->copy()->subSecond()])
                ->sum('total_amount');
            
            $revenueTrend = $lastMonthRevenue > 0 ? (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 : ($thisMonthRevenue > 0 ? 100 : 0);

            $metrics = [
                'total_athletes' => $totalAthletes,
                'athlete_trend' => round($athleteTrend, 1),
                'active_athletes' => Athlete::where('is_active', true)->count(),
                'total_teams' => Team::count(),
                'monthly_revenue' => $thisMonthRevenue,
                'revenue_trend' => round($revenueTrend, 1),
                'total_revenue' => Order::where('status', 'paid')->sum('total_amount'),
                'pending_orders' => Order::where('status', 'pending')->count(),
                'birthday_athletes_count' => Athlete::whereRaw('EXTRACT(DOY FROM birth_date) BETWEEN ? AND ?', [
                    now()->dayOfYear,
                    now()->addDays(7)->dayOfYear
                ])->count(),
            ];
        } catch (\Exception $e) {
            $metrics = [
                'total_athletes' => 0,
                'athlete_trend' => 0,
                'active_athletes' => 0,
                'total_teams' => 0,
                'monthly_revenue' => 0,
                'revenue_trend' => 0,
                'total_revenue' => 0,
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
                    $date = \Carbon\Carbon::parse($item->month);
                    return [
                        'month' => $date->format('Y-m'),
                        'month_label' => $date->format('M/Y'),
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
