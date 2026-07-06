<?php

namespace App\Http\Controllers;

use App\Enums\RolEnum;
use App\Models\Audiencia;
use Carbon\Carbon;
use Illuminate\View\View;

/**
 * Controlador para la agenda de audiencias.
 *
 * Muestra el calendario de audiencias con filtro por rol: los procuradores
 * ven solo sus propias audiencias, mientras que los directores ven todas.
 * Las audiencias se agrupan por mes para visualización calendario.
 */
class AgendaController extends Controller
{
    /**
     * Lista las audiencias ordenadas por fecha y hora.
     *
     * Aplica eager loading de relaciones (caso, cliente, tipoTrámite, procurador).
     * Filtra por procurador autenticado si el rol es PROCURADOR.
     * Agrupa resultados por mes (Y-m) para el calendario y extrae las
     * próximas 10 audiencias desde la fecha actual.
     *
     * @return View Vista agenda con audiencias, agrupación por mes y próximas
     */
    public function index()
    {
        // 1. Iniciamos la consulta con tus relaciones y ordenamientos originales
        $query = Audiencia::with(['caso.cliente', 'caso.tipoTramite', 'procurador'])
            ->orderBy('audiencia_fecha')
            ->orderBy('audiencia_hora');

        // 2. Si es procurador, filtramos por sus casos. Si es Director, se salta este IF y ve todo.
        if (RolEnum::equals(auth()->user()->rol?->rol_nombre, RolEnum::PROCURADOR)) {
            $query->whereHas('caso', function ($q) {
                $q->where('procurador_id', auth()->user()->procurador_id);
            });
        }

        $audiencias = $query->get();

        // --- Todo tu flujo de abajo se mantiene idéntico ---
        // Agrupar por mes para el calendario
        $audienciasPorMes = $audiencias->groupBy(fn ($a) => Carbon::parse($a->audiencia_fecha)->format('Y-m'));

        $proximas = $audiencias->where('audiencia_fecha', '>=', now()->toDateString())
            ->take(10);

        return view('agenda.index', compact('audiencias', 'audienciasPorMes', 'proximas'));
    }
}
