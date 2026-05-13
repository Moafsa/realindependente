<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TenantController extends Controller
{
    /**
     * Display a listing of the tenants.
     */
    public function index()
    {
        $tenants = Tenant::with(['plan', 'domains'])->latest()->paginate(10);
        
        return view('admin.tenants.index', compact('tenants'));
    }

    /**
     * Display the specified tenant.
     */
    public function show($id)
    {
        $tenant = Tenant::with(['plan', 'domains'])->findOrFail($id);
        
        return view('admin.tenants.show', compact('tenant'));
    }

    /**
     * Toggle tenant status (activate/suspend).
     */
    public function toggleStatus(Tenant $tenant)
    {
        $newStatus = $tenant->status === 'active' ? 'suspended' : 'active';
        $tenant->update(['status' => $newStatus]);

        return back()->with('success', "O clube {$tenant->name} foi " . ($newStatus === 'active' ? 'ativado' : 'suspenso') . " com sucesso.");
    }

    /**
     * Statistics for the Super Admin Dashboard.
     */
    public function dashboard()
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
        
        $tenantsByPlan = Plan::withCount('tenants')->get();

        return view('admin.dashboard', compact('stats', 'recentTenants', 'tenantsByPlan'));
    }
}
