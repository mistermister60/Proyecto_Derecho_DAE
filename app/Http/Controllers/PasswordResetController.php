<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Controlador para el flujo de "Olvidé mi contraseña" (Password Reset).
 *
 * Permite a cualquier usuario (incluyendo Procuradores) solicitar un enlace
 * de restablecimiento por correo y establecer una nueva contraseña.
 * No requiere autenticación previa.
 */
class PasswordResetController extends BaseController
{
    /**
     * Muestra el formulario para solicitar el enlace de restablecimiento.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Envía el enlace de restablecimiento al correo del usuario.
     *
     * Utiliza el broker de passwords de Laravel (tabla password_reset_tokens).
     * El enlace expira a los 60 minutos por defecto.
     */
    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email|exists:usuarios,email',
        ], [
            'email.exists' => 'No existe ninguna cuenta asociada a este correo electrónico.',
        ]);

        $status = Password::broker('users')->sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Muestra el formulario para restablecer la contraseña con el token.
     */
    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Procesa el restablecimiento de la contraseña.
     *
     * Valida el token, actualiza la contraseña y limpia el token usado.
     * IMPORTANTE: Si el usuario tenía 'debe_cambiar_contrasena = true',
     * lo mantenemos en true para que el flujo de primer login siga vigente.
     */
    public function reset(Request $request): RedirectResponse
    {
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