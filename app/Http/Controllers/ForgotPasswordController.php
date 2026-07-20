<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Controlador para la recuperación de contraseña por email (auto-servicio).
 *
 * Permite a cualquier usuario (incluyendo Procuradores) solicitar un enlace
 * de restablecimiento de contraseña a su correo institucional.
 */
class ForgotPasswordController extends BaseController
{
    /**
     * Muestra el formulario para solicitar restablecimiento de contraseña.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Procesa la solicitud de restablecimiento y envía el email.
     */
    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|email|exists:usuarios,email',
        ]);

        $usuario = Usuario::where('email', $request->email)->first();

        // Generar token de restablecimiento (usando el sistema nativo de Laravel Password Reset)
        $token = Str::random(60);
        
        // Guardar en tabla password_reset_tokens (Laravel 11+)
        \DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $usuario->email],
            [
                'token' => hash('sha256', $token),
                'created_at' => now(),
            ]
        );

        // Enviar email con el token
        Mail::to($usuario->email)->send(new ResetPasswordMail($token, $usuario->usuario_nombre, $usuario->email));

        return back()->with('status', 'Hemos enviado un enlace de restablecimiento a tu correo institucional.');
    }
}