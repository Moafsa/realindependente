<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTenantStatus
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = tenant();
        
        if (!$tenant) {
            return redirect()->route('marketing.home');
        }

        // Check if tenant is pending payment
        if ($tenant->status === 'pending') {
            // Bloqueia qualquer tentativa de alteração de dados (POST, PUT, DELETE)
            if (!$request->isMethod('GET') && !$request->routeIs('logout')) {
                return back()->with('error', 'Acesso Restrito: Realize o pagamento para desbloquear a criação de atletas, equipes e edição do site.');
            }
        }

        // Check if tenant is active
        if ($tenant->status !== 'active' && $tenant->status !== 'pending') {
            if ($tenant->status === 'suspended') {
                return view('tenant.suspended');
            }
            
            if ($tenant->status === 'trial' && $tenant->trial_ends_at && $tenant->trial_ends_at->isPast()) {
                return view('tenant.trial-expired');
            }
        }

        return $next($request);
    }
}
