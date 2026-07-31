<?php

namespace App\Http\Controllers;

use App\Models\Caso;
use App\Models\Seguimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * ═══════════════════════════════════════════════════════
 * CONTROLADOR: SeguimientoController
 * ═══════════════════════════════════════════════════════
 * Gestiona la bitácora de seguimiento (timeline) de un caso.
 * Permite agregar entradas de seguimiento con tipo y descripción.
 * Rutas: POST /casos/{caso}/seguimientos
 * Middleware: auth, otp, password.changed
 * Roles: Procurador (dueño del caso), Director
 * Autorización: Gate 'agregarSeguimiento' sobre el Caso
 * Nota: Recibe caso_id numérico (inconsistencia: otros usan $expediente)
 */
class SeguimientoController extends Controller
{
    /**
     * ═══════════════════════════════════════════════════════
     * store
     * ───────────────────────────────────────────────────────
     * Agrega una entrada a la bitácora de seguimiento del caso.
     * Valida tipo y descripción. Asigna caso, usuario autenticado,
     * fecha actual y estado 'activo'. Respuesta: Redirect back con success.
     * ═══════════════════════════════════════════════════════
     */
    public function store(Request $request, $caso_id)
    {
        // ─── [Obtener caso y autorizar] ───────────────────────────────
        $caso = Caso::findOrFail($caso_id);
        Gate::authorize('agregarSeguimiento', $caso);

        // ─── [Validar datos de entrada] ───────────────────────────────
        $request->validate([
            'seguimiento_tipo' => 'required|string|max:50',
            'seguimiento_descripcion' => 'required|string',
        ]);

        // ─── [Crear entrada en bitácora] ──────────────────────────────
        Seguimiento::create([
            'caso_id' => $caso_id,
            'usuario_id' => Auth::id(),
            'seguimiento_fecha' => now()->toDateString(),
            'seguimiento_tipo' => $request->input('seguimiento_tipo'),
            'seguimiento_descripcion' => $request->input('seguimiento_descripcion'),
            'seguimiento_estado' => 'activo',
        ]);

        return redirect()->back()->with('success', 'Bitácora actualizada correctamente.');
    }
}
