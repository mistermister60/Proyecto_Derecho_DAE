<?php

/**
 * ═══════════════════════════════════════════════════════
 * MIDDLEWARE: CheckRole
 * ═══════════════════════════════════════════════════════
 * Verifica que el usuario autenticado posea al menos uno
 * de los roles especificados en la ruta antes de permitir
 * el acceso. Si no tiene el rol adecuado, responde con
 * un error 403 (prohibido).
 *
 * Uso en rutas: ->middleware('role:director,procurador')
 */

namespace App\Http\Middleware;

use App\Enums\RolEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Validar que el usuario autenticado tenga al menos uno de los roles requeridos.
     *
     * Comprueba:
     * - Que exista un usuario autenticado con un rol asignado.
     * - Que el nombre del rol (insensible a mayúsculas) coincida con alguno
     *   de los roles pasados como parámetros en la ruta.
     *
     * Si falla: retorna HTTP 403 con mensaje genérico sin revelar información.
     *
     * @param  Request  $request  Petición HTTP entrante.
     * @param  Closure  $next  Siguiente middleware/controlador en la cadena.
     * @param  string  ...$roles  Lista de roles permitidos (ej. 'director', 'procurador').
     * @return Response Respuesta HTTP con el contenido de la ruta o error 403.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->rol) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        foreach ($roles as $role) {
            $roleEnum = RolEnum::tryFrom(strtolower($role));
            if (! $roleEnum) {
                abort(403, 'Rol inválido.');
            }
            if (RolEnum::equals($user->rol->rol_nombre, $roleEnum)) {
                return $next($request);
            }
        }

        abort(403, 'No tienes permiso para acceder a esta sección.');
    }
}
