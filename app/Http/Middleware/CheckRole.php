<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     * 
     * Suporta múltiplos roles separados por pipe (|)
     * Exemplo: role:admin|coach ou role:athlete|guardian
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Você precisa estar autenticado para acessar esta área.');
        }

        $user = auth()->user();
        
        // Divide os roles permitidos (suporta múltiplos roles separados por |)
        $allowedRoles = explode('|', $roles);
        
        // Verifica se o usuário tem algum dos roles permitidos
        if (!in_array($user->role, $allowedRoles)) {
            abort(403, 'Acesso negado. Você não tem permissão para acessar esta área.');
        }

        // Validação adicional para Atletas
        if ($user->role === 'athlete' && !$user->athlete) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Sua conta de atleta ainda não foi totalmente configurada. Entre em contato com o clube.');
        }

        return $next($request);
    }

    /**
     * Redireciona o usuário baseado no seu role após login.
     * 
     * @param string $defaultRoute Rota padrão caso não haja redirecionamento específico
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function redirectByRole($user = null, string $defaultRoute = 'dashboard'): \Illuminate\Http\RedirectResponse
    {
        $user = $user ?? auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $role = $user->role;

        // Se o usuário for admin ou coach, verificamos se ele é dono de algum tenant (independente do status)
        // Super admins NUNCA são redirecionados para subdomínios, eles permanecem no painel global.
        if (in_array($role, ['admin', 'coach']) && !$user->isSuperAdmin()) {
            $tenant = \App\Models\Tenant::where('email', $user->email)->first();
            
            if ($tenant) {
                $tenantUrl = rtrim($tenant->url, '/');
                $currentUrl = rtrim(request()->getSchemeAndHttpHost(), '/');
                $port = request()->getPort();
                if ($port && !in_array($port, [80, 443]) && !str_contains($currentUrl, ":$port")) {
                    $currentUrl .= ":$port";
                }

                // Só redireciona se não estivermos já no domínio do tenant
                if (!str_contains($currentUrl, $tenant->domain)) {
                    return redirect()->to($tenantUrl . '/dashboard');
                }
            }
        }

        // Se o usuário não tem tenant e quer acessar o dashboard central, 
        // mas NÃO é super admin, barramos.
        if (!$user->isSuperAdmin() && !tenant()) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'Acesso não autorizado ao painel global.');
        }

        // Define rotas de redirecionamento por role (Default central)
        $roleRoutes = [
            'admin' => 'dashboard',
            'coach' => 'dashboard',
            'athlete' => 'portal.dashboard',
            'guardian' => 'portal.dashboard',
        ];

        $route = $roleRoutes[$role] ?? $defaultRoute;

        return redirect()->route($route);
    }
}
