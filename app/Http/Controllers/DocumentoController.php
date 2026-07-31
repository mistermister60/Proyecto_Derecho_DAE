<?php

namespace App\Http\Controllers;

use App\Models\Caso;
use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * ═══════════════════════════════════════════════════════
 * CONTROLADOR: DocumentoController
 * ═══════════════════════════════════════════════════════
 * Gestiona documentos adjuntos a casos (subir, descargar, eliminar).
 * Rutas: POST /casos/{expediente}/documentos, GET /casos/{expediente}/documentos/{documento}/descargar, DELETE /casos/{expediente}/documentos/{documento}
 * Middleware: auth, otp, password.changed
 * Roles: Procurador (dueño del caso), Director
 * Autorización: Gate 'update' (subir/eliminar), 'view' (descargar)
 * Almacenamiento: disco local en 'documentos/{caso_id}/'
 * MIMEs permitidos: PDF, DOC, DOCX, JPG, JPEG, PNG (max 10MB)
 */
class DocumentoController extends Controller
{
    /** MIMEs permitidos para documentos legales */
    private const MIMES_PERMITIDOS = 'pdf,doc,docx,jpg,jpeg,png';

    /**
     * ═══════════════════════════════════════════════════════
     * store
     * ───────────────────────────────────────────────────────
     * Sube un documento adjunto al caso.
     * Valida MIME y tamaño (max 10MB). Almacena en disco local
     * y registra metadatos en BD. Respuesta: Redirect back con success.
     * ═══════════════════════════════════════════════════════
     */
    public function store(Request $request, string $expediente)
    {
        // ─── [Obtener caso y autorizar] ───────────────────────────────
        $caso = Caso::where('caso_numero_expediente', $expediente)->firstOrFail();
        Gate::authorize('update', $caso);

        // ─── [Validar archivo y descripción] ──────────────────────────
        $validated = $request->validate([
            'archivo' => 'required|file|mimes:'.self::MIMES_PERMITIDOS.'|max:10240',
            'documento_descripcion' => 'nullable|string|max:500',
        ]);

        // ─── [Almacenar archivo en disco local] ───────────────────────
        $archivo = $request->file('archivo');
        $ruta = $archivo->store('documentos/'.$caso->caso_id, 'local');

        // ─── [Registrar metadatos en BD] ──────────────────────────────
        Documento::create([
            'caso_id' => $caso->caso_id,
            'documento_nombre' => $archivo->getClientOriginalName(),
            'documento_tipo' => strtoupper($archivo->getClientOriginalExtension()),
            'documento_ruta' => $ruta,
            'documento_tamano' => $archivo->getSize(),
            'documento_descripcion' => $validated['documento_descripcion'] ?? null,
            'documento_estado' => 'activo',
        ]);

        return back()->with('success', 'Documento subido exitosamente.');
    }

    /**
     * ═══════════════════════════════════════════════════════
     * download
     * ───────────────────────────────────────────────────────
     * Descarga un documento del caso.
     * Verifica existencia en disco y retorna BinaryFileResponse
     * con nombre original. Respuesta: descarga del archivo.
     * ═══════════════════════════════════════════════════════
     */
    public function download(string $expediente, int $documento_id)
    {
        // ─── [Obtener caso y autorizar vista] ─────────────────────────
        $caso = Caso::where('caso_numero_expediente', $expediente)->firstOrFail();
        Gate::authorize('view', $caso);

        // ─── [Buscar documento en BD] ─────────────────────────────────
        $doc = Documento::where('documento_id', $documento_id)
            ->where('caso_id', $caso->caso_id)
            ->firstOrFail();

        // ─── [Verificar existencia en disco y descargar] ──────────────
        abort_unless(Storage::disk('local')->exists($doc->documento_ruta), 404);

        return Storage::disk('local')->download($doc->documento_ruta, $doc->documento_nombre);
    }

    /**
     * ═══════════════════════════════════════════════════════
     * destroy
     * ───────────────────────────────────────────────────────
     * Elimina un documento del caso.
     * Borra archivo del disco local y registro de BD.
     * Respuesta: Redirect back con mensaje success.
     * ═══════════════════════════════════════════════════════
     */
    public function destroy(string $expediente, int $documento_id)
    {
        // ─── [Obtener caso y autorizar] ───────────────────────────────
        $caso = Caso::where('caso_numero_expediente', $expediente)->firstOrFail();
        Gate::authorize('update', $caso);

        // ─── [Buscar documento en BD] ─────────────────────────────────
        $doc = Documento::where('documento_id', $documento_id)
            ->where('caso_id', $caso->caso_id)
            ->firstOrFail();

        // ─── [Eliminar archivo y registro] ────────────────────────────
        Storage::disk('local')->delete($doc->documento_ruta);
        $doc->delete();

        return back()->with('success', 'Documento eliminado.');
    }
}
