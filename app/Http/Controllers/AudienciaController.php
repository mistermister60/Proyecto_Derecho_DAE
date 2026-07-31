<?php

namespace App\Http\Controllers;

use App\Models\Audiencia;
use App\Models\Caso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * ═══════════════════════════════════════════════════════
 * CONTROLADOR: AudienciaController
 * ═══════════════════════════════════════════════════════
 * Gestiona audiencias dentro de un caso (crear, eliminar).
 * Rutas: POST /casos/{expediente}/audiencias, DELETE /casos/{expediente}/audiencias/{audiencia}
 * Middleware: auth, otp, password.changed
 * Roles: Procurador (dueño del caso), Director
 * Autorización: Gate 'update' sobre el Caso
 */
class AudienciaController extends Controller
{
    /**
     * ═══════════════════════════════════════════════════════
     * store
     * ───────────────────────────────────────────────────────
     * Registra una nueva audiencia para el caso.
     * Valida fecha, hora, tipo, juzgado, observaciones.
     * Asigna caso_id, procurador_id y estado 'pendiente'.
     * Respuesta: Redirect back con mensaje success.
     * ═══════════════════════════════════════════════════════
     */
    public function store(Request $request, string $expediente)
    {
        // ─── [Obtener caso y autorizar] ───────────────────────────────
        $caso = Caso::where('caso_numero_expediente', $expediente)->firstOrFail();
        Gate::authorize('update', $caso);

        // ─── [Validar datos de entrada] ───────────────────────────────
        $validated = $request->validate([
            'audiencia_fecha' => 'required|date',
            'audiencia_hora' => 'nullable|date_format:H:i',
            'audiencia_tipo' => 'required|string|max:100',
            'audiencia_juzgado' => 'nullable|string|max:50',
            'audiencia_observaciones' => 'nullable|string',
        ]);

        // ─── [Asignar relaciones y estado inicial] ────────────────────
        $validated['caso_id'] = $caso->caso_id;
        $validated['procurador_id'] = $caso->procurador_id;
        $validated['audiencia_estado'] = 'pendiente';

        // ─── [Crear audiencia y responder] ────────────────────────────
        Audiencia::create($validated);

        return back()->with('success', 'Audiencia agendada.');
    }

    /**
     * ═══════════════════════════════════════════════════════
     * destroy
     * ───────────────────────────────────────────────────────
     * Elimina una audiencia del caso.
     * Busca por ID dentro del caso y elimina físicamente.
     * Respuesta: Redirect back con mensaje success.
     * ═══════════════════════════════════════════════════════
     */
    public function destroy(string $expediente, int $audiencia_id)
    {
        // ─── [Obtener caso y autorizar] ───────────────────────────────
        $caso = Caso::where('caso_numero_expediente', $expediente)->firstOrFail();
        Gate::authorize('update', $caso);

        // ─── [Buscar audiencia dentro del caso] ───────────────────────
        $audiencia = Audiencia::where('audiencia_id', $audiencia_id)
            ->where('caso_id', $caso->caso_id)
            ->firstOrFail();

        // ─── [Eliminar y responder] ───────────────────────────────────
        $audiencia->delete();

        return back()->with('success', 'Audiencia eliminada.');
    }
}
