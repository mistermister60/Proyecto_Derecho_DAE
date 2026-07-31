<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * ═══════════════════════════════════════════════════════
 * CONTROLADOR: ConfirmPasswordController
 * ═══════════════════════════════════════════════════════
 * Confirmación de contraseña (password confirmation).
 * Requerido antes de acciones sensibles (cambiar email,
 * borrar cuenta, etc.). Utiliza el middleware
 * 'password.confirm' para verificar que la sesión ha
 * sido confirmada recientemente.
 * ───────────────────────────────────────────────────────
 * Rutas: GET /confirmar-contrasena, POST /confirmar-contrasena
 * Middleware: 'auth' (en routes/web.php)
 */
class ConfirmPasswordController extends BaseController
{
    /**
     * ═══════════════════════════════════════════════════════
     * show
     * ───────────────────────────────────────────────────────
     * Muestra el formulario de confirmación de contraseña.
     * ═══════════════════════════════════════════════════════
     *
     * @return View Vista auth.confirm-password
     */
    public function show(Request $request)
    {
        return view('auth.confirm-password');
    }

    /**
     * ═══════════════════════════════════════════════════════
     * store
     * ───────────────────────────────────────────────────────
     * Confirma la contraseña del usuario.
     * Valida la contraseña actual y marca la sesión como
     * confirmada mediante el timestamp
     * 'auth.password_confirmed_at'.
     * ═══════════════════════════════════════════════════════
     *
     * @return RedirectResponse Redirección a ruta intentada o dashboard
     */
    public function store(Request $request): RedirectResponse
    {
        // ─── [Validación de contraseña actual] ─────────────────
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        // ─── [Marcar sesión como confirmada (timestamp)] ────────
        $request->session()->put('auth.password_confirmed_at', time());

        // ─── [Redirección a ruta intentada o dashboard] ─────────
        return redirect()->intended(
            $request->session()->pull('password_confirmation_redirect', route('dashboard'))
        );
    }
}
