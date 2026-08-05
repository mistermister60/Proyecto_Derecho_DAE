<?php

/**
 * ═══════════════════════════════════════════════════════
 * MIDDLEWARE: SecurityHeadersMiddleware
 * ═══════════════════════════════════════════════════════
 * Inyecta cabeceras de seguridad HTTP en TODAS las respuestas
 * del sistema para proteger contra ataques comunes:
 *
 * - X-Frame-Options: DENY (protección clickjacking)
 * - X-Content-Type-Options: nosniff (evita MIME sniffing)
 * - Referrer-Policy: estricta (control de referente)
 * - Content-Security-Policy: restringe orígenes de scripts,
 *   estilos, fuentes, imágenes, workers y manifiestos
 *
 * El CSP está optimizado para Laravel Cloud + Vite + PWA + Alpine.js.
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Inyecta cabeceras de seguridad estrictas en cada respuesta HTTP global.
     *
     * @param  Request  $request  Petición HTTP entrante.
     * @param  Closure  $next  Siguiente middleware/controlador en la cadena.
     * @return Response Respuesta HTTP con cabeceras de seguridad añadidas.
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
            $csp = "default-src 'self'; ".
                   "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://fonts.bunny.net; ".
                   "style-src 'self' 'unsafe-inline' https://fonts.bunny.net; ".
                   "font-src 'self' https://fonts.bunny.net data:; ".
                   "img-src 'self' data: https:; ".
                   "connect-src 'self' https://fonts.bunny.net; ".
                   "frame-ancestors 'none'; ".
                   "worker-src 'self' blob:; ".
                   "manifest-src 'self';";

            $response->header('Content-Security-Policy', $csp);
        }

        return $response;
    }
}
