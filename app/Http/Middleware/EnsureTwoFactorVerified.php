<?php

/**
 * ═══════════════════════════════════════════════════════
 * MIDDLEWARE: EnsureTwoFactorVerified
 * ═══════════════════════════════════════════════════════
 * Verifica que el usuario haya completado la autenticación
 * de dos factores (2FA) mediante OTP enviado por correo.
 *
 * Verifica la marca de sesión 'two_factor_verified' antes de permitir el acceso.
 * Si el código 2FA expiró o nunca se generó, fuerza un
 * re-login completo por seguridad.
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorVerified
{
    /**
     * Validar que el usuario haya verificado el código 2FA antes de acceder.
     *
     * Comprueba:
     * - Si la sesión tiene la marca 'two_factor_verified', permite el paso.
     * - El flujo 2FA es obligatorio para todos los roles (incluido el Director).
     * - Si no hay código 2FA en sesión (expirado o nunca generado),
     *   redirige al login para reiniciar el flujo de autenticación.
     * - Si hay código pero no está verificado, redirige al formulario 2FA.
     *
     * Si falla: redirige al login o al formulario 2FA según el caso.
     *
     * @param  Request  $request  Petición HTTP entrante.
     * @param  Closure  $next  Siguiente middleware/controlador en la cadena.
     * @return Response Respuesta HTTP con redirección o contenido normal.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && ! session()->has('two_factor_verified')) {
            // Si no hay código 2FA en sesión (expirado o nunca generado), forzar re-login
            if (! session()->has('two_factor_code')) {
                return redirect()->route('login');
            }

            return redirect()->route('auth.two-factor');
        }

        return $next($request);
    }
}
