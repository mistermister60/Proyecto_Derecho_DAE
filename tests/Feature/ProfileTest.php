<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests funcionales del perfil de usuario generados por Laravel Breeze.
 *
 * Actualmente todos los tests están marcados como omitidos porque las rutas
 * de perfil (/profile) no están implementadas en el sistema de autenticación
 * personalizado de esta aplicación.
 */
class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Verifica que la página de perfil se muestre correctamente.
     *
     * Test omitido: ruta /profile no implementada en este sistema de autenticación.
     */
    public function test_profile_page_is_displayed(): void
    {
        $this->markTestSkipped('Profile routes (/profile) not implemented in this custom auth system.');
    }

    /**
     * Verifica que la información del perfil pueda actualizarse.
     *
     * Test omitido: ruta /profile no implementada en este sistema de autenticación.
     */
    public function test_profile_information_can_be_updated(): void
    {
        $this->markTestSkipped('Profile routes (/profile) not implemented in this custom auth system.');
    }

    /**
     * Verifica que el estado de verificación de email no cambie cuando el email no se modifica.
     *
     * Test omitido: ruta /profile no implementada en este sistema de autenticación.
     */
    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $this->markTestSkipped('Profile routes (/profile) not implemented in this custom auth system.');
    }

    /**
     * Verifica que un usuario pueda eliminar su cuenta.
     *
     * Test omitido: ruta /profile no implementada en este sistema de autenticación.
     */
    public function test_user_can_delete_their_account(): void
    {
        $this->markTestSkipped('Profile routes (/profile) not implemented in this custom auth system.');
    }

    /**
     * Verifica que se requiera la contraseña correcta para eliminar la cuenta.
     *
     * Test omitido: ruta /profile no implementada en este sistema de autenticación.
     */
    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $this->markTestSkipped('Profile routes (/profile) not implemented in this custom auth system.');
    }
}
