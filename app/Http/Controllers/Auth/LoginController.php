<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Middleware\CheckRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'remember' => 'boolean',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            \Log::info('Login success', ['email' => $user->email, 'id' => $user->id, 'tenant' => tenant('id')]);
            
            $request->session()->regenerate();
            
            return CheckRole::redirectByRole($user);
        }

        \Log::warning('Login failed', ['email' => $request->email, 'tenant' => tenant('id')]);
        
        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        // Log the logout
        // activity()
        //     ->causedBy(Auth::user())
        //     ->log('User logged out');

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Impersonate a tenant user (usually admin).
     */
    public function impersonate(Request $request)
    {
        if (!$request->hasValidSignature()) {
            abort(403, 'Link de impersonação inválido ou expirado.');
        }

        // Find the first admin user in this tenant
        $user = \App\Models\User::where('role', 'admin')->first();

        if (!$user) {
            abort(404, 'Nenhum administrador encontrado para este clube.');
        }

        Auth::login($user);
        
        $request->session()->put('impersonated_by', $request->admin_id);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('success', 'Você está personificando ' . $user->name);
    }
}
