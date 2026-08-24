<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureTwoFactorVerified;
use App\Models\Procurador;
use App\Models\Usuario;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * ═══════════════════════════════════════════════════════
 * TEST: Completar perfil del procurador
 * ═══════════════════════════════════════════════════════
 * Verifica que el procurador con perfil incompleto es
 * redirigido al formulario y puede completarlo, y que las
 * reglas de validación exigen los campos obligatorios.
 */
class PerfilProcuradorTest extends TestCase
{
    use RefreshDatabase;

    private function crearProcuradorIncompleto(): Usuario
    {
        $this->seed(RolSeeder::class);
        $rolId = DB::table('roles')->where('rol_nombre', 'Procurador')->value('rol_id');

        $procurador = Procurador::create([
            'procurador_nombre' => 'Test',
            'procurador_apellido' => 'Procurador',
            'procurador_dni' => 'TEMP-0000000',
            'procurador_fecha_nacimiento' => '2000-01-01',
            'procurador_email' => 'testp@usap.edu',
            'procurador_estado' => 'activo',
        ]);

        return Usuario::create([
            'rol_id' => $rolId,
            'procurador_id' => $procurador->procurador_id,
            'usuario_nombre' => 'Test Procurador',
            'email' => 'testp@usap.edu',
            'contrasena' => bcrypt('password'),
            'usuario_estado' => 'activo',
            'debe_cambiar_contrasena' => false,
        ]);
    }

    #[Test]
    public function valida_campos_obligatorios(): void
    {
        $user = $this->crearProcuradorIncompleto();
        $this->withoutMiddleware(EnsureTwoFactorVerified::class);

        $response = $this->actingAs($user)->post(route('procuradores.completar-perfil.store'), []);

        $response->assertSessionHasErrors([
            'procurador_dni',
            'procurador_fecha_nacimiento',
            'procurador_telefono',
            'procurador_contacto_emergencia',
        ]);
    }

    #[Test]
    public function usuario_procurador_incompleto_es_redirigido_a_completar_perfil(): void
    {
        $user = $this->crearProcuradorIncompleto();
        $this->withoutMiddleware(EnsureTwoFactorVerified::class);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertRedirect(route('procuradores.completar-perfil'));
    }

    #[Test]
    public function puede_completar_perfil(): void
    {
        $user = $this->crearProcuradorIncompleto();
        $this->withoutMiddleware(EnsureTwoFactorVerified::class);

        $response = $this->actingAs($user)->post(route('procuradores.completar-perfil.store'), [
            'procurador_dni' => '0801-2020-99999',
            'procurador_fecha_nacimiento' => '2000-01-01',
            'procurador_telefono' => '9876-5432',
            'procurador_contacto_emergencia' => 'Contacto Emergencia 911',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseHas('procuradores', [
            'procurador_dni' => '0801-2020-99999',
            'procurador_contacto_emergencia' => 'Contacto Emergencia 911',
        ]);
    }
}
