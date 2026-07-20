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
            $response->header('X-Content-Type-Options', 'nosniff');
            $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');
            
            // CSP optimizado para producción Laravel Cloud + Vite + PWA + Alpine.js
            // Permite: scripts propios, inline (View Transitions), módulos ES, SW, fuentes, imágenes
            $csp = "default-src 'self'; " .
                   "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://fonts.bunny.net; " .
                   "style-src 'self' 'unsafe-inline' https://fonts.bunny.net; " .
                   "font-src 'self' https://fonts.bunny.net data:; " .
                   "img-src 'self' data: https:; " .
                   "connect-src 'self' https://fonts.bunny.net; " .
                   "frame-ancestors 'none'; " .
                   "worker-src 'self' blob:; " .
                   "manifest-src 'self';";

            $response->header('Content-Security-Policy', $csp);
        }

        return $response;
    }
}
