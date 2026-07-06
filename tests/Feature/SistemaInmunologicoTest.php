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
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

/**
 * Tests de regresión y robustez del sistema (Sistema Inmunológico).
 *
 * Verifica los mecanismos de defensa de la aplicación: cabeceras de seguridad
 * HTTP, registro de auditoría en el canal 'audit', y política de contraseñas
 * seguras (A1). Actúa como un "sistema inmunológico" que detecta regresiones
 * en la seguridad y estabilidad del sistema.
 */
class SistemaInmunologicoTest extends TestCase
{
    use RefreshDatabase;

    // ── Headers de seguridad ───────────────────────────────────────────────

    /**
     * Verifica que las cabeceras de seguridad HTTP estén presentes en las respuestas.
     *
     * Comprueba que la aplicación envíe X-Frame-Options, X-Content-Type-Options,
     * Referrer-Policy y Content-Security-Policy, y que NO se envíe
     * X-XSS-Protection (desaconsejado por OWASP).
     */
    public function test_cabeceras_de_seguridad_presentes(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Content-Security-Policy');
        // X-XSS-Protection desaconsejado por OWASP; no debe enviarse.
        $this->assertNull($response->headers->get('X-XSS-Protection'));
    }

    // ── Canal audit registra creación ──────────────────────────────────────

    /**
     * Verifica que la creación de un caso registre una entrada en el canal de auditoría.
     *
     * Utiliza un mock de Log para interceptar la llamada al canal 'audit'
     * y verifica que se ejecute el método info al crear un caso.
     */
    public function test_creacion_de_caso_escribe_en_audit_log(): void
    {
        $mockLog = \Mockery::mock();
        $mockLog->shouldReceive('info')->once();

        Log::shouldReceive('channel')
            ->with('audit')
            ->once()
            ->andReturn($mockLog);

        $this->crearCasoBase();
    }

    // ── Canal audit registra modificación ─────────────────────────────────

    /**
     * Verifica que la modificación de un caso registre una entrada en el canal de auditoría.
     *
     * Crea un caso base, luego lo modifica y verifica que se registre
     * una entrada info en el canal 'audit'.
     */
    public function test_modificacion_de_caso_escribe_en_audit_log(): void
    {
        $caso = $this->crearCasoBase();

        $mockLog = \Mockery::mock();
        $mockLog->shouldReceive('info')->once();

        Log::shouldReceive('channel')
            ->with('audit')
            ->once()
            ->andReturn($mockLog);

        $caso->update(['caso_juzgado' => 'J-7']);
    }

    // ── Política de contraseñas (A1) ───────────────────────────────────────

    /**
     * Verifica que una contraseña débil sea rechazada al crear un usuario.
     *
     * Failure path: intenta crear un usuario con contraseña 'abc123'
     * (demasiado débil) y espera un error de validación en el campo 'contrasena'.
     */
    public function test_password_debil_es_rechazado_en_crear_usuario(): void
    {
        $rol = Rol::create(['rol_nombre' => 'Director', 'rol_descripcion' => 'Dir', 'rol_estado' => 'activo']);
        $director = Usuario::create([
            'rol_id' => $rol->rol_id, 'procurador_id' => null,
            'usuario_nombre' => 'Dir', 'email' => 'dir@test.hn',
            'contrasena' => Hash::make('Segura1!'), 'usuario_estado' => 'activo',
        ]);

        $this->actingAs($director)
            ->from(route('usuarios.create'))
            ->post(route('usuarios.store'), [
                'rol_id' => $rol->rol_id,
                'usuario_nombre' => 'Nuevo',
                'email' => 'nuevo@test.hn',
                'contrasena' => 'abc123',           // demasiado débil
                'contrasena_confirmation' => 'abc123',
            ])
            ->assertSessionHasErrors('contrasena');
    }

    /**
     * Verifica que una contraseña fuerte sea aceptada al crear un usuario.
     *
     * Happy path: crea un usuario con contraseña que cumple los requisitos
     * (8+ caracteres, mayúscula, número) y espera una redirección exitosa.
     */
    public function test_password_fuerte_es_aceptado(): void
    {
        $rol = Rol::create(['rol_nombre' => 'Director', 'rol_descripcion' => 'Dir', 'rol_estado' => 'activo']);
        $director = Usuario::create([
            'rol_id' => $rol->rol_id, 'procurador_id' => null,
            'usuario_nombre' => 'Dir', 'email' => 'dir@test.hn',
            'contrasena' => Hash::make('Segura1!'), 'usuario_estado' => 'activo',
        ]);

        $this->actingAs($director)
            ->post(route('usuarios.store'), [
                'rol_id' => $rol->rol_id,
                'usuario_nombre' => 'Nuevo',
                'email' => 'nuevo@test.hn',
                'contrasena' => 'Segura8chars',     // cumple: 8+, mayúscula, número
                'contrasena_confirmation' => 'Segura8chars',
            ])
            ->assertRedirect(route('usuarios.index'));
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    /**
     * Crea un caso base con datos mínimos para usar en los tests de auditoría.
     *
     * Crea un tipo de trámite, estado, cliente, procurador y retorna el caso
     * creado con todos los datos básicos.
     *
     * @return Caso Caso creado con datos de prueba.
     */
    private function crearCasoBase(): Caso
    {
        $tramite = TipoTramite::create(['tramite_nombre' => 'Civil', 'tramite_descripcion' => 'x', 'tramite_estado' => 'activo']);
        $estado = EstadoCaso::create(['estado_nombre' => 'Entrevista', 'estado_orden' => 1, 'estado_color' => '#9CA3AF', 'estado_tipo' => 'pipeline']);
        $cliente = Cliente::create([
            'cliente_nombre' => 'Pedro', 'cliente_apellido' => 'Sosa', 'cliente_dni' => '0501199000111',
            'cliente_estado_civil' => 'Soltero', 'cliente_telefono' => '9999-9999',
            'cliente_direccion' => 'SPS', 'cliente_numero_hijos' => 0, 'cliente_estado' => 'activo',
        ]);
        $proc = Procurador::create([
            'procurador_nombre' => 'A', 'procurador_apellido' => 'B', 'procurador_dni' => '0501199100222',
            'procurador_carnet' => 'C-100', 'procurador_fecha_nacimiento' => '1991-01-01',
            'procurador_genero' => 'Masculino', 'procurador_email' => 'a@b.com',
            'procurador_telefono' => '8888-8888', 'procurador_estado' => 'activo',
        ]);

        return Caso::create([
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
    }
}
