<?php

namespace Tests\Feature;

use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Tests funcionales del perfil de usuario.
 *
 * Verifica que la página de perfil se muestre, se pueda actualizar
 * la información y eliminar la cuenta (desactivar).
 */
class ProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles para satisfacer FK
        DB::table('roles')->insert([
            ['rol_id' => 1, 'rol_nombre' => 'Director', 'rol_estado' => 'activo'],
            ['rol_id' => 2, 'rol_nombre' => 'Procurador', 'rol_estado' => 'activo'],
        ]);
    }

    /**
     * Verifica que la página de perfil se muestre correctamente.
     */
    public function test_profile_page_is_displayed(): void
    {
        $user = Usuario::factory()->create([
            'email' => 'test@example.com',
            'contrasena' => Hash::make('password'),
            'usuario_estado' => 'activo',
            'rol_id' => 2,
            'debe_cambiar_contrasena' => false,
        ]);

        $this->actingAsAuthenticated($user)
            ->get('/profile')
            ->assertStatus(200)
            ->assertViewIs('profile.edit')
            ->assertSee($user->usuario_nombre)
            ->assertSee($user->email);
    }

    /**
     * Verifica que la información del perfil pueda actualizarse.
     */
    public function test_profile_information_can_be_updated(): void
    {
        $user = Usuario::factory()->create([
            'email' => 'test@example.com',
            'contrasena' => Hash::make('password'),
            'usuario_estado' => 'activo',
            'rol_id' => 2,
            'debe_cambiar_contrasena' => false,
        ]);

        $this->actingAsAuthenticated($user)
            ->patch('/profile', [
                'usuario_nombre' => 'Nuevo Nombre',
                'email' => 'nuevo@example.com',
            ])
            ->assertRedirect('/profile')
            ->assertSessionHas('success', 'Perfil actualizado exitosamente.');

        $user->refresh();
        $this->assertSame('Nuevo Nombre', $user->usuario_nombre);
        $this->assertSame('nuevo@example.com', $user->email);
    }

    /**
     * Verifica que el estado de verificación de email no cambie cuando el email no se modifica.
     * En nuestro sistema custom no usamos email_verified_at, pero verificamos que no falle.
     */
    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = Usuario::factory()->create([
            'email' => 'test@example.com',
            'contrasena' => Hash::make('password'),
            'usuario_estado' => 'activo',
            'rol_id' => 2,
            'debe_cambiar_contrasena' => false,
        ]);

        $this->actingAsAuthenticated($user)
            ->patch('/profile', [
                'usuario_nombre' => 'Otro Nombre',
                'email' => 'test@example.com', // Mismo email
            ])
            ->assertRedirect('/profile')
            ->assertSessionHas('success', 'Perfil actualizado exitosamente.');

        $user->refresh();
        $this->assertSame('Otro Nombre', $user->usuario_nombre);
        $this->assertSame('test@example.com', $user->email);
    }

    /**
     * Verifica que un usuario pueda desactivar su cuenta.
     */
    public function test_user_can_delete_their_account(): void
    {
        $user = Usuario::factory()->create([
            'email' => 'test@example.com',
            'contrasena' => Hash::make('password'),
            'usuario_estado' => 'activo',
            'rol_id' => 2,
            'debe_cambiar_contrasena' => false,
        ]);

        $this->actingAsAuthenticated($user)
            ->delete('/profile', [
                'password' => 'password',
            ])
            ->assertRedirect('/');

        $user->refresh();
        $this->assertSame('inactivo', $user->usuario_estado);
        $this->assertGuest();
    }

    /**
     * Verifica que se requiera la contraseña correcta para desactivar la cuenta.
     */
    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = Usuario::factory()->create([
            'email' => 'test@example.com',
            'contrasena' => Hash::make('password'),
            'usuario_estado' => 'activo',
            'rol_id' => 2,
            'debe_cambiar_contrasena' => false,
        ]);

        $response = $this->actingAsAuthenticated($user)
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response->assertSessionHasErrorsIn('userDeletion', 'password');
        $user->refresh();
        $this->assertSame('activo', $user->usuario_estado);
    }
}