<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

/**
 * ═══════════════════════════════════════════════════════
 * CONTROLADOR: ResetPasswordController
 * ═══════════════════════════════════════════════════════
 * Restablecimiento de contraseña via token (email institucional).
 * Valida token SHA-256 en BD, verifica expiración (60 min),
 * actualiza contraseña y limpia token. Flujo autónomo (no usa broker Laravel).
 * Rutas: GET /password/reset/{token}, POST /password/reset
 * Middleware: guest
 * Roles: Cualquier usuario con email @usap.edu
 * Tabla: password_reset_tokens (token hasheado SHA-256)
 */
class ResetPasswordController extends BaseController
{
    /**
     * ═══════════════════════════════════════════════════════
     * showResetForm
     * ───────────────────────────────────────────────────────
     * Muestra el formulario de restablecimiento con el token.
     * Respuesta: Vista auth.reset-password con token
     * ═══════════════════════════════════════════════════════
     */
    public function showResetForm(Request $request, string $token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    /**
     * ═══════════════════════════════════════════════════════
     * reset
     * ───────────────────────────────────────────────────────
     * Procesa el restablecimiento de contraseña.
     * Valida token, email institucional, contraseña segura.
     * Verifica token en BD (hash SHA-256) y expiración (60 min).
     * Actualiza contraseña, quita bandera debe_cambiar_contrasena si estaba.
     * Elimina token usado. Respuesta: Redirect login con success.
     * ═══════════════════════════════════════════════════════
     */
    public function reset(Request $request): RedirectResponse
    {
        // ─── [Validar datos de entrada] ───────────────────────────────
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:usuarios,email|ends_with:@usap.edu',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ], [
            'token.required' => 'El token de restablecimiento es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El formato del correo electrónico no es válido.',
            'email.exists' => 'No existe ninguna cuenta registrada con este correo.',
            'email.ends_with' => 'El correo debe ser institucional (@usap.edu).',
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'password.min' => 'La contraseña debe tener al menos :min caracteres.',
            'password.mixed' => 'La contraseña debe contener mayúsculas y minúsculas.',
            'password.numbers' => 'La contraseña debe contener al menos un número.',
            'password.symbols' => 'La contraseña debe contener al menos un símbolo.',
        ]);

        // ─── [Verificar token en BD] ──────────────────────────────────
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (! $resetRecord || ! hash_equals($resetRecord->token, hash('sha256', $request->token))) {
            throw ValidationException::withMessages([
                'token' => ['El token de restablecimiento es inválido o ha expirado.'],
            ]);
        }

        // ─── [Verificar expiración (60 minutos)] ──────────────────────
        if (Carbon::parse($resetRecord->created_at)->diffInMinutes(now()) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            throw ValidationException::withMessages([
                'token' => ['El token de restablecimiento ha expirado. Solicita uno nuevo.'],
            ]);
        }

        // ─── [Actualizar contraseña] ──────────────────────────────────
        $usuario = Usuario::where('email', $request->email)->firstOrFail();
        $usuario->contrasena = Hash::make($request->password);

        // Si era primer login, quitar la bandera
        if ($usuario->debe_cambiar_contrasena) {
            $usuario->debe_cambiar_contrasena = false;
        }

        $usuario->save();

        // ─── [Eliminar token usado] ───────────────────────────────────
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Tu contraseña ha sido restablecida correctamente. Ya puedes iniciar sesión.');
    }
}
