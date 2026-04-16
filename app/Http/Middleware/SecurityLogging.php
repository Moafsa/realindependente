<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class SecurityLogging
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Log security-relevant requests
        $this->logSecurityEvent($request);

        $response = $next($request);

        // Log suspicious responses
        if ($response->getStatusCode() >= 400) {
            $this->logSuspiciousActivity($request, $response);
        }

        return $response;
    }

    /**
     * Log security events.
     */
    protected function logSecurityEvent(Request $request): void
    {
        $securityRoutes = [
            '/login',
            '/register',
            '/logout',
            '/password/reset',
            '/api/v1/ai/',
            '/api/v1/financial/',
        ];

        foreach ($securityRoutes as $route) {
            if (str_contains($request->path(), $route)) {
                Log::channel(config('security.logging.security_events_channel', 'daily'))
                    ->info('Security Event', [
                        'event_type' => 'security_access',
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'path' => $request->path(),
                        'method' => $request->method(),
                        'user_id' => auth()->id(),
                        'timestamp' => now()->toIso8601String(),
                    ]);
                break;
            }
        }
    }

    /**
     * Log suspicious activity.
     */
    protected function logSuspiciousActivity(Request $request, Response $response): void
    {
        $statusCode = $response->getStatusCode();

        // Log failed authentication attempts
        if ($statusCode === 401 || $statusCode === 403) {
            Log::channel(config('security.logging.security_events_channel', 'daily'))
                ->warning('Unauthorized Access Attempt', [
                    'event_type' => 'unauthorized_access',
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'path' => $request->path(),
                    'method' => $request->method(),
                    'status_code' => $statusCode,
                    'user_id' => auth()->id(),
                    'timestamp' => now()->toIso8601String(),
                ]);
        }

        // Log potential attacks
        if ($statusCode === 429) { // Too Many Requests
            Log::channel(config('security.logging.security_events_channel', 'daily'))
                ->warning('Rate Limit Exceeded', [
                    'event_type' => 'rate_limit_exceeded',
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'path' => $request->path(),
                    'method' => $request->method(),
                    'timestamp' => now()->toIso8601String(),
                ]);
        }

        // Log SQL injection attempts
        if ($this->detectSQLInjection($request)) {
            Log::channel(config('security.logging.security_events_channel', 'daily'))
                ->critical('Potential SQL Injection Attempt', [
                    'event_type' => 'sql_injection_attempt',
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'path' => $request->path(),
                    'method' => $request->method(),
                    'input' => $request->all(),
                    'timestamp' => now()->toIso8601String(),
                ]);
        }

        // Log XSS attempts
        if ($this->detectXSS($request)) {
            Log::channel(config('security.logging.security_events_channel', 'daily'))
                ->critical('Potential XSS Attempt', [
                    'event_type' => 'xss_attempt',
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'path' => $request->path(),
                    'method' => $request->method(),
                    'input' => $request->all(),
                    'timestamp' => now()->toIso8601String(),
                ]);
        }
    }

    /**
     * Detect potential SQL injection attempts.
     */
    protected function detectSQLInjection(Request $request): bool
    {
        $patterns = [
            '/(\bUNION\b.*\bSELECT\b)/i',
            '/(\bSELECT\b.*\bFROM\b)/i',
            '/(\bINSERT\b.*\bINTO\b)/i',
            '/(\bDELETE\b.*\bFROM\b)/i',
            '/(\bDROP\b.*\bTABLE\b)/i',
            '/(\bUPDATE\b.*\bSET\b)/i',
            '/(\'|\")(\s*)OR(\s*)(\'|\")(\s*)=(\s*)(\'|\")/i',
            '/--/',
            '/;(\s*)DROP/',
        ];

        $input = json_encode($request->all());

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Detect potential XSS attempts.
     */
    protected function detectXSS(Request $request): bool
    {
        $patterns = [
            '/<script[^>]*>.*?<\/script>/is',
            '/<iframe[^>]*>.*?<\/iframe>/is',
            '/javascript:/i',
            '/on\w+\s*=/i', // onclick, onload, etc.
            '/<embed[^>]*>/i',
            '/<object[^>]*>/i',
        ];

        $input = json_encode($request->all());

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $input)) {
                return true;
            }
        }

        return false;
    }
}
