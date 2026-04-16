<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only add headers if enabled in config
        if (!config('security.security_headers.enabled', true)) {
            return $response;
        }

        // X-Frame-Options: Prevent clickjacking attacks
        $response->headers->set(
            'X-Frame-Options',
            config('security.security_headers.x_frame_options', 'DENY')
        );

        // X-Content-Type-Options: Prevent MIME type sniffing
        $response->headers->set(
            'X-Content-Type-Options',
            config('security.security_headers.x_content_type_options', 'nosniff')
        );

        // X-XSS-Protection: Enable XSS filtering
        $response->headers->set(
            'X-XSS-Protection',
            config('security.security_headers.x_xss_protection', '1; mode=block')
        );

        // Referrer-Policy: Control referrer information
        $response->headers->set(
            'Referrer-Policy',
            config('security.security_headers.referrer_policy', 'no-referrer-when-downgrade')
        );

        // Strict-Transport-Security: Force HTTPS
        if (config('app.env') === 'production') {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        // Content-Security-Policy: Prevent XSS and data injection attacks
        if (config('security.csp.enabled', true)) {
            $csp = $this->buildContentSecurityPolicy();
            $response->headers->set('Content-Security-Policy', $csp);
        }

        // Permissions-Policy: Control browser features
        $response->headers->set(
            'Permissions-Policy',
            'geolocation=(), microphone=(), camera=()'
        );

        // Remove sensitive headers
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }

    /**
     * Build Content Security Policy header.
     */
    protected function buildContentSecurityPolicy(): string
    {
        $directives = [
            'default-src' => config('security.csp.default_src', ["'self'"]),
            'script-src' => config('security.csp.script_src', ["'self'", "'unsafe-inline'", "'unsafe-eval'"]),
            'style-src' => config('security.csp.style_src', ["'self'", "'unsafe-inline'"]),
            'img-src' => config('security.csp.img_src', ["'self'", 'data:', 'https:']),
            'font-src' => config('security.csp.font_src', ["'self'"]),
            'connect-src' => config('security.csp.connect_src', ["'self'"]),
            'form-action' => config('security.csp.form_action', ["'self'"]),
            'frame-ancestors' => config('security.csp.frame_ancestors', ["'none'"]),
            'base-uri' => ["'self'"],
            'object-src' => ["'none'"],
        ];

        $policy = [];
        foreach ($directives as $directive => $sources) {
            $policy[] = $directive . ' ' . implode(' ', $sources);
        }

        return implode('; ', $policy);
    }
}
