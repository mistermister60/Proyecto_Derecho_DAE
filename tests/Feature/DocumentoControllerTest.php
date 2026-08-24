<?php

namespace Tests\Feature;

use App\Models\Caso;
use App\Models\Documento;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * ═══════════════════════════════════════════════════════
 * TEST: DocumentoController::store
 * ═══════════════════════════════════════════════════════
 * - Rechaza archivos con extensiones no permitidas (.exe) con error de validación.
 * - Acepta un PDF válido y lo guarda en el disco privado ('private').
 */
class DocumentoControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Rol::factory()->create(['rol_id' => 1, 'rol_nombre' => 'Director']);
        Storage::fake('private');
    }

    /**
     * Crea un usuario Director (autorizado para actualizar cualquier caso).
     */
    private function director(): Usuario
    {
        return Usuario::factory()->create(['rol_id' => 1]);
    }

    #[Test]
    public function rechaza_archivo_con_extension_no_permitida(): void
    {
        $caso = Caso::factory()->create();
        $archivo = UploadedFile::fake()->create('malware.exe', 100, 'application/octet-stream');

        $response = $this->actingAsAuthenticated($this->director())
            ->post(route('documentos.store', $caso->caso_numero_expediente), [
                'archivo' => $archivo,
            ]);

        // Las rutas web devuelven 302 + errores en sesión (no 422).
        $response->assertSessionHasErrors('archivo');
        $this->assertDatabaseCount('documentos', 0);
    }

    #[Test]
    public function acepta_pdf_y_lo_guarda_en_disco_private(): void
    {
        $caso = Caso::factory()->create();

        // PDF mínimo pero válido (finfo lo detecta como application/pdf).
        $pdf = "%PDF-1.4\n1 0 obj<</Type/Catalog>>endobj\ntrailer<</Root 1 0 R>>\n%%EOF";
        $archivo = UploadedFile::fake()->createWithContent('documento.pdf', $pdf);

        $response = $this->actingAsAuthenticated($this->director())
            ->post(route('documentos.store', $caso->caso_numero_expediente), [
                'archivo' => $archivo,
                'documento_descripcion' => 'Contrato',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('documentos', [
            'caso_id' => $caso->caso_id,
            'documento_tipo' => 'PDF',
            'documento_descripcion' => 'Contrato',
        ]);

        $documento = Documento::where('caso_id', $caso->caso_id)->first();
        Storage::disk('private')->assertExists($documento->documento_ruta);
    }
}
