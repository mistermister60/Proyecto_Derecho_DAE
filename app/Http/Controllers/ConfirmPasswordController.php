<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Controlador para confirmación de contraseña (password confirmation).
 *
 * Requerido antes de acciones sensibles (cambiar email, borrar cuenta, etc.).
 * Utiliza el middleware 'password.confirm' para verificar que la sesión
 * ha sido confirmada recientemente.
 */
class ConfirmPasswordController extends BaseController
{
    /**
     * Muestra el formulario de confirmación de contraseña.
     */
    public function show(Request $request)
    {
        return view('auth.confirm-password');
    }

    /**
     * Confirma la contraseña del usuario.
     *
     * Valida la contraseña actual y marca la sesión como confirmada
     * mediante el timestamp 'auth.password_confirmed_at'.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        // Marcar sesión como confirmada (timestamp)
        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(
            $request->session()->pull('password_confirmation_redirect', route('dashboard'))
        );
    }
}