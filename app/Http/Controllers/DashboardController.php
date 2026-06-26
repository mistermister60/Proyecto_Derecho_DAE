<?php

namespace App\Http\Controllers;

use App\Models\Caso;
use App\Models\Audiencia;
use App\Models\Procurador;
use App\Models\EstadoCaso;
use App\Models\TipoTramite;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $casosActivos = Caso::where('caso_estado', 'activo')->count();
        $cerrados = Caso::where('caso_estado', 'cerrado')->count();
        $totalCasos = Caso::count();

        $nuevosEsteMes = Caso::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $audienciasEstaSemana = Audiencia::whereBetween('audiencia_fecha', [
            now()->startOfWeek(), now()->endOfWeek()
        ])->count();

        $atrasados = Caso::where('estado_id', 11)->where('caso_estado', 'activo')->count();

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
            }
        ])->get();
        $procuradores->loadCount(['casos as activos' => function ($q) {
            $q->where('caso_estado', 'activo')
              ->whereNotIn('estado_id', [8, 9]); // No cerrados ni inadmisibles
        }]);

        // Datos para gráfica de pipeline
        $estados = EstadoCaso::where('estado_tipo', 'pipeline')
            ->orderBy('estado_orden')
            ->get();
        $pipelineLabels = $estados->pluck('estado_nombre');
        $pipelineData = $estados->map(fn($e) => Caso::where('estado_id', $e->estado_id)
            ->where('caso_estado', 'activo')
            ->count()
        );
        $pipelineColors = $estados->pluck('estado_color');

        // Datos para gráfica de tipo de trámite
        $tramites = TipoTramite::all();
        $tipoLabels = $tramites->pluck('tramite_nombre');
        $tipoData = $tramites->map(fn($t) => Caso::where('tipo_tramite_id', $t->tipo_tramite_id)
            ->where('caso_estado', 'activo')
            ->count()
        );

        return view('dashboard.index', compact(
            'casosActivos', 'cerrados', 'totalCasos',
            'nuevosEsteMes', 'audienciasEstaSemana', 'atrasados',
            'proximasAudiencias', 'procuradores',
            'pipelineLabels', 'pipelineData', 'pipelineColors',
            'tipoLabels', 'tipoData'
        ));
    }
}
