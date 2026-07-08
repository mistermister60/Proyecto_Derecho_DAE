<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests de registro de nuevos usuarios (Breeze).
 *
 * Actualmente todos los tests están marcados como omitidos porque la ruta
 * de registro (/register) no está implementada en el sistema de autenticación
 * personalizado de esta aplicación.
 */
class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Verifica que la pantalla de registro se renderice correctamente.
     *
     * Test omitido: ruta /register no implementada en este sistema de autenticación.
     */
    public function test_registration_screen_can_be_rendered(): void
    {
        $this->markTestSkipped('Registration route (/register) not implemented in this custom auth system.');
    }

    /**
     * Verifica que un nuevo usuario pueda registrarse.
     *
     * Test omitido: ruta /register no implementada en este sistema de autenticación.
     */
    public function test_new_users_can_register(): void
    {
        $this->markTestSkipped('Registration route (/register) not implemented in this custom auth system.');
    }
}
