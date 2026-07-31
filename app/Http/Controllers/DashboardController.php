<?php

namespace App\Http\Controllers;

use App\Enums\RolEnum;
use App\Models\Audiencia;
use App\Models\Caso;
use App\Models\EstadoCaso;
use App\Models\Procurador;
use App\Models\TipoTramite;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

/**
 * ═══════════════════════════════════════════════════════
 * CONTROLADOR: DashboardController
 * ═══════════════════════════════════════════════════════
 * Panel principal de administración. Ejecuta múltiples
 * consultas de agregación para mostrar indicadores clave
 * (KPIs), gráficas de pipeline y tipo de trámite, audiencias
 * próximas y carga de trabajo por procurador.
 * ───────────────────────────────────────────────────────
 * Si el usuario autenticado es Procurador, TODAS las métricas
 * se filtran automáticamente para reflejar solo sus casos,
 * audiencias y datos.
 * ───────────────────────────────────────────────────────
 * ⚠️ IMPORTANTE - CACHE: NO usar Cache::remember con modelos/
 * Collections aquí. Laravel 13 trae 'serializable_classes' =>
 * false por defecto (config/cache.php), lo que degrada los
 * objetos cacheados a __PHP_Incomplete_Class al leerlos y
 * provoca un error 500 ("Attempt to read property on string")
 * en el segundo request. Solo sería seguro cachear escalares
 * o arrays puros (conteos, IDs, strings).
 * ───────────────────────────────────────────────────────
 * Rutas protegidas: middleware ['auth', 'otp', 'password.changed']
 * Roles: Director (ve todo), Procurador (solo sus datos)
 */
class DashboardController extends Controller
{
    /**
     * ═══════════════════════════════════════════════════════
     * getProcuradorFilter
     * ───────────────────────────────────────────────────────
     * Obtener el procurador_id si el usuario es Procurador,
     * null si es Director. Método privado auxiliar.
     * ═══════════════════════════════════════════════════════
     */
    private function getProcuradorFilter(): ?int
    {
        if (RolEnum::equals(auth()->user()->rol?->rol_nombre, RolEnum::PROCURADOR)) {
            return auth()->user()->procurador_id;
        }

        return null;
    }

    /**
     * ═══════════════════════════════════════════════════════
     * casosFiltered
     * ───────────────────────────────────────────────────────
     * Aplicar filtro de procurador a una consulta de Casos.
     * Método privado auxiliar.
     * ═══════════════════════════════════════════════════════
     */
    private function casosFiltered(): Builder
    {
        $procuradorId = $this->getProcuradorFilter();

        return Caso::when($procuradorId, fn ($q) => $q->where('procurador_id', $procuradorId));
    }

    /**
     * ═══════════════════════════════════════════════════════
     * audienciasFiltered
     * ───────────────────────────────────────────────────────
     * Aplicar filtro de procurador a una consulta de Audiencias.
     * Método privado auxiliar.
     * ═══════════════════════════════════════════════════════
     */
    private function audienciasFiltered(): Builder
    {
        $procuradorId = $this->getProcuradorFilter();

        return Audiencia::when($procuradorId, fn ($q) => $q->whereHas('caso', fn ($cq) => $cq->where('procurador_id', $procuradorId)));
    }

    /**
     * ═══════════════════════════════════════════════════════
     * index
     * ───────────────────────────────────────────────────────
     * Muestra el panel principal con métricas y gráficas del sistema.
     * Calcula KPIs, audiencias próximas, carga por procurador,
     * y datos para 3 gráficas Chart.js (pipeline, tipo trámite, resoluciones).
     * ═══════════════════════════════════════════════════════
     *
     * @return View
     */
    public function index()
    {
        $procuradorId = $this->getProcuradorFilter();
        $esProcurador = $procuradorId !== null;

        // ═══════════════════════════════════════════════════════
        // === KPIs (Indicadores Clave) ===
        // ════════════════════════════════════════════════════════
        // ─── [Casos activos] ────────────────────────────────────
        $casosActivos = $this->casosFiltered()->where('caso_estado', 'activo')->count();
        // ─── [Casos cerrados] ───────────────────────────────────
        $cerrados = $this->casosFiltered()->where('caso_estado', 'cerrado')->count();
        // ─── [Total de casos] ───────────────────────────────────
        $totalCasos = $this->casosFiltered()->count();

        // ─── [Nuevos casos este mes] ────────────────────────────
        $nuevosEsteMes = $this->casosFiltered()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // ─── [Audiencias esta semana] ───────────────────────────
        $audienciasEstaSemana = $this->audienciasFiltered()
            ->whereBetween('audiencia_fecha', [
                now()->startOfWeek(), now()->endOfWeek(),
            ])->count();

        // ─── [Casos atrasados] ──────────────────────────────────
        $estadoAtrasado = EstadoCaso::where('estado_nombre', config('app.estados_caso.atrasado'))->value('estado_id');
        $atrasados = $this->casosFiltered()
            ->where('estado_id', $estadoAtrasado)
            ->where('caso_estado', 'activo')
            ->count();

        // ═══════════════════════════════════════════════════════
        // === Audiencias próximas (hoy + 7 días) ===
        // ═══════════════════════════════════════════════════════
        $proximasAudiencias = $this->audienciasFiltered()
            ->with(['caso', 'procurador'])
            ->whereBetween('audiencia_fecha', [now()->toDateString(), now()->addDays(7)->toDateString()])
            ->orderBy('audiencia_fecha')
            ->orderBy('audiencia_hora')
            ->take(5)
            ->get();

        // ═══════════════════════════════════════════════════════
        // === Carga por procurador ===
        // ═══════════════════════════════════════════════════════
        if ($esProcurador) {
            // ─── [Si es Procurador: solo se ve a sí mismo] ──────
            $procuradores = Procurador::where('procurador_id', $procuradorId)
                ->withCount([
                    'casos as total_casos' => fn ($q) => $q->where('caso_estado', 'activo'),
                ])
                ->get();
        } else {
            // ─── [Si es Director: ve todos los procuradores] ────
            $procuradores = Procurador::withCount([
                'casos as total_casos' => fn ($q) => $q->where('caso_estado', 'activo'),
            ])->get();
        }

        // ─── [Estados excluidos para conteo de activos] ─────────
        $estadosExcluidos = EstadoCaso::whereIn('estado_nombre', [
            config('app.estados_caso.cerrado'),
            config('app.estados_caso.inadmisible'),
        ])->pluck('estado_id');

        // ─── [Carga de casos activos por procurador] ────────────
        $procuradores->loadCount(['casos as activos' => function ($q) use ($estadosExcluidos, $procuradorId) {
            $q->where('caso_estado', 'activo')
                ->whereNotIn('estado_id', $estadosExcluidos);
            // ─── [Si es Procurador filtrando su propia carga] ───
            if ($procuradorId) {
                $q->where('procurador_id', $procuradorId);
            }
        }]);

        // ═══════════════════════════════════════════════════════
        // === Datos para gráfica de pipeline (estados pipeline) ===
        // ═══════════════════════════════════════════════════════
        $estados = EstadoCaso::where('estado_tipo', 'pipeline')
            ->orderBy('estado_orden')
            ->get();
        $pipelineLabels = $estados->pluck('estado_nombre');
        $pipelineCounts = $this->casosFiltered()
            ->where('caso_estado', 'activo')
            ->selectRaw('estado_id, COUNT(*) as total')
            ->groupBy('estado_id')
            ->pluck('total', 'estado_id');
        $pipelineData = $estados->map(fn ($e) => $pipelineCounts[$e->estado_id] ?? 0);
        $pipelineColors = $estados->pluck('estado_color');

        // ═══════════════════════════════════════════════════════
        // === Datos para gráfica de tipo de trámite ===
        // ═══════════════════════════════════════════════════════
        $tramites = TipoTramite::all();
        $tipoLabels = $tramites->pluck('tramite_nombre');
        $tipoCounts = $this->casosFiltered()
            ->where('caso_estado', 'activo')
            ->selectRaw('tipo_tramite_id, COUNT(*) as total')
            ->groupBy('tipo_tramite_id')
            ->pluck('total', 'tipo_tramite_id');
        $tipoData = $tramites->map(fn ($t) => $tipoCounts[$t->tipo_tramite_id] ?? 0);

        // ═══════════════════════════════════════════════════════
        // === Datos para gráfica de resoluciones (casos cerrados) ===
        // ═══════════════════════════════════════════════════════
        $resolucionesLabels = ['Ganado', 'Perdido', 'Conciliado', 'Desistido', 'Desestimado'];
        $resolucionesValues = $this->casosFiltered()
            ->where('caso_estado', 'cerrado')
            ->whereNotNull('resolucion_tipo')
            ->selectRaw('resolucion_tipo, COUNT(*) as total')
            ->groupBy('resolucion_tipo')
            ->pluck('total', 'resolucion_tipo');
        $resolucionesData = collect(['ganado', 'perdido', 'conciliado', 'desistido', 'desestimado'])
            ->map(fn ($key) => $resolucionesValues[$key] ?? 0);
        $resolucionesColors = ['#2563EB', '#DC2626', '#16A34A', '#F59E0B', '#7C3AED'];

        // ─── [Renderizado de vista con todas las variables] ─────
        return view('dashboard.index', compact(
            'casosActivos', 'cerrados', 'totalCasos',
            'nuevosEsteMes', 'audienciasEstaSemana', 'atrasados',
            'proximasAudiencias', 'procuradores',
            'pipelineLabels', 'pipelineData', 'pipelineColors',
            'tipoLabels', 'tipoData',
            'resolucionesLabels', 'resolucionesData', 'resolucionesColors',
            'esProcurador'
        ));
    }
}
