<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Update last login
            Auth::user()->update(['last_login_at' => now()]);

            // Redirect based on user role
            return $this->redirectBasedOnRole(Auth::user());
        }

        throw ValidationException::withMessages([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
        ]);
    }

    /**
     * Show the registration form.
     */
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * Handle registration request.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:athlete,guardian',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => true,
        ]);

        Auth::login($user);

        return redirect()->route('portal.dashboard')
            ->with('success', 'Conta criada com sucesso!');
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('site.home');
    }

    /**
     * Redirect user based on their role.
     */
    private function redirectBasedOnRole(User $user)
    {
        return match($user->role) {
            'admin' => redirect()->route('dashboard'),
            'coach' => redirect()->route('dashboard'),
            'athlete' => redirect()->route('portal.dashboard'),
            'guardian' => redirect()->route('portal.dashboard'),
            default => redirect()->route('site.home'),
        };
    }

    /**
     * Show the password reset request form.
     */
    public function showPasswordReset()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle password reset request.
     */
    public function passwordReset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        // TODO: Implement password reset logic
        // This would typically involve sending an email with a reset link

        return back()->with('success', 'Se o email existir, você receberá um link para redefinir sua senha.');
    }

    /**
     * Show the password reset form.
     */
    public function showPasswordResetForm(Request $request, $token)
    {
        // TODO: Implement password reset form
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Handle password reset.
     */
    public function passwordResetUpdate(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // TODO: Implement password reset logic

        return redirect()->route('auth.login')
            ->with('success', 'Sua senha foi redefinida com sucesso!');
    }
}
