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
 * Tests de autorización para Casos (CRUD, reasignación, validaciones).
 *
 * Verifica permisos diferenciados entre Director y Procurador:
 * - Procurador: solo sus casos, campos limitados, sin destroy/reasignar
 * - Director: todos los casos, todos los campos, destroy/reasignar
 */
class CasoAutorizacionTest extends TestCase
{
    use RefreshDatabase;

    protected $procuradorA;
    protected $procuradorB;
    protected $director;
    protected $casoDeA;
    protected $cliente;
    protected $tipoTramite;
    protected $estadoAdmitidoId;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles
        DB::table('roles')->insert([
            ['rol_id' => 1, 'rol_nombre' => 'Director', 'rol_estado' => 'activo'],
            ['rol_id' => 2, 'rol_nombre' => 'Procurador', 'rol_estado' => 'activo'],
        ]);

        // Seed estados caso
        $estadoAdmitido = EstadoCaso::create(['estado_nombre' => 'Admitido', 'estado_estado' => 'activo']);
        EstadoCaso::create(['estado_nombre' => 'En tramite', 'estado_estado' => 'activo']);
        EstadoCaso::create(['estado_nombre' => 'Cerrado', 'estado_estado' => 'activo']);
        $this->estadoAdmitidoId = $estadoAdmitido->estado_id;

        // Seed tipo tramite
        $this->tipoTramite = TipoTramite::create(['tramite_nombre' => 'Divorcio', 'tramite_estado' => 'activo']);

        // Clientes
        $this->cliente = Cliente::create([
            'cliente_dni' => '0801199000001',
            'cliente_nombre' => 'Juan',
            'cliente_apellido' => 'Perez',
            'cliente_telefono' => '9999-0001',
            'cliente_direccion' => 'San Pedro Sula',
            'cliente_estado' => 'activo',
        ]);

        // Procuradores
        $this->procuradorA = Procurador::create([
            'procurador_dni' => '0801199000010',
            'procurador_nombre' => 'Ana',
            'procurador_apellido' => 'Martinez',
            'procurador_carnet' => 'PA-01',
            'procurador_fecha_nacimiento' => '1990-01-01',
            'procurador_genero' => 'Femenino',
            'procurador_telefono' => '8888-0001',
            'procurador_email' => 'ana@test.hn',
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
            'procurador_email' => 'carlos@test.hn',
            'procurador_estado' => 'activo',
        ]);

        // Usuarios
        $this->director = Usuario::factory()->create([
            'email' => 'director@test.hn',
            'contrasena' => bcrypt('password'),
            'usuario_estado' => 'activo',
            'rol_id' => 1, // Director
            'debe_cambiar_contrasena' => false,
        ]);

        $userA = Usuario::factory()->create([
            'email' => 'procA@test.hn',
            'contrasena' => bcrypt('password'),
            'usuario_estado' => 'activo',
            'rol_id' => 2, // Procurador
            'procurador_id' => $this->procuradorA->procurador_id,
            'debe_cambiar_contrasena' => false,
        ]);

        $userB = Usuario::factory()->create([
            'email' => 'procB@test.hn',
            'contrasena' => bcrypt('password'),
            'usuario_estado' => 'activo',
            'rol_id' => 2,
            'procurador_id' => $this->procuradorB->procurador_id,
            'debe_cambiar_contrasena' => false,
        ]);

        // Re-cargar relaciones
        $this->procuradorA = $this->procuradorA->fresh();
        $this->procuradorB = $this->procuradorB->fresh();

        // Caso de procurador A
        $this->casoDeA = Caso::create([
            'caso_numero_expediente' => 'EXP-2024-001',
            'cliente_id' => $this->cliente->cliente_id,
            'tipo_tramite_id' => $this->tipoTramite->tipo_tramite_id,
            'procurador_id' => $this->procuradorA->procurador_id,
            'estado_id' => $this->estadoAdmitidoId,
            'caso_parte_representada' => 'Juan Perez',
            'caso_relacion_hechos' => 'Hechos del caso A',
        ]);
    }

    /**
     * Payload base para actualización por Procurador (campos permitidos).
     */
    private function payloadProcurador(array $overrides = []): array
    {
        return array_merge([
            'cliente_id' => $this->casoDeA->cliente_id,
            'tipo_tramite_id' => $this->casoDeA->tipo_tramite_id,
            'caso_parte_representada' => 'Juan Perez Actualizado',
            'caso_relacion_hechos' => 'Hechos actualizados',
            'estado_id' => $this->estadoAdmitidoId,
            'caso_juzgado' => 'J-7',
        ], $overrides);
    }

    // ── C1: Procurador actualiza sus propios casos ────────────────────────────

    /**
     * Test: Procurador puede actualizar campos permitidos de su caso.
     */
    public function test_procurador_puede_actualizar_campos_permitidos_de_su_caso(): void
    {
        $this->actingAsAuthenticated($this->procuradorA->usuario)
            ->from(route('casos.edit', $this->casoDeA->caso_numero_expediente))
            ->put(route('casos.update', $this->casoDeA->caso_numero_expediente), $this->payloadProcurador())
            ->assertRedirect(route('casos.show', $this->casoDeA->caso_numero_expediente));

        $this->assertDatabaseHas('casos', [
            'caso_id' => $this->casoDeA->caso_id,
            'estado_id' => $this->estadoAdmitidoId,
            'caso_juzgado' => 'J-7',
            'caso_parte_representada' => 'Juan Perez Actualizado',
        ]);
    }

    /**
     * Test: Procurador NO puede cambiar procurador ni observaciones del director.
     */
    public function test_procurador_no_puede_cambiar_procurador_ni_observaciones_del_director(): void
    {
        $procuradorOriginal = $this->casoDeA->procurador_id;

        $this->actingAsAuthenticated($this->procuradorA->usuario)
            ->from(route('casos.edit', $this->casoDeA->caso_numero_expediente))
            ->put(route('casos.update', $this->casoDeA->caso_numero_expediente), $this->payloadProcurador([
                'procurador_id' => $this->procuradorB->procurador_id,
                'caso_observaciones_director' => 'Inyección de nota',
                'caso_estado' => 'cerrado',
            ]))
            ->assertRedirect(); // Redirecciona pero ignora campos no permitidos

        $this->casoDeA->refresh();
        $this->assertSame($procuradorOriginal, $this->casoDeA->procurador_id);
        $this->assertNull($this->casoDeA->caso_observaciones_director);
    }

    /**
     * Test: Procurador NO puede actualizar caso ajeno.
     */
    public function test_procurador_no_puede_actualizar_caso_ajeno(): void
    {
        $this->actingAsAuthenticated($this->procuradorB->usuario)
            ->put(route('casos.update', $this->casoDeA->caso_numero_expediente), $this->payloadProcurador())
            ->assertForbidden();
    }

    /**
     * Test: Procurador NO puede ver caso ajeno.
     */
    public function test_procurador_no_puede_ver_caso_ajeno(): void
    {
        $this->actingAsAuthenticated($this->procuradorB->usuario)
            ->get(route('casos.show', $this->casoDeA->caso_numero_expediente))
            ->assertForbidden();
    }

    /**
     * Test: Director PUEDE actualizar todos los campos.
     */
    public function test_director_puede_actualizar_todos_los_campos(): void
    {
        $this->actingAsAuthenticated($this->director)
            ->put(route('casos.update', $this->casoDeA->caso_numero_expediente), $this->payloadProcurador([
                'cliente_id' => $this->casoDeA->cliente_id,
                'tipo_tramite_id' => $this->casoDeA->tipo_tramite_id,
                'procurador_id' => $this->procuradorB->procurador_id,
                'caso_observaciones_director' => 'Nota del director',
                'caso_estado' => 'cerrado',
            ]))
            ->assertRedirect(route('casos.show', $this->casoDeA->caso_numero_expediente));

        $this->assertDatabaseHas('casos', [
            'caso_id' => $this->casoDeA->caso_id,
            'caso_observaciones_director' => 'Nota del director',
            'caso_estado' => 'cerrado',
        ]);
    }

    // ── C4: destroy y reasignar solo Director ────────────────────────────────

    /**
     * Test: Procurador NO puede desactivar ni su propio caso.
     */
    public function test_procurador_no_puede_desactivar_ni_su_propio_caso(): void
    {
        $this->actingAsAuthenticated($this->procuradorA->usuario)
            ->delete(route('casos.destroy', $this->casoDeA->caso_numero_expediente))
            ->assertForbidden();

        $this->assertDatabaseHas('casos', ['caso_id' => $this->casoDeA->caso_id, 'caso_estado' => 'activo']);
    }

    /**
     * Test: Procurador NO puede acceder a reasignación.
     */
    public function test_procurador_no_puede_acceder_a_reasignacion(): void
    {
        $this->actingAsAuthenticated($this->procuradorA->usuario)
            ->get(route('casos.reasignar', $this->casoDeA->caso_numero_expediente))
            ->assertForbidden();
    }

    /**
     * Test: Director PUEDE desactivar caso.
     */
    public function test_director_puede_desactivar_caso(): void
    {
        $this->actingAsAuthenticated($this->director)
            ->delete(route('casos.destroy', $this->casoDeA->caso_numero_expediente))
            ->assertRedirect(route('casos.index'));

        $this->assertDatabaseHas('casos', ['caso_id' => $this->casoDeA->caso_id, 'caso_estado' => 'inactivo']);
    }

    // ── C2: validación alineada al esquema (varchar 50) ──────────────────────

    /**
     * Test: Store rechaza parte_representada > 50 caracteres.
     */
    public function test_store_rechaza_parte_representada_mayor_a_50_caracteres(): void
    {
        $this->actingAsAuthenticated($this->director)
            ->from(route('casos.create'))
            ->post(route('casos.store'), [
                'cliente_id' => $this->casoDeA->cliente_id,
                'tipo_tramite_id' => $this->casoDeA->tipo_tramite_id,
                'procurador_id' => $this->casoDeA->procurador_id,
                'caso_parte_representada' => str_repeat('x', 51),
                'caso_relacion_hechos' => 'Hechos.',
            ])
            ->assertSessionHasErrors('caso_parte_representada');
    }

    /**
     * Test: Update rechaza parte_representada > 50 caracteres.
     */
    public function test_update_rechaza_parte_representada_mayor_a_50_caracteres(): void
    {
        $this->actingAsAuthenticated($this->procuradorA->usuario)
            ->from(route('casos.edit', $this->casoDeA->caso_numero_expediente))
            ->put(route('casos.update', $this->casoDeA->caso_numero_expediente), $this->payloadProcurador([
                'caso_parte_representada' => str_repeat('x', 51),
            ]))
            ->assertSessionHasErrors('caso_parte_representada');
    }
}