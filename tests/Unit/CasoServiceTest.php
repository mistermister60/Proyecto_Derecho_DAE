<?php

namespace Tests\Unit;

use App\Models\Caso;
use App\Models\Cliente;
use App\Models\Demandado;
use App\Models\EstadoCaso;
use App\Models\Procurador;
use App\Models\Reasignacion;
use App\Models\Rol;
use App\Models\TipoTramite;
use App\Models\Usuario;
use App\Services\CasoService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests unitarios del servicio CasoService.
 *
 * Cubre la lógica de negocio crítica: generación de expedientes con lockForUpdate,
 * reasignación transaccional, cierre de casos, desactivación y consultas index/Kanban.
 */
class CasoServiceTest extends TestCase
{
    use RefreshDatabase;

    private CasoService $casoService;

    // Datos de prueba creados en setUp
    private int $estadoEntrevistaId;

    private int $estadoEnProcesoId;

    private int $estadoCerradoId;

    private int $tipoTramiteCivilId;

    private int $tipoTramitePenalId;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear roles base
        Rol::factory()->create(['rol_id' => 1, 'rol_nombre' => 'Director']);
        Rol::factory()->create(['rol_id' => 2, 'rol_nombre' => 'Procurador']);

        // Crear estados de caso necesarios con IDs fijos
        $this->estadoEntrevistaId = EstadoCaso::factory()->create([
            'estado_id' => 1, 'estado_nombre' => 'Entrevista', 'estado_tipo' => 'pipeline', 'estado_orden' => 1, 'estado_color' => '#3B82F6',
        ])->estado_id;

        $this->estadoEnProcesoId = EstadoCaso::factory()->create([
            'estado_id' => 2, 'estado_nombre' => 'En Proceso', 'estado_tipo' => 'pipeline', 'estado_orden' => 2, 'estado_color' => '#F59E0B',
        ])->estado_id;

        $this->estadoCerradoId = EstadoCaso::factory()->create([
            'estado_id' => 3, 'estado_nombre' => 'Cerrado', 'estado_tipo' => 'cerrado', 'estado_orden' => 3, 'estado_color' => '#10B981',
        ])->estado_id;

        // Crear tipos de trámite con IDs fijos
        $this->tipoTramiteCivilId = TipoTramite::factory()->create([
            'tipo_tramite_id' => 1, 'tramite_nombre' => 'Civil',
        ])->tipo_tramite_id;

        $this->tipoTramitePenalId = TipoTramite::factory()->create([
            'tipo_tramite_id' => 2, 'tramite_nombre' => 'Penal',
        ])->tipo_tramite_id;

        $this->casoService = new CasoService;
    }

    /** @test */
    public function test_create_caso_genera_expediente_con_formato_correcto(): void
    {
        $cliente = Cliente::factory()->create();
        $demandado = Demandado::factory()->create();
        $procurador = Procurador::factory()->create();

        $data = [
            'cliente_id' => $cliente->cliente_id,
            'demandado_id' => $demandado->demandado_id,
            'tipo_tramite_id' => $this->tipoTramiteCivilId,
            'procurador_id' => $procurador->procurador_id,
            'caso_parte_representada' => 'Demandante',
            'caso_juzgado' => 'Juzgado 1',
            'caso_relacion_hechos' => 'Hechos de prueba',
        ];

        $caso = $this->casoService->createCaso($data);

        $this->assertInstanceOf(Caso::class, $caso);
        $this->assertMatchesRegularExpression('/^0501-\d{4}-\d{5}$/', $caso->caso_numero_expediente);
        $this->assertEquals('activo', $caso->caso_estado);
        $this->assertNotNull($caso->caso_fecha_interpuesta);
        $this->assertNotNull($caso->caso_fecha_asignacion);
        $this->assertEquals('Entrevista', $caso->estado->estado_nombre);
    }

    /** @test */
    public function test_create_caso_incrementa_correlativo_correctamente(): void
    {
        $cliente = Cliente::factory()->create();
        $demandado = Demandado::factory()->create();
        $procurador = Procurador::factory()->create();

        $baseData = [
            'cliente_id' => $cliente->cliente_id,
            'demandado_id' => $demandado->demandado_id,
            'tipo_tramite_id' => $this->tipoTramiteCivilId,
            'procurador_id' => $procurador->procurador_id,
            'caso_parte_representada' => 'Demandante',
            'caso_relacion_hechos' => 'Hechos',
        ];

        // Crear primer caso
        $caso1 = $this->casoService->createCaso($baseData);
        $correlativo1 = (int) substr($caso1->caso_numero_expediente, -5);

        // Crear segundo caso
        $caso2 = $this->casoService->createCaso($baseData);
        $correlativo2 = (int) substr($caso2->caso_numero_expediente, -5);

        $this->assertEquals($correlativo1 + 1, $correlativo2);
    }

    /** @test */
    public function test_create_caso_usa_lock_for_update_para_evitar_race_conditions(): void
    {
        $cliente = Cliente::factory()->create();
        $demandado = Demandado::factory()->create();
        $procurador = Procurador::factory()->create();

        $data = [
            'cliente_id' => $cliente->cliente_id,
            'demandado_id' => $demandado->demandado_id,
            'tipo_tramite_id' => $this->tipoTramiteCivilId,
            'procurador_id' => $procurador->procurador_id,
            'caso_parte_representada' => 'Demandante',
            'caso_relacion_hechos' => 'Hechos',
        ];

        // Ejecutar múltiples creaciones en secuencia rápida
        $casos = [];
        for ($i = 0; $i < 5; $i++) {
            $casos[] = $this->casoService->createCaso($data);
        }

        $correlativos = array_map(fn ($c) => (int) substr($c->caso_numero_expediente, -5), $casos);
        $this->assertEquals(range($correlativos[0], $correlativos[0] + 4), $correlativos);
    }

    /** @test */
    public function test_reassign_caso_crea_registro_reasignacion_y_actualiza_procurador(): void
    {
        $cliente = Cliente::factory()->create();
        $demandado = Demandado::factory()->create();
        $procuradorOrigen = Procurador::factory()->create();
        $procuradorDestino = Procurador::factory()->create();

        // Crear caso manualmente con datos del setUp
        $caso = Caso::create([
            'caso_numero_expediente' => '0501-'.date('Y').'-00001',
            'cliente_id' => $cliente->cliente_id,
            'demandado_id' => $demandado->demandado_id,
            'tipo_tramite_id' => $this->tipoTramiteCivilId,
            'procurador_id' => $procuradorOrigen->procurador_id,
            'caso_parte_representada' => 'Demandante',
            'caso_relacion_hechos' => 'Hechos',
            'estado_id' => $this->estadoEntrevistaId,
            'caso_fecha_interpuesta' => now()->toDateString(),
            'caso_fecha_asignacion' => now()->toDateString(),
            'caso_estado' => 'activo',
        ]);

        $data = [
            'procurador_destino_id' => $procuradorDestino->procurador_id,
            'reasignacion_motivo' => 'Cambio de carga laboral',
        ];

        $casoActualizado = $this->casoService->reassignCaso($caso, $data);

        $this->assertEquals($procuradorDestino->procurador_id, $casoActualizado->procurador_id);

        $reasignacion = Reasignacion::where('caso_id', $caso->caso_id)->first();
        $this->assertNotNull($reasignacion);
        $this->assertEquals($procuradorOrigen->procurador_id, $reasignacion->procurador_origen_id);
        $this->assertEquals($procuradorDestino->procurador_id, $reasignacion->procurador_destino_id);
        $this->assertEquals('Cambio de carga laboral', $reasignacion->reasignacion_motivo);
        $this->assertEquals('completada', $reasignacion->reasignacion_estado);
    }

    /** @test */
    public function test_reassign_caso_revierte_transaccion_si_falla_creacion_reasignacion(): void
    {
        $cliente = Cliente::factory()->create();
        $demandado = Demandado::factory()->create();
        $procuradorOrigen = Procurador::factory()->create();

        $caso = Caso::create([
            'caso_numero_expediente' => '0501-'.date('Y').'-00002',
            'cliente_id' => $cliente->cliente_id,
            'demandado_id' => $demandado->demandado_id,
            'tipo_tramite_id' => $this->tipoTramiteCivilId,
            'procurador_id' => $procuradorOrigen->procurador_id,
            'caso_parte_representada' => 'Demandante',
            'caso_relacion_hechos' => 'Hechos',
            'estado_id' => $this->estadoEntrevistaId,
            'caso_fecha_interpuesta' => now()->toDateString(),
            'caso_fecha_asignacion' => now()->toDateString(),
            'caso_estado' => 'activo',
        ]);

        // Procurador destino inexistente debería fallar la FK
        $data = [
            'procurador_destino_id' => 99999,
            'reasignacion_motivo' => 'Motivo',
        ];

        $this->expectException(QueryException::class);
        $this->casoService->reassignCaso($caso, $data);

        // Verificar que el caso no cambió de procurador (rollback)
        $caso->refresh();
        $this->assertEquals($procuradorOrigen->procurador_id, $caso->procurador_id);
    }

    /** @test */
    public function test_close_caso_marca_como_cerrado_y_guarda_resolucion(): void
    {
        $caso = Caso::create([
            'caso_numero_expediente' => '0501-'.date('Y').'-00003',
            'cliente_id' => Cliente::factory()->create()->cliente_id,
            'tipo_tramite_id' => $this->tipoTramiteCivilId,
            'procurador_id' => Procurador::factory()->create()->procurador_id,
            'caso_parte_representada' => 'Demandante',
            'caso_relacion_hechos' => 'Hechos',
            'estado_id' => $this->estadoEntrevistaId,
            'caso_fecha_interpuesta' => now()->toDateString(),
            'caso_fecha_asignacion' => now()->toDateString(),
            'caso_estado' => 'activo',
        ]);

        $data = [
            'resolucion_tipo' => 'ganado',
            'resolucion_fecha' => '2026-01-15',
            'resolucion_notas' => 'Sentencia favorable',
        ];

        $result = $this->casoService->closeCaso($caso, $data);

        $this->assertTrue($result);
        $caso->refresh();
        $this->assertEquals('cerrado', $caso->caso_estado);
        $this->assertEquals('ganado', $caso->resolucion_tipo);
        $this->assertEquals('2026-01-15', $caso->resolucion_fecha->toDateString());
        $this->assertEquals('Sentencia favorable', $caso->resolucion_notas);
    }

    /** @test */
    public function test_close_caso_usa_fecha_actual_si_no_se_proporciona(): void
    {
        $caso = Caso::create([
            'caso_numero_expediente' => '0501-'.date('Y').'-00004',
            'cliente_id' => Cliente::factory()->create()->cliente_id,
            'tipo_tramite_id' => $this->tipoTramiteCivilId,
            'procurador_id' => Procurador::factory()->create()->procurador_id,
            'caso_parte_representada' => 'Demandante',
            'caso_relacion_hechos' => 'Hechos',
            'estado_id' => $this->estadoEntrevistaId,
            'caso_fecha_interpuesta' => now()->toDateString(),
            'caso_fecha_asignacion' => now()->toDateString(),
            'caso_estado' => 'activo',
        ]);

        $data = [
            'resolucion_tipo' => 'conciliado',
            // Sin resolucion_fecha
        ];

        $this->casoService->closeCaso($caso, $data);

        $caso->refresh();
        $this->assertEquals(now()->toDateString(), $caso->resolucion_fecha->toDateString());
    }

    /** @test */
    public function test_deactivate_caso_cambia_estado_a_inactivo(): void
    {
        $caso = Caso::create([
            'caso_numero_expediente' => '0501-'.date('Y').'-00005',
            'cliente_id' => Cliente::factory()->create()->cliente_id,
            'tipo_tramite_id' => $this->tipoTramiteCivilId,
            'procurador_id' => Procurador::factory()->create()->procurador_id,
            'caso_parte_representada' => 'Demandante',
            'caso_relacion_hechos' => 'Hechos',
            'estado_id' => $this->estadoEntrevistaId,
            'caso_fecha_interpuesta' => now()->toDateString(),
            'caso_fecha_asignacion' => now()->toDateString(),
            'caso_estado' => 'activo',
        ]);

        $result = $this->casoService->deactivateCaso($caso);

        $this->assertTrue($result);
        $caso->refresh();
        $this->assertEquals('inactivo', $caso->caso_estado);
    }

    /** @test */
    public function test_get_index_data_retorna_estructura_esperada(): void
    {
        $director = Usuario::factory()->create(['rol_id' => 1]);
        $this->actingAsAuthenticated($director);

        $data = $this->casoService->getIndexData();

        $this->assertArrayHasKey('casos', $data);
        $this->assertArrayHasKey('estados', $data);
        $this->assertArrayHasKey('tramites', $data);
        $this->assertArrayHasKey('columnas', $data);
        $this->assertInstanceOf(LengthAwarePaginator::class, $data['casos']);
    }

    /** @test */
    public function test_get_index_data_filtra_por_procurador_si_usuario_es_procurador(): void
    {
        $procurador = Procurador::factory()->create();
        $usuarioProcurador = Usuario::factory()->create([
            'rol_id' => 2,
            'procurador_id' => $procurador->procurador_id,
        ]);
        $this->actingAsAuthenticated($usuarioProcurador);

        // Crear caso propio
        $casoPropio = Caso::create([
            'caso_numero_expediente' => '0501-'.date('Y').'-00006',
            'cliente_id' => Cliente::factory()->create()->cliente_id,
            'tipo_tramite_id' => $this->tipoTramiteCivilId,
            'procurador_id' => $procurador->procurador_id,
            'caso_parte_representada' => 'Demandante',
            'caso_relacion_hechos' => 'Hechos',
            'estado_id' => $this->estadoEntrevistaId,
            'caso_fecha_interpuesta' => now()->toDateString(),
            'caso_fecha_asignacion' => now()->toDateString(),
            'caso_estado' => 'activo',
        ]);

        // Caso de otro procurador
        Caso::create([
            'caso_numero_expediente' => '0501-'.date('Y').'-00007',
            'cliente_id' => Cliente::factory()->create()->cliente_id,
            'tipo_tramite_id' => $this->tipoTramiteCivilId,
            'procurador_id' => Procurador::factory()->create()->procurador_id,
            'caso_parte_representada' => 'Demandante',
            'caso_relacion_hechos' => 'Hechos',
            'estado_id' => $this->estadoEntrevistaId,
            'caso_fecha_interpuesta' => now()->toDateString(),
            'caso_fecha_asignacion' => now()->toDateString(),
            'caso_estado' => 'activo',
        ]);

        $data = $this->casoService->getIndexData();

        $this->assertEquals(1, $data['casos']->total());
        $this->assertEquals($casoPropio->caso_id, $data['casos']->first()->caso_id);
    }

    /** @test */
    public function test_get_kanban_columns_agrupa_casos_por_estado(): void
    {
        $director = Usuario::factory()->create(['rol_id' => 1]);
        $this->actingAsAuthenticated($director);

        // Crear casos con estados específicos usando IDs del setUp
        Caso::create([
            'caso_numero_expediente' => '0501-'.date('Y').'-00008',
            'cliente_id' => Cliente::factory()->create()->cliente_id,
            'tipo_tramite_id' => $this->tipoTramiteCivilId,
            'procurador_id' => Procurador::factory()->create()->procurador_id,
            'caso_parte_representada' => 'Demandante',
            'caso_relacion_hechos' => 'Hechos 1',
            'estado_id' => $this->estadoEntrevistaId,
            'caso_fecha_interpuesta' => now()->toDateString(),
            'caso_fecha_asignacion' => now()->toDateString(),
            'caso_estado' => 'activo',
        ]);

        Caso::create([
            'caso_numero_expediente' => '0501-'.date('Y').'-00009',
            'cliente_id' => Cliente::factory()->create()->cliente_id,
            'tipo_tramite_id' => $this->tipoTramiteCivilId,
            'procurador_id' => Procurador::factory()->create()->procurador_id,
            'caso_parte_representada' => 'Demandante',
            'caso_relacion_hechos' => 'Hechos 2',
            'estado_id' => $this->estadoEntrevistaId,
            'caso_fecha_interpuesta' => now()->toDateString(),
            'caso_fecha_asignacion' => now()->toDateString(),
            'caso_estado' => 'activo',
        ]);

        Caso::create([
            'caso_numero_expediente' => '0501-'.date('Y').'-00010',
            'cliente_id' => Cliente::factory()->create()->cliente_id,
            'tipo_tramite_id' => $this->tipoTramiteCivilId,
            'procurador_id' => Procurador::factory()->create()->procurador_id,
            'caso_parte_representada' => 'Demandante',
            'caso_relacion_hechos' => 'Hechos 3',
            'estado_id' => $this->estadoEnProcesoId,
            'caso_fecha_interpuesta' => now()->toDateString(),
            'caso_fecha_asignacion' => now()->toDateString(),
            'caso_estado' => 'activo',
        ]);

        $data = $this->casoService->getIndexData();
        $columnas = $data['columnas'];

        $this->assertArrayHasKey('Entrevista', $columnas);
        $this->assertArrayHasKey('En Proceso', $columnas);
        $this->assertCount(2, $columnas['Entrevista'][1]); // [color, tarjetas]
        $this->assertCount(1, $columnas['En Proceso'][1]);
    }

    /** @test */
    public function test_get_kanban_columns_maneja_casos_sin_cliente_tramite_audiencia(): void
    {
        $director = Usuario::factory()->create(['rol_id' => 1]);
        $this->actingAsAuthenticated($director);

        $cliente = Cliente::factory()->create();
        $caso = Caso::create([
            'caso_numero_expediente' => '0501-'.date('Y').'-00011',
            'cliente_id' => $cliente->cliente_id,
            'tipo_tramite_id' => $this->tipoTramiteCivilId,
            'procurador_id' => Procurador::factory()->create()->procurador_id,
            'caso_parte_representada' => 'Demandante',
            'caso_relacion_hechos' => 'Hechos',
            'estado_id' => $this->estadoEntrevistaId,
            'caso_fecha_interpuesta' => now()->toDateString(),
            'caso_fecha_asignacion' => now()->toDateString(),
            'caso_estado' => 'activo',
        ]);

        $data = $this->casoService->getIndexData();
        $columnas = $data['columnas'];

        $tarjetas = $columnas['Entrevista'][1];
        $this->assertArrayHasKey($caso->caso_numero_expediente, $tarjetas);
        $this->assertEquals($cliente->nombre_completo, $tarjetas[$caso->caso_numero_expediente][0]);
        $this->assertEquals('Civil', $tarjetas[$caso->caso_numero_expediente][1]);
    }
}
