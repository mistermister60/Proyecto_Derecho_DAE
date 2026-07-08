<?php

namespace App\Http\Controllers;

use App\Models\Caso;
use App\Models\Entrevista;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Controlador para la gestión de entrevistas dentro de un caso.
 *
 * Permite registrar y eliminar entrevistas asociadas a un caso específico.
 * Estructura similar a AudienciaController. Todas las operaciones requieren
 * autorización 'update' sobre el caso.
 */
class EntrevistaController extends Controller
{
    /**
     * Registra una nueva entrevista para el caso.
     *
     * Valida fecha, relación de hechos y observaciones. Asigna automáticamente
     * el caso, el procurador responsable y establece el estado 'activo'.
     *
     * @param  Request  $request  Datos de la entrevista
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
            'entrevista_fecha' => 'required|date',
            'entrevista_relacion_hechos' => 'required|string',
            'entrevista_observaciones' => 'nullable|string',
        ]);

        $validated['caso_id'] = $caso->caso_id;
        $validated['procurador_id'] = $caso->procurador_id;
        $validated['entrevista_estado'] = 'activo';

        Entrevista::create($validated);

        return back()->with('success', 'Entrevista registrada.');
    }

    /**
     * Elimina una entrevista del caso.
     *
     * Busca la entrevista por ID dentro del caso especificado y la elimina
     * físicamente de la base de datos.
     *
     * @param  string  $expediente  Número de expediente del caso
     * @param  int  $entrevista_id  ID de la entrevista a eliminar
     * @return RedirectResponse Redirección a la página anterior
     *
     * @throws AuthorizationException Si no tiene permiso 'update'
     * @throws ModelNotFoundException Si el caso o la entrevista no existen
     */
    public function destroy(string $expediente, int $entrevista_id)
    {
        $caso = Caso::where('caso_numero_expediente', $expediente)->firstOrFail();
        Gate::authorize('update', $caso);

        $entrevista = Entrevista::where('entrevista_id', $entrevista_id)
            ->where('caso_id', $caso->caso_id)
            ->firstOrFail();

        $entrevista->delete();

        return back()->with('success', 'Entrevista eliminada.');
    }
}
