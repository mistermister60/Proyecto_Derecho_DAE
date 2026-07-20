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
     * Test: La pantalla de login se puede renderizar.
     */
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    /**
     * Test: Los usuarios pueden autenticarse usando la pantalla de login.
     *
     * Ahora simula el flujo completo: login -> 2FA -> dashboard
     */
    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = Usuario::factory()->create([
            'email' => 'test@example.com',
            'contrasena' => bcrypt('password'),
            'usuario_estado' => 'activo',
            'rol_id' => 2, // Procurador
            'debe_cambiar_contrasena' => false,
        ]);

        // POST login con credenciales válidas
        // Esto redirige a /verify-two-factor (ruta GET)
        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'contrasena' => 'password',
        ]);

        // Debe redirigir a la verificación 2FA (no al dashboard directamente)
        $response->assertRedirect(route('auth.two-factor'));

        // Simular verificación 2FA exitosa
        $code = session('two_factor_code');
        $this->assertNotNull($code, 'El código 2FA debe estar en sesión');

        $response2 = $this->from(route('auth.two-factor'))->post(route('auth.two-factor.verify'), [
            'code' => $code,
        ]);

        // Ahora debe redirigir al dashboard
        $response2->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    /**
     * Test: Los usuarios NO pueden autenticarse con contraseña inválida.
     */
    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = Usuario::factory()->create([
            'email' => 'test@example.com',
            'contrasena' => bcrypt('password'),
            'usuario_estado' => 'activo',
            'rol_id' => 2,
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'contrasena' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test: Los usuarios pueden cerrar sesión.
     */
    public function test_users_can_logout(): void
    {
        $user = Usuario::factory()->create([
            'usuario_estado' => 'activo',
            'rol_id' => 2,
        ]);

        $this->actingAsAuthenticated($user);

        $response = $this->post('/logout');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}