<?php

namespace App\Http\Controllers;

use App\Models\Caso;
use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
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
 * Almacenamiento: disco PRIVADO 'private' en 'documentos/{caso_id}/'
 * MIMEs permitidos: PDF, DOC, DOCX, JPG, JPEG, PNG (max 10MB)
 */
class DocumentoController extends Controller
{
    /** MIMEs permitidos (por extensión) en la regla de validación del request */
    private const MIMES_PERMITIDOS = 'pdf,doc,docx,jpg,jpeg,png';

    /** MIME types REALES permitidos (verificados con finfo tras la subida) */
    private const MIMES_REALES_PERMITIDOS = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'image/jpeg',
        'image/png',
    ];

    /** MIME types ambiguos que finfo suele devolver para documentos Office válidos */
    private const MIMES_AMBIGUOS = [
        'application/zip',
        'application/x-zip-compressed',
        'application/octet-stream',
        'application/x-ole-storage',
    ];

    /**
     * ═══════════════════════════════════════════════════════
     * store
     * ───────────────────────────────────────────────────────
     * Sube un documento adjunto al caso.
     * Valida MIME y tamaño (max 10MB) por extensión, almacena en el
     * disco PRIVADO y luego verifica el MIME REAL del contenido con
     * finfo. Si el contenido no coincide con un formato permitido,
     * borra el archivo y retorna error 422. Registra metadatos en BD.
     * Respuesta: Redirect back con success.
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

        // ─── [Almacenar archivo en disco PRIVADO] ─────────────────────
        $archivo = $request->file('archivo');
        $ruta = $archivo->store('documentos/'.$caso->caso_id, 'private');

        // ─── [Verificación MIME REAL post-subida] ─────────────────────
        // La regla 'mimes:' valida por extensión; aquí validamos el
        // contenido real con finfo para evitar archivos disfrazados.
        $extension = strtolower($archivo->getClientOriginalExtension());
        if (! $this->contenidoEsPermitido($ruta, $extension)) {
            Storage::disk('private')->delete($ruta);

            $validator = Validator::make([], []);
            $validator->errors()->add(
                'archivo',
                'El contenido del archivo no corresponde a un formato permitido (PDF, DOC, DOCX, JPG, JPEG, PNG).'
            );

            throw new ValidationException($validator);
        }

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
     * Verifica que el MIME real del archivo guardado sea un formato permitido.
     *
     * Usa el MIME detectado por finfo (Storage::disk('private')->mimeType).
     * Los documentos Office (.docx) son archivos zip y finfo a menudo los
     * reporta como application/zip; ante un MIME ambiguo se valida la firma
     * binaria (magic bytes) según la extensión original para no rechazar
     * documentos legítimos.
     *
     * @param  string  $ruta  Ruta relativa en el disco privado
     * @param  string  $extension  Extensión original del archivo (en minúsculas)
     * @return bool True si el contenido corresponde a un formato permitido
     */
    private function contenidoEsPermitido(string $ruta, string $extension): bool
    {
        $mimeReal = Storage::disk('private')->mimeType($ruta);

        if (in_array($mimeReal, self::MIMES_REALES_PERMITIDOS, true)) {
            return true;
        }

        if (! in_array($mimeReal, self::MIMES_AMBIGUOS, true)) {
            return false;
        }

        return $this->firmaBinariaValida(Storage::disk('private')->path($ruta), $extension);
    }

    /**
     * Valida la firma binaria (magic bytes) del archivo según su extensión.
     *
     * @param  string  $rutaCompleta  Ruta absoluta del archivo en el disco privado
     * @param  string  $extension  Extensión original (en minúsculas)
     * @return bool True si la cabecera coincide con el formato esperado
     */
    private function firmaBinariaValida(string $rutaCompleta, string $extension): bool
    {
        $handle = fopen($rutaCompleta, 'rb');
        if ($handle === false) {
            return false;
        }

        $cabecera = (string) fread($handle, 8);
        fclose($handle);

        return match ($extension) {
            'docx' => str_starts_with($cabecera, "PK\x03\x04"),
            'doc' => str_starts_with($cabecera, "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1"),
            default => false,
        };
    }

    /**
     * ═══════════════════════════════════════════════════════
     * download
     * ───────────────────────────────────────────────────────
     * Descarga un documento del caso desde el disco PRIVADO.
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

        // ─── [Verificar existencia en disco privado y descargar] ──────
        abort_unless(Storage::disk('private')->exists($doc->documento_ruta), 404);

        return Storage::disk('private')->download($doc->documento_ruta, $doc->documento_nombre);
    }

    /**
     * ═══════════════════════════════════════════════════════
     * destroy
     * ───────────────────────────────────────────────────────
     * Elimina un documento del caso.
     * Borra archivo del disco privado y registro de BD.
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
        Storage::disk('private')->delete($doc->documento_ruta);
        $doc->delete();

        return back()->with('success', 'Documento eliminado.');
    }
}
