<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests de verificación de correo electrónico (Breeze).
 *
 * Actualmente todos los tests están marcados como omitidos porque las rutas
 * de verificación de email (/verify-email) no están implementadas en el
 * sistema de autenticación personalizado de esta aplicación.
 */
class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Verifica que la pantalla de verificación de email se renderice.
     *
     * Test omitido: ruta /verify-email no implementada en este sistema de autenticación.
     */
    public function test_email_verification_screen_can_be_rendered(): void
    {
        $this->markTestSkipped('Email verification route (/verify-email) not implemented in this custom auth system.');
    }

    /**
     * Verifica que un email pueda ser verificado correctamente.
     *
     * Test omitido: ruta /verify-email no implementada en este sistema de autenticación.
     */
    public function test_email_can_be_verified(): void
    {
        $this->markTestSkipped('Email verification route (/verify-email) not implemented in this custom auth system.');
    }

    /**
     * Verifica que un email no se verifique con un hash inválido.
     *
     * Test omitido: ruta /verify-email no implementada en este sistema de autenticación.
     */
    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $this->markTestSkipped('Email verification route (/verify-email) not implemented in this custom auth system.');
    }
}
