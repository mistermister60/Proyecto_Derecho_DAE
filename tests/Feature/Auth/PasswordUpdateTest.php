<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests de actualización de contraseña (Breeze).
 *
 * Actualmente todos los tests están marcados como omitidos porque la ruta
 * de actualización de contraseña (PUT /password) no está implementada
 * en el sistema de autenticación personalizado de esta aplicación.
 */
class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Verifica que la contraseña del usuario pueda ser actualizada.
     *
     * Test omitido: ruta PUT /password no implementada.
     */
    public function test_password_can_be_updated(): void
    {
        $this->markTestSkipped('Password update route (PUT /password) not implemented in this custom auth system.');
    }

    /**
     * Verifica que se requiera la contraseña actual correcta para actualizarla.
     *
     * Test omitido: ruta PUT /password no implementada.
     */
    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        $this->markTestSkipped('Password update route (PUT /password) not implemented in this custom auth system.');
    }
}
