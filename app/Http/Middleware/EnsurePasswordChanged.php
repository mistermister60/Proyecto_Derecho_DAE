<?php

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
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->debe_cambiar_contrasena) {
            // Rutas permitidas sin haber cambiado la contraseña
            $rutasPermitidas = [
                'password.change',      // formulario de cambio
                'password.update',      // procesar el cambio
                'logout',                // cerrar sesión
                'auth.two-factor',      // vista 2FA
                'auth.two-factor.verify', // validar 2FA
            ];

            if (!in_array($request->route()->getName(), $rutasPermitidas)) {
                return redirect()->route('password.change')
                    ->with('warning', 'Por seguridad, debes cambiar tu contraseña antes de continuar.');
            }
        }

        return $next($request);
    }
}
