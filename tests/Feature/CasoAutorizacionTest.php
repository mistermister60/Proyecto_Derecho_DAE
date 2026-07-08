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
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Tests de autorización y permisos del módulo de Casos.
 *
 * Verifica las reglas de negocio para la autorización de casos:
 * C1 - escalación de privilegios en actualización,
 * C2 - validación de longitud máxima de campos (varchar 50),
 * C4 - restricción de destroy y reasignación solo para Director.
 * Asegura que procuradores solo puedan modificar sus propios casos
 * y que el Director tenga acceso completo.
 */
class CasoAutorizacionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Usuario con rol Director para pruebas de permisos elevados.
     */
    private Usuario $director;

    /**
     * Usuario con rol Procurador asignado al caso de prueba.
     */
    private Usuario $procuradorA;

    /**
     * Usuario con rol Procurador NO asignado al caso (usuario ajeno).
     */
    private Usuario $procuradorB;

    /**
     * Caso de prueba asignado al procuradorA.
     */
    private Caso $casoDeA;

    /**
     * ID del estado "Entrevista" creado durante setUp.
     */
    private int $estadoEntrevistaId;

    /**
     * ID del estado "Admitido" creado durante setUp.
     */
    private int $estadoAdmitidoId;

    /**
     * Configuración inicial de cada test.
     *
     * Crea roles, estados de caso, trámite, cliente, procuradores,
     * usuarios y un caso asignado al procuradorA.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $rolDirector = Rol::create(['rol_nombre' => 'Director', 'rol_descripcion' => 'Director', 'rol_estado' => 'activo']);
        $rolProcurador = Rol::create(['rol_nombre' => 'Procurador', 'rol_descripcion' => 'Procurador', 'rol_estado' => 'activo']);

        $this->estadoEntrevistaId = EstadoCaso::create(['estado_nombre' => 'Entrevista', 'estado_orden' => 1, 'estado_color' => '#9CA3AF', 'estado_tipo' => 'pipeline'])->estado_id;
        $this->estadoAdmitidoId = EstadoCaso::create(['estado_nombre' => 'Admitido', 'estado_orden' => 2, 'estado_color' => '#60A5FA', 'estado_tipo' => 'pipeline'])->estado_id;

        $tramite = TipoTramite::create(['tramite_nombre' => 'Divorcio', 'tramite_descripcion' => 'x', 'tramite_estado' => 'activo']);

        $cliente = Cliente::create([
            'cliente_nombre' => 'Ana', 'cliente_apellido' => 'García', 'cliente_dni' => '0501199900011',
            'cliente_estado_civil' => 'Soltero', 'cliente_telefono' => '9999-0000',
            'cliente_direccion' => 'SPS', 'cliente_numero_hijos' => 0, 'cliente_estado' => 'activo',
        ]);
        $procA = Procurador::create([
            'procurador_nombre' => 'Proc', 'procurador_apellido' => 'A',
            'procurador_dni' => '0501199800001', 'procurador_carnet' => 'PA-001',
            'procurador_fecha_nacimiento' => '1998-01-01', 'procurador_genero' => 'Masculino',
            'procurador_email' => 'proc.a@test.hn', 'procurador_telefono' => '1111-1111',
            'procurador_estado' => 'activo',
        ]);
        $procB = Procurador::create([
            'procurador_nombre' => 'Proc', 'procurador_apellido' => 'B',
            'procurador_dni' => '0501199800002', 'procurador_carnet' => 'PB-002',
            'procurador_fecha_nacimiento' => '1998-02-02', 'procurador_genero' => 'Femenino',
            'procurador_email' => 'proc.b@test.hn', 'procurador_telefono' => '2222-2222',
            'procurador_estado' => 'activo',
        ]);

        $this->director = Usuario::create([
            'rol_id' => $rolDirector->rol_id, 'procurador_id' => null,
            'usuario_nombre' => 'Director', 'email' => 'director@test.hn',
            'contrasena' => Hash::make('secret123'), 'usuario_estado' => 'activo',
        ]);
        $this->procuradorA = Usuario::create([
            'rol_id' => $rolProcurador->rol_id, 'procurador_id' => $procA->procurador_id,
            'usuario_nombre' => 'Usuario A', 'email' => 'a@test.hn',
            'contrasena' => Hash::make('secret123'), 'usuario_estado' => 'activo',
        ]);
        $this->procuradorB = Usuario::create([
            'rol_id' => $rolProcurador->rol_id, 'procurador_id' => $procB->procurador_id,
            'usuario_nombre' => 'Usuario B', 'email' => 'b@test.hn',
            'contrasena' => Hash::make('secret123'), 'usuario_estado' => 'activo',
        ]);

        $this->casoDeA = Caso::create([
            'caso_numero_expediente' => '0501-2026-00001',
            'cliente_id' => $cliente->cliente_id,
            'tipo_tramite_id' => $tramite->tipo_tramite_id,
            'estado_id' => $this->estadoEntrevistaId,
            'procurador_id' => $procA->procurador_id,
            'caso_parte_representada' => 'Demandante',
            'caso_relacion_hechos' => 'Hechos iniciales.',
            'caso_admisible' => true,
            'caso_estado' => 'activo',
        ]);
    }

    /**
     Genera el payload estándar que un procurador enviaría al actualizar un caso.
     *
     * Permite fusionar datos adicionales para probar escenarios específicos
     * como inyección de campos restringidos.
     *
     * @param  array  $extra  Datos adicionales para fusionar con el payload base.
     * @return array Payload combinado para la solicitud de actualización.
     */
    private function payloadProcurador(array $extra = []): array
    {
        return array_merge([
            'estado_id' => $this->estadoAdmitidoId,
            'caso_parte_representada' => 'Demandante',
            'caso_juzgado' => 'J-7',
            'caso_relacion_hechos' => 'Hechos actualizados por procurador.',
        ], $extra);
    }

    // ── C1: escalación de privilegios en update ──────────────────────────────

    /**
     * Verifica que un procurador pueda actualizar los campos permitidos de su caso.
     *
     * Happy path: el procuradorA actualiza estado, juzgado y relación de hechos
     * de su caso asignado, esperando una redirección exitosa.
     */
    public function test_procurador_puede_actualizar_campos_permitidos_de_su_caso(): void
    {
        $response = $this->actingAs($this->procuradorA)
            ->put(route('casos.update', $this->casoDeA->caso_numero_expediente), $this->payloadProcurador());

        $response->assertRedirect(route('casos.show', $this->casoDeA->caso_numero_expediente));
        $this->assertDatabaseHas('casos', [
            'caso_id' => $this->casoDeA->caso_id,
            'estado_id' => $this->estadoAdmitidoId,
            'caso_juzgado' => 'J-7',
        ]);
    }

    /**
     * Verifica que un procurador NO pueda cambiar el procurador asignado
     * ni modificar las observaciones del director ni el estado del caso.
     *
     * Security test: intenta escalar privilegios enviando campos restringidos
     * y verifica que los valores originales se conserven.
     */
    public function test_procurador_no_puede_cambiar_procurador_ni_observaciones_del_director(): void
    {
        $procuradorOriginal = $this->casoDeA->procurador_id;

        $this->actingAs($this->procuradorA)
            ->put(route('casos.update', $this->casoDeA->caso_numero_expediente), $this->payloadProcurador([
                'procurador_id' => $this->procuradorB->procurador_id,
                'caso_observaciones_director' => 'Inyección de nota',
                'caso_estado' => 'cerrado',
            ]))
            ->assertRedirect();

        $this->casoDeA->refresh();
        $this->assertSame($procuradorOriginal, $this->casoDeA->procurador_id);
        $this->assertNull($this->casoDeA->caso_observaciones_director);
        $this->assertSame('activo', $this->casoDeA->caso_estado);
    }

    /**
     * Verifica que un procurador NO pueda actualizar un caso que no le pertenece.
     *
     * Failure path: el procuradorB intenta actualizar el caso de procuradorA
     * y recibe un error HTTP 403 Forbidden.
     */
    public function test_procurador_no_puede_actualizar_caso_ajeno(): void
    {
        $this->actingAs($this->procuradorB)
            ->put(route('casos.update', $this->casoDeA->caso_numero_expediente), $this->payloadProcurador())
            ->assertForbidden();
    }

    /**
     * Verifica que un procurador NO pueda ver los detalles de un caso ajeno.
     *
     * Failure path: el procuradorB intenta acceder al show del caso de procuradorA
     * y recibe un error HTTP 403 Forbidden.
     */
    public function test_procurador_no_puede_ver_caso_ajeno(): void
    {
        $this->actingAs($this->procuradorB)
            ->get(route('casos.show', $this->casoDeA->caso_numero_expediente))
            ->assertForbidden();
    }

    /**
     * Verifica que el Director pueda actualizar todos los campos de cualquier caso.
     *
     * Happy path: el Director modifica procurador asignado, observaciones
     * y estado del caso, esperando que todos los cambios se persistan.
     */
    public function test_director_puede_actualizar_todos_los_campos(): void
    {
        $this->actingAs($this->director)
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
     * Verifica que un procurador NO pueda desactivar ni su propio caso.
     *
     * Failure path: el procuradorA intenta eliminar (desactivar) su propio caso
     * y recibe HTTP 403, verificando que el caso permanezca activo.
     */
    public function test_procurador_no_puede_desactivar_ni_su_propio_caso(): void
    {
        $this->actingAs($this->procuradorA)
            ->delete(route('casos.destroy', $this->casoDeA->caso_numero_expediente))
            ->assertForbidden();

        $this->assertDatabaseHas('casos', ['caso_id' => $this->casoDeA->caso_id, 'caso_estado' => 'activo']);
    }

    /**
     * Verifica que un procurador NO pueda acceder a la página de reasignación.
     *
     * Failure path: el procuradorA intenta acceder a la ruta de reasignar
     * su caso y recibe HTTP 403 Forbidden.
     */
    public function test_procurador_no_puede_acceder_a_reasignacion(): void
    {
        $this->actingAs($this->procuradorA)
            ->get(route('casos.reasignar', $this->casoDeA->caso_numero_expediente))
            ->assertForbidden();
    }

    /**
     * Verifica que el Director pueda desactivar (eliminar) un caso.
     *
     * Happy path: el Director elimina el caso y verifica que su estado
     * cambie a 'inactivo'.
     */
    public function test_director_puede_desactivar_caso(): void
    {
        $this->actingAs($this->director)
            ->delete(route('casos.destroy', $this->casoDeA->caso_numero_expediente))
            ->assertRedirect(route('casos.index'));

        $this->assertDatabaseHas('casos', ['caso_id' => $this->casoDeA->caso_id, 'caso_estado' => 'inactivo']);
    }

    // ── C2: validación alineada al esquema (varchar 50) ──────────────────────

    /**
     * Verifica que la creación de un caso rechace 'parte_representada' con más de 50 caracteres.
     *
     * Failure path: envía un valor de 51 caracteres en el campo
     * 'caso_parte_representada' y espera un error de validación.
     */
    public function test_store_rechaza_parte_representada_mayor_a_50_caracteres(): void
    {
        $this->actingAs($this->director)
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
     * Verifica que la actualización de un caso rechace 'parte_representada' con más de 50 caracteres.
     *
     * Failure path: intenta actualizar con un valor de 51 caracteres
     * y espera un error de validación en 'caso_parte_representada'.
     */
    public function test_update_rechaza_parte_representada_mayor_a_50_caracteres(): void
    {
        $this->actingAs($this->procuradorA)
            ->from(route('casos.edit', $this->casoDeA->caso_numero_expediente))
            ->put(route('casos.update', $this->casoDeA->caso_numero_expediente), $this->payloadProcurador([
                'caso_parte_representada' => str_repeat('x', 51),
            ]))
            ->assertSessionHasErrors('caso_parte_representada');
    }
}
