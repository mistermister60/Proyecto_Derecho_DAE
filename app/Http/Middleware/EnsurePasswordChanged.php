<?php

/**
 * ═══════════════════════════════════════════════════════
 * MIDDLEWARE: EnsurePasswordChanged
 * ═══════════════════════════════════════════════════════
 * Fuerza al usuario a cambiar su contraseña temporal en el
 * primer inicio de sesión. Si el campo 'debe_cambiar_contrasena'
 * está activo, redirige a la vista de cambio de contraseña
 * excepto para rutas específicas (logout, 2FA, cambio).
 *
 * Esto garantiza que ningún usuario opere con la contraseña
 * temporal asignada por el Director al crear la cuenta.
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware que fuerza el cambio de contraseña en el primer inicio de sesión.
 *
 * Si el usuario autenticado tiene el campo 'debe_cambiar_contrasena' activo,
 * se le redirige a la vista de cambio de contraseña obligatorio. Solo se
 * permite acceder a esa ruta, al logout y a las rutas de 2FA.
 */
class EnsurePasswordChanged
{
    /**
     * Verificar si el usuario debe cambiar su contraseña antes de continuar.
     *
     * Comprueba:
     * - Si el usuario autenticado tiene el flag 'debe_cambiar_contrasena' activo.
     * - Si la ruta actual NO está en la lista de permitidas (cambio de contraseña,
     *   logout, verificación 2FA), redirige al formulario de cambio.
     *
     * Si falla: redirige a 'password.change' con mensaje de advertencia.
     *
     * @param  Request  $request  Petición HTTP entrante.
     * @param  Closure  $next  Siguiente middleware/controlador en la cadena.
     * @return Response Respuesta HTTP con redirección o contenido normal.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->debe_cambiar_contrasena) {
            // Rutas permitidas sin haber cambiado la contraseña
            $rutasPermitidas = [
                'password.change',          // formulario de cambio
                'password.change.update', // procesar el cambio
                'logout',                // cerrar sesión
                'auth.two-factor',      // vista 2FA
                'auth.two-factor.verify', // validar 2FA
            ];

            if (! in_array($request->route()->getName(), $rutasPermitidas, true)) {
                return redirect()->route('password.change')
                    ->with('warning', 'Por seguridad, debes cambiar tu contraseña antes de continuar.');
            }
        }

        return $next($request);
    }
}
