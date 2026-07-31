<?php

namespace App\Http\Controllers;

use App\Enums\RolEnum;
use App\Models\Audiencia;
use Carbon\Carbon;
use Illuminate\View\View;

/**
 * ═══════════════════════════════════════════════════════
 * CONTROLADOR: AgendaController
 * ═══════════════════════════════════════════════════════
 * Agenda de audiencias. Muestra el calendario de audiencias
 * con filtro por rol: los procuradores ven solo sus propias
 * audiencias, mientras que los directores ven todas.
 * Las audiencias se agrupan por mes para visualización
 * calendario.
 * ───────────────────────────────────────────────────────
 * Rutas protegidas: middleware ['auth', 'otp', 'password.changed']
 * Roles: Director (ve todo), Procurador (solo sus audiencias)
 */
class AgendaController extends Controller
{
    /**
     * ═══════════════════════════════════════════════════════
     * index
     * ───────────────────────────────────────────────────────
     * Lista las audiencias ordenadas por fecha y hora.
     * Aplica eager loading de relaciones (caso, cliente,
     * tipoTrámite, procurador). Filtra por procurador
     * autenticado si el rol es PROCURADOR. Agrupa resultados
     * por mes (Y-m) para el calendario y extrae las próximas
     * 10 audiencias desde la fecha actual.
     * ═══════════════════════════════════════════════════════
     *
     * @return View Vista agenda con audiencias, agrupación por mes y próximas
     */
    public function index()
    {
        // ─── [1. Consulta base con relaciones y ordenamiento] ───
        $query = Audiencia::with(['caso.cliente', 'caso.tipoTramite', 'procurador'])
            ->orderBy('audiencia_fecha')
            ->orderBy('audiencia_hora');

        // ─── [2. Filtro por rol: Procurador ve solo sus audiencias] ───
        if (RolEnum::equals(auth()->user()->rol?->rol_nombre, RolEnum::PROCURADOR)) {
            $query->whereHas('caso', function ($q) {
                $q->where('procurador_id', auth()->user()->procurador_id);
            });
        }

        // ─── [3. Ejecutar consulta] ─────────────────────────────
        $audiencias = $query->get();

        // ─── [4. Agrupar por mes para el calendario] ────────────
        $audienciasPorMes = $audiencias->groupBy(fn ($a) => Carbon::parse($a->audiencia_fecha)->format('Y-m'));

        // ─── [5. Próximas 10 audiencias desde hoy] ──────────────
        $proximas = $audiencias->where('audiencia_fecha', '>=', now()->toDateString())
            ->take(10);

        // ─── [6. Renderizado de vista] ──────────────────────────
        return view('agenda.index', compact('audiencias', 'audienciasPorMes', 'proximas'));
    }
}
