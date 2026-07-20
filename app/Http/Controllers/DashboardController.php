<?php

namespace App\Http\Controllers;

use App\Models\Audiencia;
use App\Models\Caso;
use App\Models\EstadoCaso;
use App\Models\Procurador;
use App\Models\TipoTramite;
use Illuminate\View\View;

/**
 * Controlador del panel principal de administración.
 *
 * Ejecuta múltiples consultas de agregación para mostrar indicadores clave,
 * gráficas de pipeline y tipo de trámite, audiencias próximas y carga de
 * trabajo por procurador.
 *
 * Nota: NO usar Cache::remember con modelos/Collections aquí. Laravel 13
 * trae 'serializable_classes' => false por defecto (config/cache.php), lo que
 * degrada los objetos cacheados a __PHP_Incomplete_Class al leerlos y provoca
 * un error 500 ("Attempt to read property on string") en el segundo request.
 * Solo sería seguro cachear escalares o arrays puros (conteos, IDs, strings).
 */
class DashboardController extends Controller
{
    /**
     * Muestra el panel principal con métricas y gráficas del sistema.
     *
     * @return View
     */
    public function index()
    {
        $casosActivos = Caso::where('caso_estado', 'activo')->count();
        $cerrados = Caso::where('caso_estado', 'cerrado')->count();
        $totalCasos = Caso::count();

        $nuevosEsteMes = Caso::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $audienciasEstaSemana = Audiencia::whereBetween('audiencia_fecha', [
            now()->startOfWeek(), now()->endOfWeek(),
        ])->count();

        $estadoAtrasado = EstadoCaso::where('estado_nombre', 'Atrasado')->value('estado_id');
        $atrasados = Caso::where('estado_id', $estadoAtrasado)->where('caso_estado', 'activo')->count();

        // Audiencias próximas (hoy + 7 días)
        $proximasAudiencias = Audiencia::with(['caso', 'procurador'])
            ->whereBetween('audiencia_fecha', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->orderBy('audiencia_fecha')
            ->orderBy('audiencia_hora')
            ->take(5)
            ->get();

        // Carga por procurador
        $procuradores = Procurador::withCount([
            'casos as total_casos' => function ($q) {
                $q->where('caso_estado', 'activo');
            },
        ])->get();
        $procuradores->loadCount(['casos as activos' => function ($q) {
            $q->where('caso_estado', 'activo')
                ->whereNotIn('estado_id', EstadoCaso::whereIn('estado_nombre', ['Cerrado', 'Inadmisible'])->pluck('estado_id'));
        }]);

        // Datos para gráfica de pipeline
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

        // Datos para gráfica de tipo de trámite
        $tramites = TipoTramite::all();
        $tipoLabels = $tramites->pluck('tramite_nombre');
        $tipoCounts = Caso::where('caso_estado', 'activo')
            ->selectRaw('tipo_tramite_id, COUNT(*) as total')
            ->groupBy('tipo_tramite_id')
            ->pluck('total', 'tipo_tramite_id');
        $tipoData = $tramites->map(fn ($t) => $tipoCounts[$t->tipo_tramite_id] ?? 0);

        // Datos para gráfica de resoluciones (casos cerrados)
        $resolucionesLabels = ['Ganado', 'Perdido', 'Conciliado', 'Desistido', 'Desestimado'];
        $resolucionesValues = Caso::where('caso_estado', 'cerrado')
            ->whereNotNull('resolucion_tipo')
            ->selectRaw('resolucion_tipo, COUNT(*) as total')
            ->groupBy('resolucion_tipo')
            ->pluck('total', 'resolucion_tipo');
        $resolucionesData = collect(['ganado', 'perdido', 'conciliado', 'desistido', 'desestimado'])
            ->map(fn ($key) => $resolucionesValues[$key] ?? 0);
        $resolucionesColors = ['#2563EB', '#DC2626', '#16A34A', '#F59E0B', '#7C3AED'];

        return view('dashboard.index', compact(
            'casosActivos', 'cerrados', 'totalCasos',
            'nuevosEsteMes', 'audienciasEstaSemana', 'atrasados',
            'proximasAudiencias', 'procuradores',
            'pipelineLabels', 'pipelineData', 'pipelineColors',
            'tipoLabels', 'tipoData',
            'resolucionesLabels', 'resolucionesData', 'resolucionesColors'
        ));
    }
}
