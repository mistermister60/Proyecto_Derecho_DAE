<?php

/**
 * ═══════════════════════════════════════════════════════
 * TEST: TestCase (Clase Base)
 * ═══════════════════════════════════════════════════════
 * Clase base abstracta para todos los tests de la aplicación.
 * Extiende TestCase de Laravel, deshabilita middlewares que
 * interfieren con los tests (2FA, cambio de contraseña, CSRF)
 * y provee el helper actingAsAuthenticated para simular
 * sesiones completamente autenticadas y verificadas.
 */

namespace Tests;

use App\Http\Middleware\EnsurePasswordChanged;
use App\Http\Middleware\EnsureTwoFactorVerified;
use App\Models\Usuario;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * Clase base abstracta para todos los tests de la aplicación.
 *
 * Extiende la clase TestCase de Laravel para proporcionar la configuración
 * fundamental y los métodos de utilidad comunes a todos los tests unitarios
 * y funcionales del proyecto.
 *
 * Deshabilita middlewares que interfieren con los tests:
 * - EnsureTwoFactorVerified: flujo 2FA
 * - EnsurePasswordChanged: flujo primer login
 * - PreventRequestForgery (VerifyCsrfToken): token CSRF en peticiones POST/PUT/DELETE
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * Configuración que se ejecuta antes de cada test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // ─── Configuración inicial ──────────────────────────
        // Deshabilita los middlewares de 2FA, cambio de
        // contraseña obligatorio y CSRF para evitar
        // interferencias en todos los tests.
        $this->withoutMiddleware([
            EnsureTwoFactorVerified::class,
            EnsurePasswordChanged::class,
            PreventRequestForgery::class,
        ]);
    }

    /**
     * Autentica un usuario y completa el flujo de 2FA simulado.
     *
     * Útil para tests que necesitan un usuario autenticado y verificado.
     *
     * @param  Usuario  $user
     * @return $this
     */
    protected function actingAsAuthenticated($user)
    {
        // ─── [ACT] Iniciar sesión como el usuario ──────────
        $this->actingAs($user);

        // ─── Simular verificación 2FA ──────────────────────
        session(['two_factor_verified' => true]);

        // ─── Simular cambio de contraseña si aplica ────────
        if ($user->debe_cambiar_contrasena) {
            $user->update(['debe_cambiar_contrasena' => false]);
        }

        return $this;
    }
}
