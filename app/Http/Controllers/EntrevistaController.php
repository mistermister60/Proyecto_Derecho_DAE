<?php

namespace App\Http\Controllers;

use App\Models\Caso;
use App\Models\Entrevista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * ═══════════════════════════════════════════════════════
 * CONTROLADOR: EntrevistaController
 * ═══════════════════════════════════════════════════════
 * Gestión de entrevistas dentro de un caso.
 * Permite registrar y eliminar entrevistas asociadas a un expediente.
 * Estructura similar a AudienciaController.
 * Rutas: POST /casos/{expediente}/entrevistas, DELETE /casos/{expediente}/entrevistas/{entrevista}
 * Middleware: auth, otp, password.changed (vía routes/web.php)
 * Roles: Procurador (dueño del caso), Director
 * Autorización: 'update' sobre el Caso
 */
class EntrevistaController extends Controller
{
    /**
     * ═══════════════════════════════════════════════════════
     * store
     * ───────────────────────────────────────────────────────
     * Registra una nueva entrevista para el caso.
     * Valida fecha, relación de hechos y observaciones.
     * Asigna automáticamente caso, procurador y estado 'activo'.
     * Respuesta: Redirect back con mensaje success.
     * ═══════════════════════════════════════════════════════
     */
    public function store(Request $request, string $expediente)
    {
        // ─── [Obtener caso y autorizar] ──────────────────────────────
        $caso = Caso::where('caso_numero_expediente', $expediente)->firstOrFail();
        Gate::authorize('update', $caso);

        // ─── [Validar datos de entrada] ──────────────────────────────
        $validated = $request->validate([
            'entrevista_fecha' => 'required|date',
            'entrevista_relacion_hechos' => 'required|string',
            'entrevista_observaciones' => 'nullable|string',
        ]);

        // ─── [Asignar relaciones y estado por defecto] ───────────────
        $validated['caso_id'] = $caso->caso_id;
        $validated['procurador_id'] = $caso->procurador_id;
        $validated['entrevista_estado'] = 'activo';

        // ─── [Crear registro en BD] ──────────────────────────────────
        Entrevista::create($validated);

        return back()->with('success', 'Entrevista registrada.');
    }

    /**
     * ═══════════════════════════════════════════════════════
     * destroy
     * ───────────────────────────────────────────────────────
     * Elimina una entrevista del caso.
     * Busca por ID dentro del caso y borra físicamente de BD.
     * Respuesta: Redirect back con mensaje success.
     * ═══════════════════════════════════════════════════════
     */
    public function destroy(string $expediente, int $entrevista_id)
    {
        // ─── [Obtener caso y autorizar] ──────────────────────────────
        $caso = Caso::where('caso_numero_expediente', $expediente)->firstOrFail();
        Gate::authorize('update', $caso);

        // ─── [Buscar entrevista dentro del caso] ─────────────────────
        $entrevista = Entrevista::where('entrevista_id', $entrevista_id)
            ->where('caso_id', $caso->caso_id)
            ->firstOrFail();

        // ─── [Eliminar registro] ─────────────────────────────────────
        $entrevista->delete();

        return back()->with('success', 'Entrevista eliminada.');
    }
}
