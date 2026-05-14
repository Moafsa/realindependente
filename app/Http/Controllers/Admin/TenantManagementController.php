<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

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
        $query = Tenant::withTrashed()->with(['plan', 'domains']);

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('domain', 'like', '%' . $request->search . '%');
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
            'total' => Tenant::withTrashed()->count(),
            'active' => Tenant::where('status', 'active')->count(),
            'trial' => Tenant::where('status', 'trial')->count(),
            'suspended' => Tenant::where('status', 'suspended')->count(),
            'cancelled' => Tenant::where('status', 'cancelled')->count(),
            'deleted' => Tenant::onlyTrashed()->count(),
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
        
        // Get basic stats
        $stats = [
            'created_at' => $tenant->created_at,
            'trial_ends_at' => $tenant->trial_ends_at,
            'subscription_ends_at' => $tenant->subscription_ends_at,
            'status' => $tenant->status,
        ];

        // Get tenant statistics by manually switching context (safer than run())
        try {
            tenancy()->initialize($tenant);
            $usageStats = [
                'athletes_count' => \App\Models\Athlete::count(),
                'teams_count' => \App\Models\Team::count(),
                'ai_content_count' => \App\Models\AiGeneratedContent::count(),
                'users_count' => \App\Models\User::count(),
            ];
        } catch (\Throwable $e) {
            $usageStats = [
                'athletes_count' => 0,
                'teams_count' => 0,
                'ai_content_count' => 0,
                'users_count' => 0,
            ];
        } finally {
            if (tenancy()->initialized) {
                tenancy()->end();
            }
        }

        return view('admin.tenants.show', compact('tenant', 'stats', 'usageStats'));
    }

    /**
     * Impersonate a tenant admin.
     */
    public function impersonate(Tenant $tenant)
    {
        // Get the primary domain
        $domain = $tenant->domains()->where('is_primary', true)->first() 
               ?? $tenant->domains()->first();

        if (!$domain) {
            return back()->with('error', 'Este clube não possui um domínio configurado.');
        }

        // Generate a signed URL for impersonation
        // We'll use a route that exists on the tenant side
        // Force the root URL to the tenant domain so the signature is generated correctly for that domain
        $currentPort = request()->getPort();
        $portSuffix = ($currentPort && $currentPort != 80 && $currentPort != 443) ? ":$currentPort" : "";
        $protocol = request()->isSecure() ? 'https://' : 'http://';
        
        $originalRootUrl = config('app.url');
        URL::forceRootUrl($protocol . $domain->domain . $portSuffix);
        
        $url = URL::signedRoute('tenant.impersonate', [
            'tenant' => $tenant->id,
            'admin_id' => auth()->id(),
        ], now()->addMinutes(15));
        
        // Restore the original root URL
        URL::forceRootUrl($originalRootUrl);

        return redirect()->away($url);
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

    /**
     * Remove the specified tenant from storage.
     *
     * @param Tenant $tenant
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Tenant $tenant)
    {
        try {
            Log::info('Tenant deletion started by admin', [
                'tenant_id' => $tenant->id,
                'admin_id' => auth()->id(),
            ]);

            // stancl/tenancy correctly handles database and domain deletion
            // if configured in tenancy.php.
            // Force delete to remove the row from the table and allow subdomain reuse
            $tenant->forceDelete();

            return redirect()->route('admin.tenants.index')
                ->with('success', 'Clube excluído com sucesso!');

        } catch (\Exception $e) {
            Log::error('Error deleting tenant', [
                'tenant_id' => $tenant->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'error' => 'Erro ao excluir clube: ' . $e->getMessage()
            ]);
        }
    }
}

