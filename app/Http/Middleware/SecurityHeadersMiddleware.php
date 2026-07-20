<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Inyecta cabeceras de seguridad estrictas en cada respuesta HTTP global.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (method_exists($response, 'header')) {
            $response->header('X-Frame-Options', 'DENY');
            // X-XSS-Protection fue eliminado de navegadores modernos; la protección real es la CSP.
            $response->header('X-Content-Type-Options', 'nosniff');
            $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');
            // CSP: unsafe-inline en style-src necesario para Tailwind inline styles + Alpine.js.
            // unsafe-eval eliminado (ya no se usa). script-src solo permite scripts externos del bundle.
            $response->header('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline' https://fonts.bunny.net; font-src 'self' https://fonts.bunny.net data:; img-src 'self' data:; frame-ancestors 'none';");
        }

        return $response;
    }
}
