<?php

namespace Tests\Feature;

use App\Models\Caso;
use App\Models\Cliente;
use App\Models\EstadoCaso;
use App\Models\Procurador;
use App\Models\TipoTramite;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Tests de visibilidad y autoasignación para Procuradores.
 *
 * Cubre las correcciones del bug "el procurador crea pero no ve nada":
 * - Autoasignación forzada del procurador vinculado al crear casos.
 * - Clientes/demandados compartidos (el procurador ve todos).
 * - Blindaje de procurador_id NULL (0 resultados, sin fuga de datos).
 * - Filtros server-side por estado y trámite.
 * - Ruta /clientes/buscar antes que /clientes/{identidad}.
 */
class ProcuradorVisibilidadTest extends TestCase
{
    use RefreshDatabase;

    protected Procurador $procuradorA;

    protected Procurador $procuradorB;

    protected Usuario $procuradorAUser;

    protected Cliente $cliente;

    protected TipoTramite $tipoTramite;

    protected TipoTramite $tipoTramitePenal;

    protected int $estadoEntrevistaId;

    protected int $estadoAdmitidoId;

    protected function setUp(): void
    {
        parent::setUp();

        // ─── [Seed de roles] ─────────────────────────────────
        DB::table('roles')->insert([
            ['rol_id' => 1, 'rol_nombre' => 'Director', 'rol_estado' => 'activo'],
            ['rol_id' => 2, 'rol_nombre' => 'Procurador', 'rol_estado' => 'activo'],
        ]);

        // ─── [Seed de estados del pipeline] ─────────────────
        // 'Entrevista' es el estado inicial que CasoService asigna al crear
        $estadoEntrevista = EstadoCaso::create([
            'estado_nombre' => 'Entrevista',
            'estado_tipo' => 'pipeline',
            'estado_orden' => 1,
            'estado_estado' => 'activo',
        ]);
        $estadoAdmitido = EstadoCaso::create([
            'estado_nombre' => 'Admitido',
            'estado_tipo' => 'pipeline',
            'estado_orden' => 2,
            'estado_estado' => 'activo',
        ]);
        $this->estadoEntrevistaId = $estadoEntrevista->estado_id;
        $this->estadoAdmitidoId = $estadoAdmitido->estado_id;

        // ─── [Tipos de trámite] ──────────────────────────────
        $this->tipoTramite = TipoTramite::create(['tramite_nombre' => 'Divorcio', 'tramite_estado' => 'activo']);
        $this->tipoTramitePenal = TipoTramite::create(['tramite_nombre' => 'Penal', 'tramite_estado' => 'activo']);

        // ─── [Cliente compartido] ────────────────────────────
        $this->cliente = Cliente::create([
            'cliente_dni' => '0801199000001',
            'cliente_nombre' => 'Juan',
            'cliente_apellido' => 'Perez',
            'cliente_estado_civil' => 'Soltero',
            'cliente_telefono' => '9999-0001',
            'cliente_direccion' => 'San Pedro Sula',
            'cliente_estado' => 'activo',
        ]);

        // ─── [Procuradores] ──────────────────────────────────
        $this->procuradorA = Procurador::create([
            'procurador_dni' => '0801199000010',
            'procurador_nombre' => 'Ana',
            'procurador_apellido' => 'Martinez',
            'procurador_carnet' => 'PA-01',
            'procurador_fecha_nacimiento' => '1990-01-01',
            'procurador_genero' => 'Femenino',
            'procurador_telefono' => '8888-0001',
            'procurador_email' => 'ana-vis@test.hn',
            'procurador_estado' => 'activo',
        ]);

        $this->procuradorB = Procurador::create([
            'procurador_dni' => '0801199000011',
            'procurador_nombre' => 'Carlos',
            'procurador_apellido' => 'Lopez',
            'procurador_carnet' => 'PC-02',
            'procurador_fecha_nacimiento' => '1990-02-02',
            'procurador_genero' => 'Masculino',
            'procurador_telefono' => '8888-0002',
            'procurador_email' => 'carlos-vis@test.hn',
            'procurador_estado' => 'activo',
        ]);

        // ─── [Usuario procurador A con procurador vinculado] ─
        $this->procuradorAUser = Usuario::factory()->create([
            'email' => 'proca-vis@test.hn',
            'rol_id' => 2,
            'procurador_id' => $this->procuradorA->procurador_id,
            'debe_cambiar_contrasena' => false,
        ]);
    }

    /**
     * Payload base válido para crear un caso (StoreCasoRequest).
     */
    private function payloadCaso(array $overrides = []): array
    {
        return array_merge([
            'cliente_id' => $this->cliente->cliente_id,
            'tipo_tramite_id' => $this->tipoTramite->tipo_tramite_id,
            'procurador_id' => $this->procuradorB->procurador_id,
            'caso_parte_representada' => 'Juan Perez',
            'caso_relacion_hechos' => 'Hechos del caso.',
        ], $overrides);
    }

    /**
     * Crea un usuario Procurador sin procurador vinculado (procurador_id null).
     */
    private function procuradorSinVinculo(): Usuario
    {
        return Usuario::factory()->create([
            'email' => 'proca-sin-vinculo@test.hn',
            'rol_id' => 2,
            'procurador_id' => null,
            'debe_cambiar_contrasena' => false,
        ]);
    }

    /**
     * Test: El procurador crea un caso enviando el procurador_id de OTRO
     * procurador, pero el sistema lo sobrescribe con el SUYO (autoasignación).
     * Además el caso aparece en su listado de casos.
     */
    public function test_procurador_crea_caso_y_se_autoasigna_su_procurador(): void
    {
        // ─── [ACT] POST a casos.store con procurador_id ajeno ──
        $this->actingAsAuthenticated($this->procuradorAUser)
            ->from(route('casos.create'))
            ->post(route('casos.store'), $this->payloadCaso())
            ->assertRedirect(route('casos.index'));

        // ─── [ASSERT] El caso quedó asignado al procurador del usuario ──
        $caso = Caso::latest('caso_id')->first();
        $this->assertNotNull($caso, 'El caso debería haberse creado.');
        $this->assertSame($this->procuradorA->procurador_id, $caso->procurador_id, 'El caso debe autoasignarse al procurador vinculado.');
        $this->assertDatabaseHas('casos', [
            'caso_id' => $caso->caso_id,
            'procurador_id' => $this->procuradorA->procurador_id,
        ]);

        // ─── [ASSERT] El listado de casos contiene el expediente ──
        $this->get(route('casos.index'))
            ->assertOk()
            ->assertSee($caso->caso_numero_expediente);
    }

    /**
     * Test: El procurador crea un cliente y lo ve en el índice y detalle
     * (los clientes son compartidos del consultorio, no solo "los suyos").
     */
    public function test_procurador_crea_cliente_y_lo_ve_en_index(): void
    {
        $this->actingAsAuthenticated($this->procuradorAUser);

        // ─── [ACT] Crear cliente como Procurador ──────────────
        $this->post(route('clientes.store'), [
            'nombre_completo' => 'María López',
            'cliente_dni' => '0501199900099',
            'cliente_estado_civil' => 'Casado',
            'cliente_telefono' => '9000-0099',
            'cliente_direccion' => 'Calle Nueva 456',
            'cliente_numero_hijos' => 0,
        ])->assertRedirect(route('clientes.index'));

        // ─── [ASSERT] Aparece en el índice (sin whereHas de casos) ──
        $this->get(route('clientes.index'))
            ->assertOk()
            ->assertSee('María')
            ->assertSee('López');

        // ─── [ASSERT] El detalle responde 200 (ya no da 403) ──
        $this->get(route('clientes.show', '0501199900099'))
            ->assertOk();
    }

    /**
     * Test: Un usuario Procurador SIN procurador vinculado no debe ver
     * datos ajenos: el dashboard muestra 0 casos y la bandera de aviso.
     */
    public function test_procurador_sin_procurador_vinculado_no_ve_datos_ajenos(): void
    {
        // ─── [Arrange] Un caso ajeno (de procurador A) existe ──
        Caso::create([
            'caso_numero_expediente' => '0501-2026-00099',
            'cliente_id' => $this->cliente->cliente_id,
            'tipo_tramite_id' => $this->tipoTramite->tipo_tramite_id,
            'procurador_id' => $this->procuradorA->procurador_id,
            'estado_id' => $this->estadoEntrevistaId,
            'caso_parte_representada' => 'Juan Perez',
            'caso_relacion_hechos' => 'Caso ajeno.',
        ]);

        $usuarioSinVinculo = $this->procuradorSinVinculo();

        // ─── [ACT] Dashboard como procurador sin vínculo ──────
        $this->actingAsAuthenticated($usuarioSinVinculo)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('totalCasos', 0)
            ->assertViewHas('sinProcuradorAsignado', true)
            ->assertViewHas('esProcurador', true);
    }

    /**
     * Test: Los filtros server-side por estado y trámite devuelven
     * solo los casos que coinciden con ambos criterios.
     */
    public function test_filtros_de_casos_por_estado_y_tramite(): void
    {
        // ─── [Arrange] Tres casos: solo el primero coincide ──
        $casoCoincide = Caso::create([
            'caso_numero_expediente' => '0501-2026-00001',
            'cliente_id' => $this->cliente->cliente_id,
            'tipo_tramite_id' => $this->tipoTramite->tipo_tramite_id,
            'procurador_id' => $this->procuradorA->procurador_id,
            'estado_id' => $this->estadoEntrevistaId,
            'caso_parte_representada' => 'Juan Perez',
            'caso_relacion_hechos' => 'Caso 1.',
        ]);

        Caso::create([
            'caso_numero_expediente' => '0501-2026-00002',
            'cliente_id' => $this->cliente->cliente_id,
            'tipo_tramite_id' => $this->tipoTramite->tipo_tramite_id,
            'procurador_id' => $this->procuradorA->procurador_id,
            'estado_id' => $this->estadoAdmitidoId,
            'caso_parte_representada' => 'Juan Perez',
            'caso_relacion_hechos' => 'Caso 2 (estado distinto).',
        ]);

        Caso::create([
            'caso_numero_expediente' => '0501-2026-00003',
            'cliente_id' => $this->cliente->cliente_id,
            'tipo_tramite_id' => $this->tipoTramitePenal->tipo_tramite_id,
            'procurador_id' => $this->procuradorA->procurador_id,
            'estado_id' => $this->estadoEntrevistaId,
            'caso_parte_representada' => 'Juan Perez',
            'caso_relacion_hechos' => 'Caso 3 (trámite distinto).',
        ]);

        // ─── [ACT] Filtrar por estado=Entrevista y tramite=Divorcio ──
        $response = $this->actingAsAuthenticated($this->procuradorAUser)
            ->get(route('casos.index', ['estado' => 'Entrevista', 'tramite' => 'Divorcio']))
            ->assertOk();

        // ─── [ASSERT] Solo el caso 1 cumple ambos filtros ─────
        $response->assertViewHas('casos', function ($casos) use ($casoCoincide) {
            return $casos->total() === 1
                && $casos->pluck('caso_numero_expediente')->contains($casoCoincide->caso_numero_expediente);
        });
        $response->assertDontSee('0501-2026-00002')
            ->assertDontSee('0501-2026-00003');
    }

    /**
     * Test: La ruta /clientes/buscar responde 200 JSON y no es
     * capturada por /clientes/{identidad} (debe declararse antes).
     */
    public function test_ruta_clientes_buscar_funciona(): void
    {
        $this->actingAsAuthenticated($this->procuradorAUser);

        // ─── [Arrange] Un cliente que coincida con la búsqueda ──
        Cliente::create([
            'cliente_dni' => '0501199900088',
            'cliente_nombre' => 'Rosa',
            'cliente_apellido' => 'Garcia',
            'cliente_estado_civil' => 'Soltero',
            'cliente_telefono' => '9000-0088',
            'cliente_direccion' => 'Colonia Centro',
            'cliente_estado' => 'activo',
        ]);

        // ─── [ACT] GET /clientes/buscar?q=Rosa ─────────────────
        $this->get('/clientes/buscar?q=Rosa')
            ->assertOk()
            ->assertJsonFragment(['dni' => '0501199900088']);

        // ─── [ASSERT] Con término vacío responde array vacío 200 ──
        $this->get('/clientes/buscar?q=')
            ->assertOk()
            ->assertJson([]);
    }
}
