<?php

namespace Tests\Feature;

use App\Models\Caso;
use App\Models\Cliente;
use App\Models\EstadoCaso;
use App\Models\Procurador;
use App\Models\Rol;
use App\Models\TipoTramite;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class SistemaInmunologicoTest extends TestCase
{
    use RefreshDatabase;

    public function test_las_cabeceras_de_seguridad_estan_presentes_globalmente(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
    }

    public function test_los_cambios_en_casos_disparan_logs_de_auditoria_activa(): void
    {
        Log::shouldReceive('channel')
            ->with('audit')
            ->once()
            ->andReturn(new class {
                public function info($msg, $context) {
                    // Simula canal activo de logs inmutables
                }
            });

        $rolDirector = Rol::create(['rol_nombre' => 'Director', 'rol_descripcion' => 'Dir', 'rol_estado' => 'activo']);
        $estado = EstadoCaso::create(['estado_nombre' => 'Tramite', 'estado_orden' => 1, 'estado_color' => '#000', 'estado_tipo' => 'pipeline']);
        $tramite = TipoTramite::create(['tramite_nombre' => 'Civil', 'tramite_descripcion' => 'x', 'tramite_estado' => 'activo']);
        $cliente = Cliente::create([
            'cliente_nombre' => 'Pedro', 'cliente_apellido' => 'Sosa', 'cliente_dni' => '0501199000111',
            'cliente_estado_civil' => 'Soltero', 'cliente_telefono' => '9999-9999', 'cliente_direccion' => 'SPS',
            'cliente_numero_hijos' => 0, 'cliente_estado' => 'activo'
        ]);
        $proc = Procurador::create([
            'procurador_nombre' => 'A', 'procurador_apellido' => 'B', 'procurador_dni' => '0501199100222',
            'procurador_carnet' => 'C-100', 'procurador_fecha_nacimiento' => '1991-01-01', 'procurador_genero' => 'Masculino',
            'procurador_email' => 'a@b.com', 'procurador_telefono' => '8888-8888', 'procurador_estado' => 'activo'
        ]);

        $caso = Caso::create([
            'caso_numero_expediente' => '0501-2026-99999',
            'cliente_id' => $cliente->cliente_id,
            'tipo_tramite_id' => $tramite->tipo_tramite_id,
            'estado_id' => $estado->estado_id,
            'procurador_id' => $proc->procurador_id,
            'caso_parte_representada' => 'Demandante',
            'caso_relacion_hechos' => 'Inicial',
            'caso_admisible' => true,
            'caso_estado' => 'activo',
        ]);

        $caso->update(['caso_juzgado' => 'Juzgado de Letras']);
    }
}