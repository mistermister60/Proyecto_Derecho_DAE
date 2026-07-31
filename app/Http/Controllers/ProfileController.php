<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

/**
 * ═══════════════════════════════════════════════════════
 * CONTROLADOR: ProfileController
 * ═══════════════════════════════════════════════════════
 * Gestión del perfil del usuario autenticado.
 * Permite editar información personal (nombre, email) y
 * eliminar la cuenta (desactivación lógica).
 * ───────────────────────────────────────────────────────
 * Rutas protegidas: middleware ['auth', 'otp', 'password.changed']
 * Roles: Director y Procurador (cada uno ve su propio perfil)
 */
class ProfileController extends Controller
{
    /**
     * ═══════════════════════════════════════════════════════
     * edit
     * ───────────────────────────────────────────────────────
     * Muestra el formulario de edición del perfil del usuario
     * autenticado. Incluye pestañas para información personal
     * y cambio de contraseña.
     * ═══════════════════════════════════════════════════════
     *
     * @return View Vista profile.edit con el usuario autenticado
     */
    public function edit(Request $request): View
    {
        // ─── [Renderizado de vista con usuario actual] ──────────
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * ═══════════════════════════════════════════════════════
     * update
     * ───────────────────────────────────────────────────────
     * Actualiza la información del perfil del usuario.
     * Valida mediante ProfileUpdateRequest (nombre, email).
     * Rellena el modelo con datos validados y guarda.
     * ═══════════════════════════════════════════════════════
     *
     * @param  ProfileUpdateRequest  $request  Datos validados del perfil
     * @return RedirectResponse Redirección a edición con mensaje
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // ─── [Actualización de datos validados] ─────────────────
        $request->user()->fill($request->validated());
        $request->user()->save();

        // ─── [Redirección con mensaje de éxito] ────────────────
        return Redirect::route('profile.edit')->with('success', 'Perfil actualizado exitosamente.');
    }

    /**
     * ═══════════════════════════════════════════════════════
     * destroy
     * ───────────────────────────────────────────────────────
     * Elimina la cuenta del usuario (desactivación lógica).
     * Valida la contraseña actual mediante validación inline
     * con bag 'userDeletion'. Cierra sesión, marca usuario
     * como 'inactivo', invalida sesión y regenera token CSRF.
     * ═══════════════════════════════════════════════════════
     *
     * @return RedirectResponse Redirección a raíz (login)
     */
    public function destroy(Request $request): RedirectResponse
    {
        // ─── [Validación de contraseña actual] ─────────────────
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        // ─── [Cierre de sesión] ────────────────────────────────
        Auth::logout();

        // ─── [Desactivación lógica del usuario] ────────────────
        $user->update(['usuario_estado' => 'inactivo']);

        // ─── [Limpieza de sesión] ──────────────────────────────
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // ─── [Redirección a login] ─────────────────────────────
        return Redirect::to('/');
    }
}
