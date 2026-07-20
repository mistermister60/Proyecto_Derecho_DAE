<?php

namespace Tests;

use App\Http\Middleware\EnsurePasswordChanged;
use App\Http\Middleware\EnsureTwoFactorVerified;
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

        // Deshabilitar middlewares que bloquean los tests
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
     * @param  \App\Models\Usuario  $user
     * @return $this
     */
    protected function actingAsAuthenticated($user)
    {
        $this->actingAs($user);

        // Simular que el usuario ya pasó el 2FA
        session(['two_factor_verified' => true]);

        // Simular que el usuario ya cambió la contraseña si era necesario
        if ($user->debe_cambiar_contrasena) {
            $user->update(['debe_cambiar_contrasena' => false]);
        }

        return $this;
    }
}