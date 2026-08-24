<?php

namespace Tests\Feature;

use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * ═══════════════════════════════════════════════════════
 * TEST: Validación @usap.edu en StoreUsuariosRequest
 * ═══════════════════════════════════════════════════════
 * Al crear un usuario vía POST /usuarios (solo Director), un
 * correo que NO termina en @usap.edu es rechazado; uno con
 *
 * @usap.edu pasa la validación y crea el usuario.
 */
class StoreUsuariosEmailValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Rol::factory()->create(['rol_id' => 1, 'rol_nombre' => 'Director']);
    }

    /**
     * Crea un usuario Director para autenticar la petición.
     */
    private function director(): Usuario
    {
        return Usuario::factory()->create(['rol_id' => 1]);
    }

    #[Test]
    public function crear_usuario_con_correo_no_usap_es_rechazado(): void
    {
        Mail::fake();

        $response = $this->actingAsAuthenticated($this->director())
            ->post(route('usuarios.store'), [
                'usuario_nombre' => 'Usuario Externo',
                'email' => 'externo@gmail.com',
                'contrasena' => 'Password123',
                'rol_id' => 1,
            ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseMissing('usuarios', ['email' => 'externo@gmail.com']);
    }

    #[Test]
    public function crear_usuario_con_correo_usap_pasa_validacion(): void
    {
        Mail::fake();

        $response = $this->actingAsAuthenticated($this->director())
            ->post(route('usuarios.store'), [
                'usuario_nombre' => 'Usuario Usap',
                'email' => 'nuevo@usap.edu',
                'contrasena' => 'Password123',
                'rol_id' => 1,
            ]);

        $response->assertRedirect(route('usuarios.index'));
        $this->assertDatabaseHas('usuarios', ['email' => 'nuevo@usap.edu']);
    }
}
