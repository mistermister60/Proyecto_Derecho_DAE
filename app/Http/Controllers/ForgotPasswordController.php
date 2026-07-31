<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * ═══════════════════════════════════════════════════════
 * CONTROLADOR: ForgotPasswordController
 * ═══════════════════════════════════════════════════════
 * Recuperación de contraseña por email (auto-servicio).
 * Permite solicitar enlace de restablecimiento a correo institucional.
 * Genera token aleatorio, hashea SHA-256, guarda en password_reset_tokens,
 * envía email via ResetPasswordMail. Rate limiting por IP (3 intentos/60s).
 * Rutas: GET /password/forgot, POST /password/forgot
 * Middleware: guest
 * Roles: Cualquier usuario con email @usap.edu
 */
class ForgotPasswordController extends BaseController
{
    /**
     * ═══════════════════════════════════════════════════════
     * showLinkRequestForm
     * ───────────────────────────────────────────────────────
     * Muestra el formulario para solicitar restablecimiento de contraseña.
     * Respuesta: Vista auth.forgot-password
     * ═══════════════════════════════════════════════════════
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * ═══════════════════════════════════════════════════════
     * sendResetLinkEmail
     * ───────────────────────────────────────────────────────
     * Procesa la solicitud de restablecimiento y envía el email.
     * Valida email institucional, rate limiting por IP (3/60s),
     * genera token, guarda hash SHA-256 en BD, envía email.
     * Respuesta: Redirect back con status success.
     * ═══════════════════════════════════════════════════════
     */
    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        // ─── [Validar email institucional] ────────────────────────────
        $request->validate([
            'email' => 'required|email|exists:usuarios,email|ends_with:@usap.edu',
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.exists' => 'No existe ninguna cuenta registrada con este correo.',
            'email.ends_with' => 'El correo debe ser institucional (@usap.edu).',
        ]);

        // ─── [Rate limiting por IP] ───────────────────────────────────
        $key = 'forgot-password:'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => "Demasiados intentos. Intente nuevamente en {$seconds} segundos.",
            ]);
        }
        RateLimiter::hit($key, 60);

        // ─── [Obtener usuario] ────────────────────────────────────────
        $usuario = Usuario::where('email', $request->email)->first();

        // ─── [Generar y guardar token (hash SHA-256)] ─────────────────
        $token = Str::random(60);

        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $usuario->email],
            [
                'token' => hash('sha256', $token),
                'created_at' => now(),
            ]
        );

        // ─── [Enviar email con token] ─────────────────────────────────
        Mail::to($usuario->email)->send(new ResetPasswordMail($token, $usuario->usuario_nombre, $usuario->email));

        return back()->with('status', 'Hemos enviado un enlace de restablecimiento a tu correo institucional.');
    }
}
