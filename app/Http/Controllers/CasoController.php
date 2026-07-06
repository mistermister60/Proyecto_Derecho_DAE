<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCasoRequest;
use App\Http\Requests\UpdateCasoRequest;
use App\Models\Caso;
use App\Models\Cliente;
use App\Models\Demandado;
use App\Models\EstadoCaso;
use App\Models\Procurador;
use App\Models\TipoTramite;
use App\Services\CasoService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

/**
 * Controlador para la gestión de casos legales.
 *
 * CRUD completo de casos con autorización basada en Gates (view, update, delete,
 * reasignar, agregarSeguimiento). Incluye funcionalidad de reasignación de
 * procurador. Delega la lógica de negocio en CasoService. Los casos se
 * identifican por número de expediente (string) en lugar de ID numérico.
 */
class CasoController extends Controller
{
    public function __construct(
        protected CasoService $casoService
    ) {}

    /**
     * Lista los casos con filtros y paginación.
     *
     * Delega la obtención de datos filtrados y ordenados a
     * CasoService::getIndexData(), que retorna un array con la colección
     * de casos, filtros activos, etc.
     *
     * @return View Vista index con datos de casos
     */
    public function index()
    {
        $data = $this->casoService->getIndexData();

        return view('casos.index', $data);
    }

    /**
     * Muestra el formulario de creación de un nuevo caso.
     *
     * Precarga los catálogos de clientes activos, procuradores activos
     * y tipos de trámite para los campos select del formulario.
     *
     * @return View Vista create con catálogos precargados
     */
    public function create()
    {
        $clientes = Cliente::where('cliente_estado', 'activo')->get();
        $procuradores = Procurador::where('procurador_estado', 'activo')->get();
        $tramites = TipoTramite::all();

        return view('casos.create', compact('clientes', 'procuradores', 'tramites'));
    }

    /**
     * Registra un nuevo caso en el sistema.
     *
     * Valida los datos mediante StoreCasoRequest (Form Request).
     * Convierte el campo 'caso_admisible' a booleano y delega la creación
     * en CasoService::createCaso().
     *
     * @param  StoreCasoRequest  $request  Validación y datos del caso
     * @return RedirectResponse Redirección al índice con mensaje
     */
    public function store(StoreCasoRequest $request)
    {
        $validated = $request->validated();
        $validated['caso_admisible'] = $request->boolean('caso_admisible', true);

        $this->casoService->createCaso($validated);

        return redirect()->route('casos.index')
            ->with('success', 'Caso creado exitosamente.');
    }

    /**
     * Muestra los detalles completos de un caso.
     *
     * Realiza eager loading de 7 relaciones: cliente, demandado, tipoTrámite,
     * estado, procurador, entrevistas (con procurador), seguimientos (con
     * usuario), audiencias (con procurador) y documentos. Verifica permiso
     * 'view' mediante Gate.
     *
     * @param  string  $expediente  Número de expediente del caso
     * @return View Vista show con caso y todas sus relaciones
     *
     * @throws AuthorizationException Si no tiene permiso 'view'
     * @throws ModelNotFoundException Si el expediente no existe
     */
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

    /**
     * Muestra el formulario de edición de un caso.
     *
     * Precarga catálogos de clientes activos, procuradores activos, tipos de
     * trámite, estados (ordenados) y demandados activos. Verifica permiso
     * 'update' mediante Gate.
     *
     * @param  string  $expediente  Número de expediente del caso
     * @return View Vista edit con caso y catálogos precargados
     *
     * @throws AuthorizationException Si no tiene permiso 'update'
     * @throws ModelNotFoundException Si el expediente no existe
     */
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

    /**
     * Actualiza los datos de un caso existente.
     *
     * Valida mediante UpdateCasoRequest (Form Request). Si el usuario autenticado
     * tiene rol Director, se procesa adicionalmente el campo 'caso_admisible'.
     * Delega la actualización en CasoService::updateCaso().
     *
     * @param  UpdateCasoRequest  $request  Validación y datos actualizados
     * @param  string  $expediente  Número de expediente del caso
     * @return RedirectResponse Redirección a vista show con mensaje
     *
     * @throws AuthorizationException Si no tiene permiso 'update'
     */
    public function update(UpdateCasoRequest $request, string $expediente)
    {
        $caso = $request->caso;

        $validated = $request->validated();
        if ($request->esDirector()) {
            $validated['caso_admisible'] = $request->boolean('caso_admisible', true);
        }

        $this->casoService->updateCaso($caso, $validated);

        return redirect()->route('casos.show', $expediente)
            ->with('success', 'Caso actualizado exitosamente.');
    }

    /**
     * Desactiva un caso (eliminación lógica).
     *
     * Verifica permiso 'delete' mediante Gate y delega la desactivación
     * en CasoService::deactivateCaso(). El registro se conserva en el
     * sistema para integridad histórica.
     *
     * @param  string  $expediente  Número de expediente del caso
     * @return RedirectResponse Redirección al índice con mensaje
     *
     * @throws AuthorizationException Si no tiene permiso 'delete'
     * @throws ModelNotFoundException Si el expediente no existe
     */
    public function destroy(string $expediente)
    {
        $caso = Caso::where('caso_numero_expediente', $expediente)->firstOrFail();

        Gate::authorize('delete', $caso);

        $this->casoService->deactivateCaso($caso);

        return redirect()->route('casos.index')
            ->with('success', 'Caso desactivado exitosamente. El registro se conserva en el sistema.');
    }

    /**
     * Muestra el formulario de reasignación de procurador.
     *
     * Carga la lista de procuradores activos excluyendo al actualmente
     * asignado al caso. Verifica permiso 'reasignar' mediante Gate.
     *
     * @param  string  $expediente  Número de expediente del caso
     * @return View Vista reasignar con caso y lista de procuradores disponibles
     *
     * @throws AuthorizationException Si no tiene permiso 'reasignar'
     * @throws ModelNotFoundException Si el expediente no existe
     */
    public function reasignar(string $expediente)
    {
        $caso = Caso::with(['procurador'])->where('caso_numero_expediente', $expediente)->firstOrFail();

        Gate::authorize('reasignar', $caso);

        $procuradores = Procurador::where('procurador_estado', 'activo')
            ->where('procurador_id', '!=', $caso->procurador_id)
            ->get();

        return view('casos.reasignar', compact('caso', 'procuradores'));
    }

    /**
     * Procesa la reasignación de un caso a otro procurador.
     *
     * Valida inline el procurador destino (existente y distinto del origen)
     * y el motivo de la reasignación. Delega la lógica en
     * CasoService::reassignCaso(). Verifica permiso 'reasignar' mediante Gate.
     *
     * @param  Request  $request  Datos de la reasignación
     * @param  string  $expediente  Número de expediente del caso
     * @return RedirectResponse Redirección a vista show con mensaje
     *
     * @throws AuthorizationException Si no tiene permiso 'reasignar'
     * @throws ModelNotFoundException Si el expediente no existe
     */
    public function storeReasignacion(Request $request, string $expediente)
    {
        $caso = Caso::where('caso_numero_expediente', $expediente)->firstOrFail();

        Gate::authorize('reasignar', $caso);

        $validated = $request->validate([
            'procurador_destino_id' => 'required|exists:procuradores,procurador_id|different:procurador_origen_id',
            'reasignacion_motivo' => 'required|string',
        ]);

        $this->casoService->reassignCaso($caso, $validated);

        return redirect()->route('casos.show', $expediente)
            ->with('success', 'Caso reasignado exitosamente.');
    }
}
