<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests de restablecimiento de contraseña (Breeze).
 *
 * Actualmente todos los tests están marcados como omitidos porque las rutas
 * de restablecimiento de contraseña (/forgot-password, /reset-password) no
 * están implementadas en el sistema de autenticación personalizado.
 */
class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Verifica que la pantalla de solicitud de restablecimiento se renderice.
     *
     * Test omitido: rutas /forgot-password y /reset-password no implementadas.
     */
    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $this->markTestSkipped('Password reset routes (/forgot-password, /reset-password) not implemented in this custom auth system.');
    }

    /**
     * Verifica que se pueda solicitar un enlace de restablecimiento.
     *
     * Test omitido: rutas /forgot-password y /reset-password no implementadas.
     */
    public function test_reset_password_link_can_be_requested(): void
    {
        $this->markTestSkipped('Password reset routes (/forgot-password, /reset-password) not implemented in this custom auth system.');
    }

    /**
     * Verifica que la pantalla de restablecimiento con token se renderice.
     *
     * Test omitido: rutas /forgot-password y /reset-password no implementadas.
     */
    public function test_reset_password_screen_can_be_rendered(): void
    {
        $this->markTestSkipped('Password reset routes (/forgot-password, /reset-password) not implemented in this custom auth system.');
    }

    /**
     * Verifica que la contraseña se pueda restablecer con un token válido.
     *
     * Test omitido: rutas /forgot-password y /reset-password no implementadas.
     */
    public function test_password_can_be_reset_with_valid_token(): void
    {
        $this->markTestSkipped('Password reset routes (/forgot-password, /reset-password) not implemented in this custom auth system.');
    }
}
