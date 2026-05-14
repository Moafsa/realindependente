<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Plan;
use App\Models\Order;
use App\Services\AsaasService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FinancialController extends Controller
{
    protected AsaasService $asaasService;

    public function __construct(AsaasService $asaasService)
    {
        $this->asaasService = $asaasService;
    }

    /**
     * Display the global financial dashboard.
     */
    public function index()
    {
        try {
            // 1. Métricas de Assinaturas (Clubes pagando para a plataforma)
            $tenants = Tenant::with('plan')->get();
            $totalSubscriptionRevenue = 0;
            
            foreach ($tenants as $tenant) {
                if ($tenant->status === 'active' && $tenant->plan) {
                    // Simulação baseada no valor mensal do plano
                    $totalSubscriptionRevenue += (float) $tenant->plan->price_monthly;
                }
            }

            // 2. Métricas de Vendas dos Clubes (Volume total transacionado nos clubes)
            $totalSalesVolume = 0;
            $totalPlatformCommissions = 0;
            
            foreach ($tenants as $tenant) {
                try {
                    tenancy()->initialize($tenant);
                    
                    // Volume total de pedidos pagos neste clube
                    $tenantRevenue = \App\Models\Order::where('status', 'paid')->sum('total_amount');
                    $totalSalesVolume += (float) $tenantRevenue;
                    
                    // Comissão da plataforma baseada no admin_fee_percentage do plano deste tenant
                    $fee = $tenant->plan->admin_fee_percentage ?? 0;
                    $totalPlatformCommissions += ($tenantRevenue * ($fee / 100));
                    
                } catch (\Throwable $e) {
                    Log::warning("FinancialController Admin: Erro ao carregar métricas para tenant {$tenant->id}: " . $e->getMessage());
                } finally {
                    if (tenancy()->initialized) {
                        tenancy()->end();
                    }
                }
            }

            $stats = [
                'subscription_revenue' => $totalSubscriptionRevenue,
                'sales_volume' => $totalSalesVolume,
                'platform_commissions' => $totalPlatformCommissions,
                'total_profit' => $totalSubscriptionRevenue + $totalPlatformCommissions,
                'active_tenants' => $tenants->where('status', 'active')->count(),
                'pending_tenants' => $tenants->where('status', 'pending')->count(),
                'total_tenants' => $tenants->count(),
            ];

            // 3. Faturas de Assinaturas Recentes (Tenants Recentes)
            $recentSubscriptions = Tenant::with('plan')
                ->whereIn('status', ['active', 'trial', 'pending'])
                ->latest()
                ->take(10)
                ->get();

            return view('admin.financial.index', compact('stats', 'recentSubscriptions', 'tenants'));

        } catch (\Throwable $e) {
            Log::error('FinancialController Admin Index Error: ' . $e->getMessage());
            return back()->with('error', 'Erro ao carregar dashboard financeiro: ' . $e->getMessage());
        }
    }

    /**
     * List all club subscriptions.
     */
    public function subscriptions()
    {
        $tenants = Tenant::with('plan')->latest()->paginate(20);
        return view('admin.financial.subscriptions', compact('tenants'));
    }

    /**
     * List sales volume by club.
     */
    public function clubSales()
    {
        $tenants = Tenant::with('plan')->get();
        $salesData = [];

        foreach ($tenants as $tenant) {
            try {
                tenancy()->initialize($tenant);
                
                $revenue = \App\Models\Order::where('status', 'paid')->sum('total_amount');
                $ordersCount = \App\Models\Order::where('status', 'paid')->count();
                $fee = $tenant->plan->admin_fee_percentage ?? 0;
                $commission = $revenue * ($fee / 100);

                $salesData[] = (object) [
                    'tenant_id' => $tenant->id,
                    'tenant_name' => $tenant->name,
                    'plan_name' => $tenant->plan->name ?? 'N/A',
                    'revenue' => $revenue,
                    'orders_count' => $ordersCount,
                    'commission' => $commission,
                    'fee_percentage' => $fee,
                ];
                
            } catch (\Throwable $e) {
                Log::warning("FinancialController Admin Club Sales: Erro para tenant {$tenant->id}: " . $e->getMessage());
            } finally {
                if (tenancy()->initialized) {
                    tenancy()->end();
                }
            }
        }

        return view('admin.financial.club-sales', compact('salesData'));
    }
}
