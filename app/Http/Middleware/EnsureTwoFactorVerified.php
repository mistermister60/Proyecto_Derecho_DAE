<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && !session()->has('two_factor_verified')) {
            // Director (super usuario) omite 2FA
            $user = auth()->user();
            if ($user->rol && $user->rol->rol_nombre === 'Director') {
                session(['two_factor_verified' => true]);
                return $next($request);
            }

            // Si no hay código 2FA en sesión (expirado o nunca generado), forzar re-login
            if (!session()->has('two_factor_code')) {
                return redirect()->route('login');
            }

            return redirect()->route('auth.two-factor');
        }

        return $next($request);
    }
}
