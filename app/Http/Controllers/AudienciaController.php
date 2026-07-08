<?php

namespace App\Http\Controllers;

use App\Models\Audiencia;
use App\Models\Caso;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * Controlador para la gestión de audiencias dentro de un caso.
 *
 * Permite crear y eliminar audiencias asociadas a un caso específico.
 * Todas las operaciones requieren autorización 'update' sobre el caso.
 */
class AudienciaController extends Controller
{
    /**
     * Registra una nueva audiencia para el caso.
     *
     * Valida los datos de la audiencia (fecha, hora, tipo, juzgado,
     * observaciones), asigna automáticamente el caso y el procurador
     * responsable, y establece el estado inicial como 'pendiente'.
     *
     * @param  Request  $request  Datos de la audiencia
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
            'audiencia_fecha' => 'required|date',
            'audiencia_hora' => 'nullable|date_format:H:i',
            'audiencia_tipo' => 'required|string|max:100',
            'audiencia_juzgado' => 'nullable|string|max:50',
            'audiencia_observaciones' => 'nullable|string',
        ]);

        $validated['caso_id'] = $caso->caso_id;
        $validated['procurador_id'] = $caso->procurador_id;
        $validated['audiencia_estado'] = 'pendiente';

        Audiencia::create($validated);

        return back()->with('success', 'Audiencia agendada.');
    }

    /**
     * Elimina una audiencia del caso.
     *
     * Busca la audiencia por ID dentro del caso especificado y la elimina
     * físicamente de la base de datos.
     *
     * @param  string  $expediente  Número de expediente del caso
     * @param  int  $audiencia_id  ID de la audiencia a eliminar
     * @return RedirectResponse Redirección a la página anterior
     *
     * @throws AuthorizationException Si no tiene permiso 'update'
     * @throws ModelNotFoundException Si el caso o la audiencia no existen
     */
    public function destroy(string $expediente, int $audiencia_id)
    {
        $caso = Caso::where('caso_numero_expediente', $expediente)->firstOrFail();
        Gate::authorize('update', $caso);

        $audiencia = Audiencia::where('audiencia_id', $audiencia_id)
            ->where('caso_id', $caso->caso_id)
            ->firstOrFail();

        $audiencia->delete();

        return back()->with('success', 'Audiencia eliminada.');
    }
}
