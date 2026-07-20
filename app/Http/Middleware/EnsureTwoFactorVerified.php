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

            return redirect()->route('login')->withErrors(['error' => 'Debes completar la verificación de dos factores.']);
        }

        return $next($request);
    }
}
