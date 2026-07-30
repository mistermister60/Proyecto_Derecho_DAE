<?php

namespace App\Http\Middleware;

use App\Enums\RolEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->rol) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        foreach ($roles as $role) {
            $roleEnum = RolEnum::tryFrom(strtolower($role));
            if (!$roleEnum) {
                abort(403, 'Rol inválido.');
            }
            if (RolEnum::equals($user->rol->rol_nombre, $roleEnum)) {
                return $next($request);
            }
        }

        abort(403, 'No tienes permiso para acceder a esta sección.');
    }
}
