<?php

namespace App\Http\Controllers;

use App\Models\Caso;
use App\Models\Documento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class DocumentoController extends Controller
{
    /** MIMEs permitidos para documentos legales */
    private const MIMES_PERMITIDOS = 'pdf,doc,docx,jpg,jpeg,png';

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
