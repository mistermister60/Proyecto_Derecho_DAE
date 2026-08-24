<?php

namespace Tests\Feature;

use App\Models\Procurador;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * ═══════════════════════════════════════════════════════
 * TEST: Middleware EnsureProcuradorProfileComplete
 * ═══════════════════════════════════════════════════════
 * Verifica que un procurador con DNI temporal (prefijo TEMP-)
 * es redirigido al formulario de completar perfil, mientras
 * que un procurador con DNI real NO es redirigido.
 */
class EnsureProcuradorProfileCompleteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Crea el rol Procurador necesario para vincular el usuario.
     */
    private function crearRolProcurador(): void
    {
        Rol::factory()->create(['rol_id' => 2, 'rol_nombre' => 'Procurador']);
    }

    #[Test]
    public function procurador_con_dni_temporal_es_redirigido_a_completar_perfil(): void
    {
        $this->crearRolProcurador();

        $procurador = Procurador::factory()->create([
            'procurador_dni' => 'TEMP-1234567',
            'procurador_fecha_nacimiento' => '2000-01-01',
            'procurador_telefono' => null,
            'procurador_contacto_emergencia' => null,
        ]);

        $usuario = Usuario::factory()->create([
            'rol_id' => 2,
            'procurador_id' => $procurador->procurador_id,
            'debe_cambiar_contrasena' => false,
        ]);

        $response = $this->actingAsAuthenticated($usuario)->get(route('dashboard'));

        $response->assertRedirect(route('procuradores.completar-perfil'));
    }

    #[Test]
    public function procurador_con_dni_real_no_es_redirigido(): void
    {
        $this->crearRolProcurador();

        $procurador = Procurador::factory()->create([
            'procurador_dni' => '0801-2020-12345',
            'procurador_fecha_nacimiento' => '2000-01-01',
            'procurador_telefono' => '9999-9999',
            'procurador_contacto_emergencia' => 'Contacto Emergencia',
        ]);

        $usuario = Usuario::factory()->create([
            'rol_id' => 2,
            'procurador_id' => $procurador->procurador_id,
            'debe_cambiar_contrasena' => false,
        ]);

        $response = $this->actingAsAuthenticated($usuario)->get(route('dashboard'));

        $response->assertOk();
    }
}
