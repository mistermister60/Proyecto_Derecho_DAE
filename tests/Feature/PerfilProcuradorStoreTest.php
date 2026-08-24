<?php

namespace Tests\Feature;

use App\Models\Procurador;
use App\Models\Rol;
use App\Models\Usuario;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * ═══════════════════════════════════════════════════════
 * TEST: PerfilProcuradorController::store + PerfilProcuradorRequest
 * ═══════════════════════════════════════════════════════
 * - POST válido actualiza el procurador y quita el prefijo TEMP- del DNI.
 * - POST inválido (campos faltantes) devuelve error de validación (422).
 * - POST con DNI que conserva el prefijo TEMP- devuelve error (422, not_regex).
 */
class PerfilProcuradorStoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolSeeder::class);
    }

    /**
     * Crea un procurador con DNI temporal (perfil incompleto) y su usuario.
     */
    private function crearProcuradorTemporal(): Usuario
    {
        $rolId = Rol::where('rol_nombre', 'Procurador')->value('rol_id');

        $procurador = Procurador::factory()->create([
            'procurador_dni' => 'TEMP-7654321',
            'procurador_fecha_nacimiento' => '2000-01-01',
            'procurador_telefono' => null,
            'procurador_contacto_emergencia' => null,
        ]);

        return Usuario::factory()->create([
            'rol_id' => $rolId,
            'procurador_id' => $procurador->procurador_id,
            'debe_cambiar_contrasena' => false,
        ]);
    }

    #[Test]
    public function post_valido_actualiza_procurador_y_quita_prefijo_temp(): void
    {
        $usuario = $this->crearProcuradorTemporal();
        $procurador = $usuario->procurador;

        $response = $this->actingAsAuthenticated($usuario)
            ->post(route('procuradores.completar-perfil.store'), [
                'procurador_dni' => '0801-2020-55555',
                'procurador_fecha_nacimiento' => '1998-05-20',
                'procurador_telefono' => '9876-5432',
                'procurador_contacto_emergencia' => 'Contacto Emergencia 911',
            ]);

        $response->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('procuradores', [
            'procurador_id' => $procurador->procurador_id,
            'procurador_dni' => '0801-2020-55555',
            'procurador_telefono' => '9876-5432',
            'procurador_contacto_emergencia' => 'Contacto Emergencia 911',
        ]);

        $dniFinal = Procurador::find($procurador->procurador_id)->procurador_dni;
        $this->assertFalse(
            str_starts_with($dniFinal, 'TEMP-'),
            "El DNI {$dniFinal} aún conserva el prefijo TEMP-."
        );
    }

    #[Test]
    public function post_con_campos_faltantes_devuelve_error_validacion(): void
    {
        $usuario = $this->crearProcuradorTemporal();

        // Las rutas web devuelven 302 + errores en sesión (no 422) al
        // fallar la validación. Se usa el patrón estándar del proyecto.
        $response = $this->actingAsAuthenticated($usuario)
            ->post(route('procuradores.completar-perfil.store'), []);

        $response->assertSessionHasErrors([
            'procurador_dni',
            'procurador_fecha_nacimiento',
            'procurador_telefono',
            'procurador_contacto_emergencia',
        ]);
    }

    #[Test]
    public function post_con_dni_temporal_devuelve_error_validacion(): void
    {
        $usuario = $this->crearProcuradorTemporal();

        $response = $this->actingAsAuthenticated($usuario)
            ->post(route('procuradores.completar-perfil.store'), [
                'procurador_dni' => 'TEMP-7654321',
                'procurador_fecha_nacimiento' => '1998-05-20',
                'procurador_telefono' => '9876-5432',
                'procurador_contacto_emergencia' => 'Contacto Emergencia 911',
            ]);

        $response->assertSessionHasErrors('procurador_dni');
    }
}
