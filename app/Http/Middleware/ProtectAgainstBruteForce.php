<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ProtectAgainstBruteForce
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $path = $request->path();

        // Only protect authentication routes
        if (!$this->isProtectedRoute($path)) {
            return $next($request);
        }

        // Check if IP is blocked
        if ($this->isBlocked($ip)) {
            Log::channel('security')->warning('Blocked IP attempted access', [
                'ip' => $ip,
                'path' => $path,
                'timestamp' => now()->toIso8601String(),
            ]);

            return response()->json([
                'message' => 'Too many failed attempts. Please try again later.',
                'retry_after' => $this->getBlockDuration($ip),
            ], 429);
        }

        $response = $next($request);

        // Track failed attempts
        if ($response->getStatusCode() === 401 || $response->getStatusCode() === 422) {
            $this->incrementFailedAttempts($ip);
        } else if ($response->getStatusCode() === 200) {
            // Clear failed attempts on successful login
            $this->clearFailedAttempts($ip);
        }

        return $response;
    }

    /**
     * Check if route should be protected.
     */
    protected function isProtectedRoute(string $path): bool
    {
        $protectedRoutes = [
            'login',
            'register',
            'password/reset',
            'api/v1/auth',
        ];

        foreach ($protectedRoutes as $route) {
            if (str_contains($path, $route)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if IP is blocked.
     */
    protected function isBlocked(string $ip): bool
    {
        return Cache::has("blocked:{$ip}");
    }

    /**
     * Increment failed login attempts.
     */
    protected function incrementFailedAttempts(string $ip): void
    {
        $key = "failed_attempts:{$ip}";
        $attempts = Cache::get($key, 0) + 1;
        $maxAttempts = config('security.max_login_attempts', 5);

        Cache::put($key, $attempts, now()->addMinutes(15));

        if ($attempts >= $maxAttempts) {
            $this->blockIP($ip);
            
            Log::channel('security')->critical('IP blocked due to multiple failed attempts', [
                'ip' => $ip,
                'attempts' => $attempts,
                'timestamp' => now()->toIso8601String(),
            ]);
        }
    }

    /**
     * Block an IP address.
     */
    protected function blockIP(string $ip): void
    {
        $duration = config('security.lockout_duration', 900); // 15 minutes default
        Cache::put("blocked:{$ip}", true, now()->addSeconds($duration));
    }

    /**
     * Clear failed attempts.
     */
    protected function clearFailedAttempts(string $ip): void
    {
        Cache::forget("failed_attempts:{$ip}");
        Cache::forget("blocked:{$ip}");
    }

    /**
     * Get remaining block duration.
     */
    protected function getBlockDuration(string $ip): int
    {
        $key = "blocked:{$ip}";
        if (!Cache::has($key)) {
            return 0;
        }

        // Get TTL in seconds
        return Cache::get($key) ? config('security.lockout_duration', 900) : 0;
    }
}
