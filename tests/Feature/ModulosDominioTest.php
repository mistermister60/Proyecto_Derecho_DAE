<?php

namespace Tests\Feature;

use App\Models\Audiencia;
use App\Models\Caso;
use App\Models\Cliente;
use App\Models\Documento;
use App\Models\Entrevista;
use App\Models\EstadoCaso;
use App\Models\Procurador;
use App\Models\Rol;
use App\Models\TipoTramite;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Tests de integridad de los módulos del dominio.
 *
 * Verifica que los módulos principales (Audiencias, Documentos, Entrevistas)
 * funcionen correctamente respetando las reglas de negocio: asignación de
 * procuradores, permisos por rol y operaciones CRUD básicas.
 */
class ModulosDominioTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Usuario con rol Director usado en los escenarios de prueba.
     */
    private Usuario $director;

    /**
     * Usuario con rol Procurador asignado al caso de prueba.
     */
    private Usuario $procuradorA;

    /**
     * Usuario con rol Procurador NO asignado al caso (caso ajeno).
     */
    private Usuario $procuradorB;

    /**
     * Caso de prueba creado durante setUp.
     */
    private Caso $caso;

    /**
     * Configuración inicial de cada test.
     *
     * Crea los roles Director y Procurador, un estado de caso, un tipo de trámite,
     * un cliente, dos procuradores, los usuarios correspondientes y un caso
     * asignado al procuradorA.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $rolDir = Rol::create(['rol_nombre' => 'Director',   'rol_descripcion' => 'D', 'rol_estado' => 'activo']);
        $rolProc = Rol::create(['rol_nombre' => 'Procurador', 'rol_descripcion' => 'P', 'rol_estado' => 'activo']);

        $estado = EstadoCaso::create(['estado_nombre' => 'Entrevista', 'estado_orden' => 1, 'estado_color' => '#9CA3AF', 'estado_tipo' => 'pipeline']);
        $tramite = TipoTramite::create(['tramite_nombre' => 'Civil', 'tramite_descripcion' => 'x', 'tramite_estado' => 'activo']);
        $cliente = Cliente::create([
            'cliente_nombre' => 'Ana', 'cliente_apellido' => 'Paz', 'cliente_dni' => '0501199900011',
            'cliente_estado_civil' => 'Soltera', 'cliente_telefono' => '9000-0001',
            'cliente_direccion' => 'SPS', 'cliente_numero_hijos' => 0, 'cliente_estado' => 'activo',
        ]);

        $procA = Procurador::create([
            'procurador_nombre' => 'A', 'procurador_apellido' => 'Uno', 'procurador_dni' => '0501199800001',
            'procurador_carnet' => 'PA-01', 'procurador_fecha_nacimiento' => '1998-01-01',
            'procurador_genero' => 'Masculino', 'procurador_email' => 'a@test.hn',
            'procurador_telefono' => '1111-1111', 'procurador_estado' => 'activo',
        ]);
        $procB = Procurador::create([
            'procurador_nombre' => 'B', 'procurador_apellido' => 'Dos', 'procurador_dni' => '0501199800002',
            'procurador_carnet' => 'PB-02', 'procurador_fecha_nacimiento' => '1998-02-02',
            'procurador_genero' => 'Femenino', 'procurador_email' => 'b@test.hn',
            'procurador_telefono' => '2222-2222', 'procurador_estado' => 'activo',
        ]);

        $this->director = Usuario::create([
            'rol_id' => $rolDir->rol_id, 'procurador_id' => null,
            'usuario_nombre' => 'Director', 'email' => 'dir@test.hn',
            'contrasena' => Hash::make('Segura8!'), 'usuario_estado' => 'activo',
            'debe_cambiar_contrasena' => false,
        ]);
        $this->procuradorA = Usuario::create([
            'rol_id' => $rolProc->rol_id, 'procurador_id' => $procA->procurador_id,
            'usuario_nombre' => 'ProcA', 'email' => 'proca@test.hn',
            'contrasena' => Hash::make('Segura8!'), 'usuario_estado' => 'activo',
            'debe_cambiar_contrasena' => false,
        ]);
        $this->procuradorB = Usuario::create([
            'rol_id' => $rolProc->rol_id, 'procurador_id' => $procB->procurador_id,
            'usuario_nombre' => 'ProcB', 'email' => 'procb@test.hn',
            'contrasena' => Hash::make('Segura8!'), 'usuario_estado' => 'activo',
            'debe_cambiar_contrasena' => false,
        ]);

        $this->caso = Caso::create([
            'caso_numero_expediente' => '0501-2026-00001',
            'cliente_id' => $cliente->cliente_id,
            'tipo_tramite_id' => $tramite->tipo_tramite_id,
            'estado_id' => $estado->estado_id,
            'procurador_id' => $procA->procurador_id,
            'caso_parte_representada' => 'Demandante',
            'caso_relacion_hechos' => 'Hechos.',
            'caso_admisible' => true,
            'caso_estado' => 'activo',
        ]);
    }

    // ── Audiencias ────────────────────────────────────────────────────────

    /**
     * Verifica que un procurador pueda agendar una audiencia en su propio caso.
     *
     * Happy path: el procurador asignado al caso crea una audiencia
     * y se espera una redirección con los datos persistidos en la base de datos.
     */
    public function test_procurador_puede_agendar_audiencia_en_su_caso(): void
    {
        $this->actingAsAuthenticated($this->procuradorA)
            ->post(route('audiencias.store', $this->caso->caso_numero_expediente), [
                'audiencia_fecha' => '2026-08-15',
                'audiencia_tipo' => 'Audiencia inicial',
                'audiencia_juzgado' => 'J-7',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('audiencias', [
            'caso_id' => $this->caso->caso_id,
            'audiencia_tipo' => 'Audiencia inicial',
        ]);
    }

    /**
     * Verifica que un procurador NO pueda agendar una audiencia en un caso ajeno.
     *
     * Failure path: el procuradorB no está asignado al caso,
     * por lo que la solicitud debe ser rechazada con HTTP 403.
     */
    public function test_procurador_no_puede_agendar_audiencia_en_caso_ajeno(): void
    {
        $this->actingAsAuthenticated($this->procuradorB)
            ->post(route('audiencias.store', $this->caso->caso_numero_expediente), [
                'audiencia_fecha' => '2026-08-15',
                'audiencia_tipo' => 'Audiencia',
            ])
            ->assertForbidden();
    }

    /**
     * Verifica que el Director pueda eliminar cualquier audiencia.
     *
     * Happy path: el Director tiene permiso para eliminar audiencias
     * independientemente del procurador asignado al caso.
     */
    public function test_director_puede_eliminar_audiencia(): void
    {
        $audiencia = Audiencia::create([
            'caso_id' => $this->caso->caso_id,
            'procurador_id' => $this->caso->procurador_id,
            'audiencia_fecha' => '2026-08-15',
            'audiencia_tipo' => 'Audiencia inicial',
            'audiencia_estado' => 'pendiente',
        ]);

        $this->actingAsAuthenticated($this->director)
            ->delete(route('audiencias.destroy', [$this->caso->caso_numero_expediente, $audiencia->audiencia_id]))
            ->assertRedirect();

        $this->assertDatabaseMissing('audiencias', ['audiencia_id' => $audiencia->audiencia_id]);
    }

    // ── Documentos ────────────────────────────────────────────────────────

    /**
     * Verifica que un procurador pueda subir un documento a su caso.
     *
     * Happy path: sube un archivo PDF válido con descripción
     * y verifica que se persista en la base de datos.
     */
    public function test_procurador_puede_subir_documento_a_su_caso(): void
    {
        Storage::fake('local');

        $this->actingAsAuthenticated($this->procuradorA)
            ->post(route('documentos.store', $this->caso->caso_numero_expediente), [
                'archivo' => UploadedFile::fake()->create('poder.pdf', 100, 'application/pdf'),
                'documento_descripcion' => 'Poder notarial',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('documentos', [
            'caso_id' => $this->caso->caso_id,
            'documento_nombre' => 'poder.pdf',
            'documento_descripcion' => 'Poder notarial',
        ]);
    }

    /**
     * Verifica que se rechacen archivos con tipo MIME no permitido.
     *
     * Failure path: intenta subir un archivo .exe (application/octet-stream)
     * y espera que la validación falle con un error en el campo 'archivo'.
     */
    public function test_documento_rechaza_mime_no_permitido(): void
    {
        Storage::fake('local');

        $this->actingAsAuthenticated($this->procuradorA)
            ->from(route('casos.show', $this->caso->caso_numero_expediente))
            ->post(route('documentos.store', $this->caso->caso_numero_expediente), [
                'archivo' => UploadedFile::fake()->create('script.exe', 10, 'application/octet-stream'),
            ])
            ->assertSessionHasErrors('archivo');
    }

    /**
     * Verifica que un procurador NO pueda subir documentos a un caso ajeno.
     *
     * Failure path: el procuradorB intenta subir un documento al caso de procuradorA
     * y la solicitud debe ser rechazada con HTTP 403.
     */
    public function test_procurador_no_puede_subir_documento_a_caso_ajeno(): void
    {
        Storage::fake('local');

        $this->actingAsAuthenticated($this->procuradorB)
            ->post(route('documentos.store', $this->caso->caso_numero_expediente), [
                'archivo' => UploadedFile::fake()->create('doc.pdf', 100, 'application/pdf'),
            ])
            ->assertForbidden();
    }

    /**
     * Verifica que al eliminar un documento también se borre el archivo del disco.
     *
     * Happy path: crea un documento con archivo en disco, lo elimina como Director
     * y verifica que tanto el registro como el archivo físico desaparezcan.
     */
    public function test_eliminar_documento_borra_el_archivo_del_disco(): void
    {
        Storage::fake('local');

        $archivo = UploadedFile::fake()->create('contrato.pdf', 50, 'application/pdf');
        $ruta = $archivo->store('documentos/'.$this->caso->caso_id, 'local');

        $doc = Documento::create([
            'caso_id' => $this->caso->caso_id,
            'documento_nombre' => 'contrato.pdf',
            'documento_tipo' => 'PDF',
            'documento_ruta' => $ruta,
            'documento_tamano' => 50 * 1024,
            'documento_estado' => 'activo',
        ]);

        $this->actingAsAuthenticated($this->director)
            ->delete(route('documentos.destroy', [$this->caso->caso_numero_expediente, $doc->documento_id]))
            ->assertRedirect();

        Storage::disk('local')->assertMissing($ruta);
        $this->assertDatabaseMissing('documentos', ['documento_id' => $doc->documento_id]);
    }

    // ── Entrevistas ──────────────────────────────────────────────────────

    /**
     * Verifica que un procurador pueda registrar una entrevista en su caso.
     *
     * Happy path: crea una entrevista con fecha y relación de hechos,
     * espera redirección y persistencia en la base de datos.
     */
    public function test_procurador_puede_registrar_entrevista_en_su_caso(): void
    {
        $this->actingAsAuthenticated($this->procuradorA)
            ->post(route('entrevistas.store', $this->caso->caso_numero_expediente), [
                'entrevista_fecha' => '2026-07-10',
                'entrevista_relacion_hechos' => 'Cliente relata separación de hecho desde 2024.',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('entrevistas', [
            'caso_id' => $this->caso->caso_id,
            'entrevista_relacion_hechos' => 'Cliente relata separación de hecho desde 2024.',
        ]);
    }

    /**
     * Verifica que un procurador NO pueda registrar entrevistas en un caso ajeno.
     *
     * Failure path: el procuradorB intenta registrar una entrevista
     * en el caso de procuradorA y recibe HTTP 403.
     */
    public function test_procurador_no_puede_registrar_entrevista_en_caso_ajeno(): void
    {
        $this->actingAsAuthenticated($this->procuradorB)
            ->post(route('entrevistas.store', $this->caso->caso_numero_expediente), [
                'entrevista_fecha' => '2026-07-10',
                'entrevista_relacion_hechos' => 'Hechos.',
            ])
            ->assertForbidden();
    }

    /**
     * Verifica que el Director pueda eliminar cualquier entrevista.
     *
     * Happy path: el Director elimina una entrevista existente
     * y verifica que desaparezca de la base de datos.
     */
    public function test_director_puede_eliminar_entrevista(): void
    {
        $entrevista = Entrevista::create([
            'caso_id' => $this->caso->caso_id,
            'procurador_id' => $this->caso->procurador_id,
            'entrevista_fecha' => '2026-07-10',
            'entrevista_relacion_hechos' => 'Hechos.',
            'entrevista_estado' => 'activo',
        ]);

        $this->actingAsAuthenticated($this->director)
            ->delete(route('entrevistas.destroy', [$this->caso->caso_numero_expediente, $entrevista->entrevista_id]))
            ->assertRedirect();

        $this->assertDatabaseMissing('entrevistas', ['entrevista_id' => $entrevista->entrevista_id]);
    }
}