<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class TenantManagementController extends Controller
{
    /**
     * Display a listing of tenants.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Tenant::with(['plan', 'domains']);

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('subdomain', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by plan
        if ($request->filled('plan_id')) {
            $query->where('plan_id', $request->plan_id);
        }

        $tenants = $query->latest()->paginate(20);
        $plans = Plan::all();
        $statuses = ['trial', 'active', 'suspended', 'cancelled'];

        // Statistics
        $stats = [
            'total' => Tenant::count(),
            'active' => Tenant::where('status', 'active')->count(),
            'trial' => Tenant::where('status', 'trial')->count(),
            'suspended' => Tenant::where('status', 'suspended')->count(),
            'cancelled' => Tenant::where('status', 'cancelled')->count(),
        ];

        return view('admin.tenants.index', compact('tenants', 'plans', 'statuses', 'stats'));
    }

    /**
     * Display the specified tenant.
     *
     * @param Tenant $tenant
     * @return \Illuminate\View\View
     */
    public function show(Tenant $tenant)
    {
        $tenant->load(['plan', 'domains']);
        
        // Get tenant statistics (would need to connect to tenant database)
        $stats = [
            'created_at' => $tenant->created_at,
            'trial_ends_at' => $tenant->trial_ends_at,
            'subscription_ends_at' => $tenant->subscription_ends_at,
            'status' => $tenant->status,
        ];

        return view('admin.tenants.show', compact('tenant', 'stats'));
    }

    /**
     * Update the specified tenant.
     *
     * @param Request $request
     * @param Tenant $tenant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Tenant $tenant)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'plan_id' => 'sometimes|required|exists:plans,id',
            'status' => 'sometimes|required|in:trial,active,suspended,cancelled',
            'trial_ends_at' => 'sometimes|nullable|date',
            'subscription_ends_at' => 'sometimes|nullable|date',
        ]);

        try {
            $tenant->update($request->only([
                'name',
                'plan_id',
                'status',
                'trial_ends_at',
                'subscription_ends_at',
            ]));

            Log::info('Tenant updated by admin', [
                'tenant_id' => $tenant->id,
                'changes' => $request->only(['name', 'plan_id', 'status']),
                'admin_id' => auth()->id(),
            ]);

            return redirect()->route('admin.tenants.show', $tenant)
                ->with('success', 'Tenant atualizado com sucesso!');

        } catch (\Exception $e) {
            Log::error('Error updating tenant', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'error' => 'Erro ao atualizar tenant: ' . $e->getMessage()
            ])->withInput();
        }
    }

    /**
     * Suspend the tenant.
     *
     * @param Tenant $tenant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function suspend(Tenant $tenant)
    {
        try {
            $tenant->update(['status' => 'suspended']);

            Log::info('Tenant suspended by admin', [
                'tenant_id' => $tenant->id,
                'admin_id' => auth()->id(),
            ]);

            return redirect()->back()
                ->with('success', 'Tenant suspenso com sucesso!');

        } catch (\Exception $e) {
            Log::error('Error suspending tenant', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'error' => 'Erro ao suspender tenant: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Activate the tenant.
     *
     * @param Tenant $tenant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function activate(Tenant $tenant)
    {
        try {
            $tenant->update(['status' => 'active']);

            Log::info('Tenant activated by admin', [
                'tenant_id' => $tenant->id,
                'admin_id' => auth()->id(),
            ]);

            return redirect()->back()
                ->with('success', 'Tenant ativado com sucesso!');

        } catch (\Exception $e) {
            Log::error('Error activating tenant', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'error' => 'Erro ao ativar tenant: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Cancel the tenant.
     *
     * @param Tenant $tenant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(Tenant $tenant)
    {
        try {
            $tenant->update(['status' => 'cancelled']);

            Log::info('Tenant cancelled by admin', [
                'tenant_id' => $tenant->id,
                'admin_id' => auth()->id(),
            ]);

            return redirect()->back()
                ->with('success', 'Tenant cancelado com sucesso!');

        } catch (\Exception $e) {
            Log::error('Error cancelling tenant', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'error' => 'Erro ao cancelar tenant: ' . $e->getMessage()
            ]);
        }
    }
}

