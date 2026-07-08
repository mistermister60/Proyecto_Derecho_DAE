<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests de confirmación de contraseña (Breeze).
 *
 * Actualmente todos los tests están marcados como omitidos porque la ruta
 * de confirmación de contraseña (/confirm-password) no está implementada
 * en el sistema de autenticación personalizado de esta aplicación.
 */
class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Verifica que la pantalla de confirmación de contraseña se renderice.
     *
     * Test omitido: ruta /confirm-password no implementada.
     */
    public function test_confirm_password_screen_can_be_rendered(): void
    {
        $this->markTestSkipped('Password confirmation route (/confirm-password) not implemented in this custom auth system.');
    }

    /**
     * Verifica que la contraseña pueda ser confirmada correctamente.
     *
     * Test omitido: ruta /confirm-password no implementada.
     */
    public function test_password_can_be_confirmed(): void
    {
        $this->markTestSkipped('Password confirmation route (/confirm-password) not implemented in this custom auth system.');
    }

    /**
     * Verifica que la contraseña no sea confirmada con un valor incorrecto.
     *
     * Test omitido: ruta /confirm-password no implementada.
     */
    public function test_password_is_not_confirmed_with_invalid_password(): void
    {
        $this->markTestSkipped('Password confirmation route (/confirm-password) not implemented in this custom auth system.');
    }
}
