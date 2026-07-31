<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

/**
 * ═══════════════════════════════════════════════════════
 * CONTROLADOR: PasswordResetController
 * ═══════════════════════════════════════════════════════
 * Flujo "Olvidé mi contraseña" usando broker nativo de Laravel.
 * Permite solicitar enlace de restablecimiento por email y establecer
 * nueva contraseña. No requiere autenticación previa.
 * Rutas: GET /password/reset, POST /password/email, GET /password/reset/{token}, POST /password/reset
 * Middleware: guest (sin autenticación)
 * Roles: Cualquier usuario (incluye Procuradores)
 * Broker: 'users' (tabla password_reset_tokens, expiración 60 min)
 */
class PasswordResetController extends BaseController
{
    /**
     * ═══════════════════════════════════════════════════════
     * showLinkRequestForm
     * ───────────────────────────────────────────────────────
     * Muestra el formulario para solicitar enlace de restablecimiento.
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
     * Envía el enlace de restablecimiento al correo del usuario.
     * Usa broker 'users' de Laravel (tabla password_reset_tokens).
     * Valida email existe en usuarios. Respuesta: Redirect back con status/error.
     * ═══════════════════════════════════════════════════════
     */
    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        // ─── [Validar email] ──────────────────────────────────────────
        $request->validate([
            'email' => 'required|email|exists:usuarios,email',
        ], [
            'email.exists' => 'No existe ninguna cuenta asociada a este correo electrónico.',
        ]);

        // ─── [Enviar enlace via broker nativo] ────────────────────────
        $status = Password::broker('users')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * ═══════════════════════════════════════════════════════
     * showResetForm
     * ───────────────────────────────────────────────────────
     * Muestra el formulario para restablecer la contraseña con el token.
     * Respuesta: Vista auth.reset-password con token
     * ═══════════════════════════════════════════════════════
     */
    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * ═══════════════════════════════════════════════════════
     * reset
     * ───────────────────────────────────────────────────────
     * Procesa el restablecimiento de la contraseña.
     * Valida token, actualiza contraseña y limpia token usado.
     * IMPORTANTE: Si usuario tenía 'debe_cambiar_contrasena = true',
     * se mantiene en true para que el flujo de primer login siga vigente.
     * Respuesta: Redirect login con success o back con errors.
     * ═══════════════════════════════════════════════════════
     */
    public function reset(Request $request): RedirectResponse
    {
        // ─── [Validar datos de entrada] ───────────────────────────────
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:usuarios,email',
            'password' => [
                'required',
                'confirmed',
                \Illuminate\Validation\Rules\Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ], [
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        // ─── [Restablecer via broker nativo] ──────────────────────────
        $status = Password::broker('users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (Usuario $user, string $password) {
                $user->forceFill([
                    'contrasena' => Hash::make($password),
                    // NO tocamos 'debe_cambiar_contrasena' aquí.
                    // Si el admin reseteó la contraseña, el usuario debe seguir
                    // el flujo de primer login (debe_cambiar_contrasena = true).
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', 'Tu contraseña ha sido restablecida correctamente. Ya puedes iniciar sesión.')
            : back()->withErrors(['email' => [__($status)]]);
    }
}
