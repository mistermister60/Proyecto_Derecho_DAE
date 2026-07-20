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
 * Controlador para el cambio obligatorio de contraseña en el primer inicio de sesión.
 *
 * Gestiona la vista donde el usuario (típicamente un Procurador recién creado)
 * cambia su contraseña temporal por una definitiva, y la actualización en BD.
 */
class PasswordChangeController extends BaseController
{
    /**
     * Muestra el formulario de cambio de contraseña obligatorio.
     */
    public function showChangeForm(Request $request)
    {
        // Si el usuario no necesita cambiar la contraseña, ir al dashboard
        if (!auth()->user()->debe_cambiar_contrasena) {
            return redirect()->route('dashboard');
        }

        return view('auth.password-change');
    }

    /**
     * Procesa el cambio de contraseña.
     *
     * Valida que la nueva contraseña cumpla con los requisitos de seguridad,
     * que no sea igual a la actual, actualiza el hash en BD y desactiva la
     * bandera debe_cambiar_contrasena.
     *
     * @throws ValidationException Si la nueva contraseña es igual a la actual.
     */
    public function update(Request $request): RedirectResponse
    {
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
        ]);

        $usuario = auth()->user();

        // Verificar que la contraseña actual sea correcta
        if (!Hash::check($request->input('contrasena_actual'), $usuario->contrasena)) {
            throw ValidationException::withMessages([
                'contrasena_actual' => 'La contraseña actual es incorrecta.',
            ]);
        }

        // Verificar que la nueva no sea igual a la actual
        if (Hash::check($request->input('nueva_contrasena'), $usuario->contrasena)) {
            throw ValidationException::withMessages([
                'nueva_contrasena' => 'La nueva contraseña no puede ser igual a la actual.',
            ]);
        }

        // Actualizar contraseña y desactivar la bandera
        $usuario->contrasena = Hash::make($request->input('nueva_contrasena'));
        $usuario->debe_cambiar_contrasena = false;
        $usuario->save();

        return redirect()->route('dashboard')
            ->with('success', 'Tu contraseña ha sido actualizada correctamente.');
    }
}
