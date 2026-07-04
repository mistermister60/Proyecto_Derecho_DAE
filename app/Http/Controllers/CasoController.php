<?php

namespace App\Http\Controllers;

use App\Enums\RolEnum;
use App\Http\Requests\StoreCasoRequest;
use App\Http\Requests\UpdateCasoRequest;
use App\Models\Caso;
use App\Models\Cliente;
use App\Models\Demandado;
use App\Models\EstadoCaso;
use App\Models\Procurador;
use App\Models\Reasignacion;
use App\Models\TipoTramite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CasoController extends Controller
{
    public function index()
    {
        $queryCasos = Caso::with(['cliente', 'tipoTramite', 'estado', 'procurador', 'audiencias'])->orderBy('created_at', 'desc');

        if (RolEnum::equals(auth()->user()->rol?->rol_nombre, RolEnum::PROCURADOR)) {
            $queryCasos->where('procurador_id', auth()->user()->procurador_id);
        }
        $casos = $queryCasos->get();

        $estados = EstadoCaso::where('estado_tipo', 'pipeline')
            ->orderBy('estado_orden')
            ->get();

        $tramites = TipoTramite::all();

        // Datos agrupados para kanban
        $columnas = [];
        foreach ($estados as $estado) {
            $casosEnEstado = $casos->where('estado_id', $estado->estado_id);
            $tarjetas = [];
            foreach ($casosEnEstado as $caso) {
                $tarjetas[$caso->caso_numero_expediente] = [
                    $caso->cliente?->nombre_completo ?? 'Sin cliente',
                    $caso->tipoTramite?->tramite_nombre ?? 'Sin trámite',
                    optional($caso->audiencias->first())->audiencia_fecha ?? '',
                ];
            }
            $columnas[$estado->estado_nombre] = [$estado->estado_color, $tarjetas];
        }

        return view('casos.index', compact('casos', 'estados', 'tramites', 'columnas'));
    }

    public function create()
    {
        $clientes = Cliente::where('cliente_estado', 'activo')->get();
        $procuradores = Procurador::where('procurador_estado', 'activo')->get();
        $tramites = TipoTramite::all();

        return view('casos.create', compact('clientes', 'procuradores', 'tramites'));
    }

    public function store(StoreCasoRequest $request)
    {
        $validated = $request->validated();

        // Generar número de expediente automático
        $ultimo = Caso::orderBy('caso_id', 'desc')->first();
        $correlativo = $ultimo ? intval(substr($ultimo->caso_numero_expediente, -5)) + 1 : 1;
        $validated['caso_numero_expediente'] = '0501-'.now()->year.'-'.str_pad($correlativo, 5, '0', STR_PAD_LEFT);
        $validated['estado_id'] = EstadoCaso::where('estado_nombre', 'Entrevista')->value('estado_id');
        $validated['caso_fecha_interpuesta'] = now()->toDateString();
        $validated['caso_fecha_asignacion'] = now()->toDateString();
        $validated['caso_admisible'] = $request->boolean('caso_admisible', true);
        $validated['caso_estado'] = 'activo';

        Caso::create($validated);

        return redirect()->route('casos.index')
            ->with('success', 'Caso creado exitosamente.');
    }

    public function show(string $expediente)
    {
        $caso = Caso::with([
            'cliente', 'demandado', 'tipoTramite', 'estado', 'procurador',
            'entrevistas.procurador', 'seguimientos.usuario',
            'audiencias.procurador', 'documentos',
        ])
            ->where('caso_numero_expediente', $expediente)
            ->firstOrFail();

        Gate::authorize('view', $caso);

        return view('casos.show', compact('caso'));
    }

    public function edit(string $expediente)
    {
        $caso = Caso::where('caso_numero_expediente', $expediente)->firstOrFail();

        Gate::authorize('update', $caso);

        $clientes = Cliente::where('cliente_estado', 'activo')->get();
        $procuradores = Procurador::where('procurador_estado', 'activo')->get();
        $tramites = TipoTramite::all();
        $estados = EstadoCaso::orderBy('estado_orden')->get();
        $demandados = Demandado::where('demandado_estado', 'activo')->get();

        return view('casos.edit', compact('caso', 'clientes', 'procuradores', 'tramites', 'estados', 'demandados'));
    }

    public function update(UpdateCasoRequest $request, string $expediente)
    {
        $caso = $request->caso;

        $validated = $request->validated();
        if ($request->esDirector()) {
            $validated['caso_admisible'] = $request->boolean('caso_admisible', true);
        }

        $caso->update($validated);

        return redirect()->route('casos.show', $expediente)
            ->with('success', 'Caso actualizado exitosamente.');
    }

    public function destroy(string $expediente)
    {
        $caso = Caso::where('caso_numero_expediente', $expediente)->firstOrFail();

        Gate::authorize('delete', $caso);

        $caso->update(['caso_estado' => 'inactivo']);

        return redirect()->route('casos.index')
            ->with('success', 'Caso desactivado exitosamente. El registro se conserva en el sistema.');
    }

    public function reasignar(string $expediente)
    {
        $caso = Caso::with(['procurador'])->where('caso_numero_expediente', $expediente)->firstOrFail();

        Gate::authorize('reasignar', $caso);

        $procuradores = Procurador::where('procurador_estado', 'activo')
            ->where('procurador_id', '!=', $caso->procurador_id)
            ->get();

        return view('casos.reasignar', compact('caso', 'procuradores'));
    }

    public function storeReasignacion(Request $request, string $expediente)
    {
        $caso = Caso::where('caso_numero_expediente', $expediente)->firstOrFail();

        Gate::authorize('reasignar', $caso);

        $validated = $request->validate([
            'procurador_destino_id' => 'required|exists:procuradores,procurador_id|different:procurador_origen_id',
            'reasignacion_motivo' => 'required|string',
        ]);

        $validated['caso_id'] = $caso->caso_id;
        $validated['procurador_origen_id'] = $caso->procurador_id;
        $validated['reasignacion_fecha'] = now();
        $validated['reasignacion_estado'] = 'completada';

        Reasignacion::create($validated);

        // Actualizar el procurador del caso
        $caso->update(['procurador_id' => $validated['procurador_destino_id']]);

        return redirect()->route('casos.show', $expediente)
            ->with('success', 'Caso reasignado exitosamente.');
    }
}
