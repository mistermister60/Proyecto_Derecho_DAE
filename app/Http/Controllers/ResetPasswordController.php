<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

/**
 * Controlador para el restablecimiento de contraseña via token (email).
 *
 * Valida el token enviado por correo y permite al usuario establecer
 * una nueva contraseña segura.
 */
class ResetPasswordController extends BaseController
{
    /**
     * Muestra el formulario de restablecimiento con el token.
     */
    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * Procesa el restablecimiento de contraseña.
     */
    public function reset(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:usuarios,email',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ], [
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        // Verificar token en BD
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (! $resetRecord || ! hash_equals($resetRecord->token, hash('sha256', $request->token))) {
            throw ValidationException::withMessages([
                'token' => ['El token de restablecimiento es inválido o ha expirado.'],
            ]);
        }

        // Verificar expiración (60 minutos)
        if ($resetRecord->created_at->diffInMinutes(now()) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            throw ValidationException::withMessages([
                'token' => ['El token de restablecimiento ha expirado. Solicita uno nuevo.'],
            ]);
        }

        // Actualizar contraseña
        $usuario = Usuario::where('email', $request->email)->firstOrFail();
        $usuario->contrasena = Hash::make($request->password);
        
        // Si era primer login, quitar la bandera
        if ($usuario->debe_cambiar_contrasena) {
            $usuario->debe_cambiar_contrasena = false;
        }
        
        $usuario->save();

        // Eliminar token usado
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Tu contraseña ha sido restablecida correctamente. Ya puedes iniciar sesión.');
    }
}