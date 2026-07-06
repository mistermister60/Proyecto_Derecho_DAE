<?php

namespace App\Http\Controllers;

use App\Models\Caso;
use App\Models\Documento;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controlador para la gestión de documentos adjuntos a casos.
 *
 * Permite la carga, descarga y eliminación de documentos legales asociados
 * a un expediente. Los archivos se almacenan en disco local y solo se
 * permiten formatos PDF y de imagen. Las operaciones requieren autorización
 * 'update' (subir/eliminar) o 'view' (descargar) sobre el caso.
 */
class DocumentoController extends Controller
{
    /** MIMEs permitidos para documentos legales */
    private const MIMES_PERMITIDOS = 'pdf,doc,docx,jpg,jpeg,png';

    /**
     * Sube un documento adjunto al caso.
     *
     * Valida que el archivo cumpla con los MIME permitidos y un tamaño
     * máximo de 10 MB. Almacena el archivo en disco local bajo
     * 'documentos/{caso_id}/' y registra los metadatos en la BD.
     *
     * @param  Request  $request  Archivo y descripción opcional
     * @param  string  $expediente  Número de expediente del caso
     * @return RedirectResponse Redirección a la página anterior
     *
     * @throws AuthorizationException Si no tiene permiso 'update'
     * @throws ModelNotFoundException Si el caso no existe
     */
    public function store(Request $request, string $expediente)
    {
        $caso = Caso::where('caso_numero_expediente', $expediente)->firstOrFail();
        Gate::authorize('update', $caso);

        $validated = $request->validate([
            'archivo' => 'required|file|mimes:'.self::MIMES_PERMITIDOS.'|max:10240',
            'documento_descripcion' => 'nullable|string|max:500',
        ]);

        $archivo = $request->file('archivo');
        $ruta = $archivo->store('documentos/'.$caso->caso_id, 'local');

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
     * Descarga un documento del caso.
     *
     * Verifica que el archivo exista en disco local y devuelve la respuesta
     * de descarga con el nombre original del documento.
     *
     * @param  string  $expediente  Número de expediente del caso
     * @param  int  $documento_id  ID del documento a descargar
     * @return BinaryFileResponse Descarga del archivo
     *
     * @throws AuthorizationException Si no tiene permiso 'view'
     * @throws ModelNotFoundException Si el caso o el documento no existen
     * @throws NotFoundHttpException Si el archivo no existe en disco
     */
    public function download(string $expediente, int $documento_id)
    {
        $caso = Caso::where('caso_numero_expediente', $expediente)->firstOrFail();
        Gate::authorize('view', $caso);

        $doc = Documento::where('documento_id', $documento_id)
            ->where('caso_id', $caso->caso_id)
            ->firstOrFail();

        abort_unless(Storage::disk('local')->exists($doc->documento_ruta), 404);

        return Storage::disk('local')->download($doc->documento_ruta, $doc->documento_nombre);
    }

    /**
     * Elimina un documento del caso.
     *
     * Borra el archivo del disco local y elimina el registro de la BD.
     *
     * @param  string  $expediente  Número de expediente del caso
     * @param  int  $documento_id  ID del documento a eliminar
     * @return RedirectResponse Redirección a la página anterior
     *
     * @throws AuthorizationException Si no tiene permiso 'update'
     * @throws ModelNotFoundException Si el caso o el documento no existen
     */
    public function destroy(string $expediente, int $documento_id)
    {
        $caso = Caso::where('caso_numero_expediente', $expediente)->firstOrFail();
        Gate::authorize('update', $caso);

        $doc = Documento::where('documento_id', $documento_id)
            ->where('caso_id', $caso->caso_id)
            ->firstOrFail();

        Storage::disk('local')->delete($doc->documento_ruta);
        $doc->delete();

        return back()->with('success', 'Documento eliminado.');
    }
}
