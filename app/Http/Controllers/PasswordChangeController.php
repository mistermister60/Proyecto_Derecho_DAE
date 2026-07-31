<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

/**
 * ═══════════════════════════════════════════════════════
 * CONTROLADOR: PasswordChangeController
 * ═══════════════════════════════════════════════════════
 * Gestiona el cambio obligatorio de contraseña en primer inicio de sesión.
 * Flujo: Usuario nuevo (Procurador) → login → OTP → formulario cambio → dashboard.
 * Rutas: GET /password/cambio, PUT /password/cambio
 * Middleware: auth, otp, password.changed (excepto showChangeForm)
 * Roles: Procurador (recién creado), Director
 */
class PasswordChangeController extends BaseController
{
    /**
     * ═══════════════════════════════════════════════════════
     * showChangeForm
     * ───────────────────────────────────────────────────────
     * Muestra el formulario de cambio de contraseña obligatorio.
     * Si el usuario ya cambió su contraseña, redirige al dashboard.
     * Respuesta: Vista auth.password-change
     * ═══════════════════════════════════════════════════════
     */
    public function showChangeForm(Request $request)
    {
        // ─── [Verificar si ya cambió contraseña] ──────────────────────
        if (! auth()->user()->debe_cambiar_contrasena) {
            return redirect()->route('dashboard');
        }

        return view('auth.password-change');
    }

    /**
     * ═══════════════════════════════════════════════════════
     * update
     * ───────────────────────────────────────────────────────
     * Procesa el cambio de contraseña obligatorio.
     * Valida contraseña actual, nueva (con requisitos de seguridad),
     * confirma que no sea igual a la actual, actualiza hash y desactiva
     * la bandera debe_cambiar_contrasena. Respuesta: Redirect dashboard con success.
     * ═══════════════════════════════════════════════════════
     */
    public function update(Request $request): RedirectResponse
    {
        // ─── [Validar datos de entrada] ───────────────────────────────
        $validated = $request->validate([
            'contrasena_actual' => ['required'],
            'nueva_contrasena' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ], [
            'contrasena_actual.required' => 'Debes ingresar tu contraseña actual.',
            'nueva_contrasena.required' => 'Debes ingresar una nueva contraseña.',
            'nueva_contrasena.confirmed' => 'La confirmación de la contraseña no coincide.',
            'nueva_contrasena.min' => 'La contraseña debe tener al menos :min caracteres.',
            'nueva_contrasena.mixed' => 'La contraseña debe contener mayúsculas y minúsculas.',
            'nueva_contrasena.numbers' => 'La contraseña debe contener al menos un número.',
            'nueva_contrasena.symbols' => 'La contraseña debe contener al menos un símbolo.',
        ]);

        $usuario = auth()->user();

        // ─── [Verificar contraseña actual correcta] ───────────────────
        if (! Hash::check($request->input('contrasena_actual'), $usuario->contrasena)) {
            throw ValidationException::withMessages([
                'contrasena_actual' => 'La contraseña actual es incorrecta.',
            ]);
        }

        // ─── [Verificar que nueva no sea igual a la actual] ───────────
        if (Hash::check($request->input('nueva_contrasena'), $usuario->contrasena)) {
            throw ValidationException::withMessages([
                'nueva_contrasena' => 'La nueva contraseña no puede ser igual a la actual.',
            ]);
        }

        // ─── [Actualizar contraseña y desactivar bandera] ─────────────
        $usuario->contrasena = Hash::make($request->input('nueva_contrasena'));
        $usuario->debe_cambiar_contrasena = false;
        $usuario->save();

        return redirect()->route('dashboard')
            ->with('success', 'Tu contraseña ha sido actualizada correctamente.');
    }
}
