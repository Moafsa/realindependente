<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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

        return $next($request);
    }

    /**
     * Redireciona o usuário baseado no seu role após login.
     * 
     * @param string $defaultRoute Rota padrão caso não haja redirecionamento específico
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function redirectByRole(string $defaultRoute = 'dashboard'): \Illuminate\Http\RedirectResponse
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $role = $user->role;

        // Define rotas de redirecionamento por role
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
