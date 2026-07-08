<?php

namespace Tests\Feature\Auth;

use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Tests de autenticación de usuarios (Breeze).
 *
 * Verifica el flujo completo de autenticación: renderizado de la pantalla
 * de inicio de sesión, autenticación con credenciales válidas, rechazo de
 * contraseñas incorrectas y cierre de sesión.
 */
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Configuración inicial de cada test.
     *
     * Puebla los roles base (Director, Procurador) para satisfacer
     * la restricción de clave foránea en usuarios.rol_id.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles to satisfy FK constraint on usuarios.rol_id
        DB::table('roles')->insert([
            ['rol_id' => 1, 'rol_nombre' => 'Director', 'rol_estado' => 'activo'],
            ['rol_id' => 2, 'rol_nombre' => 'Procurador', 'rol_estado' => 'activo'],
        ]);
    }

    /**
     * Verifica que la pantalla de inicio de sesión se renderice correctamente.
     *
     * Happy path: la ruta GET /login debe retornar HTTP 200.
     */
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    /**
     * Verifica que un usuario pueda autenticarse con credenciales válidas.
     *
     * Happy path: envía email y contraseña correctos,
     * espera que el usuario quede autenticado y sea redirigido al dashboard.
     */
    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = Usuario::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'contrasena' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    /**
     * Verifica que un usuario NO pueda autenticarse con una contraseña incorrecta.
     *
     * Failure path: envía la contraseña equivocada y espera
     * que el usuario permanezca como invitado (no autenticado).
     */
    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = Usuario::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'contrasena' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    /**
     * Verifica que un usuario autenticado pueda cerrar sesión.
     *
     * Happy path: usuario autenticado envía POST a /logout,
     * espera redirección al login y estado de invitado.
     */
    public function test_users_can_logout(): void
    {
        $user = Usuario::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
