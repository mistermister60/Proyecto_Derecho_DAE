<?php

namespace App\Http\Middleware;

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

        $userRole = strtolower($user->rol->rol_nombre);

        foreach ($roles as $role) {
            if ($userRole === strtolower($role)) {
                return $next($request);
            }
        }

        abort(403, 'No tienes permiso para acceder a esta sección.');
    }
}
