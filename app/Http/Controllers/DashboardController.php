<?php

namespace App\Http\Controllers;

use App\Models\Audiencia;
use App\Models\Caso;
use App\Models\EstadoCaso;
use App\Models\Procurador;
use App\Models\TipoTramite;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * Controlador del panel principal de administración.
 *
 * Ejecuta múltiples consultas de agregación para mostrar indicadores clave,
 * gráficas de pipeline y tipo de trámite, audiencias próximas y carga de
 * trabajo por procurador.
 */
class DashboardController extends Controller
{
    /**
     * Muestra el panel principal con métricas y gráficas del sistema.
     *
     * Realiza 8 consultas de agregación: casos activos/cerrados/totales,
     * nuevos del mes, audiencias de la semana, casos atrasados, audiencias
     * próximas (7 días), carga por procurador, y datos para gráficas de
     * pipeline y tipo de trámite.
     *
     * @return View Vista dashboard con 15 variables de agregación
     */
    public function index()
    {
        [$casosActivos, $cerrados, $totalCasos, $nuevosEsteMes] = Cache::remember('dashboard.counters', 300, function () {
            return [
                Caso::where('caso_estado', 'activo')->count(),
                Caso::where('caso_estado', 'cerrado')->count(),
                Caso::count(),
                Caso::whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
            ];
        });

        $audienciasEstaSemana = Cache::remember('dashboard.audiencias_semana', 300, function () {
            return Audiencia::whereBetween('audiencia_fecha', [
                now()->startOfWeek(), now()->endOfWeek(),
            ])->count();
        });

        $atrasados = Cache::remember('dashboard.atrasados', 300, function () {
            $estadoAtrasado = EstadoCaso::where('estado_nombre', 'Atrasado')->value('estado_id');

            return Caso::where('estado_id', $estadoAtrasado)->where('caso_estado', 'activo')->count();
        });

        // Audiencias próximas (hoy + 7 días)
        $proximasAudiencias = Cache::remember('dashboard.proximas_audiencias', 300, function () {
            return Audiencia::with(['caso', 'procurador'])
                ->whereBetween('audiencia_fecha', [now()->toDateString(), now()->addDays(7)->toDateString()])
                ->orderBy('audiencia_fecha')
                ->orderBy('audiencia_hora')
                ->take(5)
                ->get();
        });

        // Carga por procurador
        $procuradores = Cache::remember('dashboard.procuradores', 300, function () {
            $procuradores = Procurador::withCount([
                'casos as total_casos' => function ($q) {
                    $q->where('caso_estado', 'activo');
                },
            ])->get();
            $procuradores->loadCount(['casos as activos' => function ($q) {
                $q->where('caso_estado', 'activo')
                    ->whereNotIn('estado_id', EstadoCaso::whereIn('estado_nombre', ['Cerrado', 'Inadmisible'])->pluck('estado_id'));
            }]);

            return $procuradores;
        });

        // Datos para gráficas de pipeline
        [$pipelineLabels, $pipelineData, $pipelineColors] = Cache::remember('dashboard.pipeline', 300, function () {
            $estados = EstadoCaso::where('estado_tipo', 'pipeline')
                ->orderBy('estado_orden')
                ->get();
            $pipelineLabels = $estados->pluck('estado_nombre');
            $pipelineCounts = Caso::where('caso_estado', 'activo')
                ->selectRaw('estado_id, COUNT(*) as total')
                ->groupBy('estado_id')
                ->pluck('total', 'estado_id');
            $pipelineData = $estados->map(fn ($e) => $pipelineCounts[$e->estado_id] ?? 0);
            $pipelineColors = $estados->pluck('estado_color');

            return [$pipelineLabels, $pipelineData, $pipelineColors];
        });

        // Datos para gráfica de tipo de trámite
        [$tipoLabels, $tipoData] = Cache::remember('dashboard.tipos', 300, function () {
            $tramites = TipoTramite::all();
            $tipoLabels = $tramites->pluck('tramite_nombre');
            $tipoCounts = Caso::where('caso_estado', 'activo')
                ->selectRaw('tipo_tramite_id, COUNT(*) as total')
                ->groupBy('tipo_tramite_id')
                ->pluck('total', 'tipo_tramite_id');
            $tipoData = $tramites->map(fn ($t) => $tipoCounts[$t->tipo_tramite_id] ?? 0);

            return [$tipoLabels, $tipoData];
        });

        return view('dashboard.index', compact(
            'casosActivos', 'cerrados', 'totalCasos',
            'nuevosEsteMes', 'audienciasEstaSemana', 'atrasados',
            'proximasAudiencias', 'procuradores',
            'pipelineLabels', 'pipelineData', 'pipelineColors',
            'tipoLabels', 'tipoData'
        ));
    }
}
