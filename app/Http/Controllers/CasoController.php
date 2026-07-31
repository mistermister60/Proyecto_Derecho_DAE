<?php

namespace App\Http\Controllers;

/**
 * ═══════════════════════════════════════════════════════
 * CONTROLADOR: CasoController
 * ═══════════════════════════════════════════════════════
 * CRUD completo de casos legales con funcionalidades adicionales:
 * reasignación de procurador y cierre con resolución.
 * Rutas protegidas por middleware 'auth', 'otp', 'password.changed'.
 * Permisos finos vía Gates: view, update, delete, reasignar,
 * agregarSeguimiento. Los casos se identifican por número de
 * expediente (string). Delega lógica de negocio en CasoService.
 */

use App\Enums\RolEnum;
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
     * @param  Request  $request  Contiene los parámetros opcionales 'estado' y 'tramite'.
     * @return View Vista index con datos de casos
     */
    public function index(Request $request)
    {
        // ─── [Obtener datos filtrados y paginados] ──────────
        // Delega en CasoService que aplica filtros por rol,
        // búsqueda, estado y ordenamiento
        $data = $this->casoService->getIndexData([
            'estado' => $request->query('estado', ''),
            'tramite' => $request->query('tramite', ''),
        ]);

        // ─── [Renderizado de vista] ─────────────────────────
        // Retorna la vista con los datos de casos y filtros
        return view('casos.index', $data);
    }

    /**
     * Muestra el formulario de creación de un nuevo caso.
     *
     * Precarga los catálogos de clientes activos, procuradores activos
     * y tipos de trámite para los campos select del formulario.
     *
     * Si el usuario es Procurador, se pasa su procurador vinculado para
     * preseleccionarlo y ocultar el selector (el service lo fuerza igualmente).
     *
     * @return View Vista create con catálogos precargados
     */
    public function create()
    {
        // ─── [Precarga de catálogos para selects] ──────────
        // Obtiene clientes activos, procuradores activos y
        // tipos de trámite para poblar los campos del formulario
        $clientes = Cliente::where('cliente_estado', 'activo')->get();
        $procuradores = Procurador::where('procurador_estado', 'activo')->get();
        $tramites = TipoTramite::all();

        // ─── [Variables para el formulario de Procurador] ──
        // Si el usuario es Procurador, su procurador vinculado se
        // preselecciona (y el service lo fuerza al crear el caso)
        $user = auth()->user();
        $esProcurador = RolEnum::equals($user->rol?->rol_nombre, RolEnum::PROCURADOR);
        $procuradorSeleccionado = $esProcurador ? $user->procurador_id : null;
        $procuradorNombre = $procuradorSeleccionado ? optional(Procurador::find($procuradorSeleccionado))->nombre_completo : null;

        // ─── [Renderizado de vista] ─────────────────────────
        return view('casos.create', compact('clientes', 'procuradores', 'tramites', 'esProcurador', 'procuradorSeleccionado', 'procuradorNombre'));
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
        // ─── [Validación y preparación de datos] ────────────
        // Obtiene los datos validados por StoreCasoRequest y
        // convierte el campo 'caso_admisible' a booleano
        $validated = $request->validated();
        $validated['caso_admisible'] = $request->boolean('caso_admisible', true);

        // ─── [Delegar creación al servicio] ─────────────────
        // CasoService se encarga de la lógica de persistencia
        // (incluye la autoasignación del procurador para rol Procurador)
        try {
            $this->casoService->createCaso($validated);
        } catch (\RuntimeException $e) {
            // ─── [Error amigable: cuenta sin procurador vinculado] ──
            // En lugar de un error 500, se devuelve al formulario con
            // el mensaje claro y los datos ya ingresados preservados.
            return back()
                ->withInput()
                ->withErrors(['procurador_id' => $e->getMessage()]);
        }

        // ─── [Redirección con mensaje de éxito] ─────────────
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
        // ─── [Consulta del caso con todas sus relaciones] ───
        // Carga el caso con 7 relaciones (cliente, demandado,
        // tipoTrámite, estado, procurador, entrevistas, seguimientos,
        // audiencias y documentos) en eager loading para evitar N+1
        $caso = Caso::with([
            'cliente', 'demandado', 'tipoTramite', 'estado', 'procurador',
            'entrevistas.procurador', 'seguimientos.usuario',
            'audiencias.procurador', 'documentos',
        ])
            ->where('caso_numero_expediente', $expediente)
            ->firstOrFail();

        // ─── [Autorización: permiso 'view'] ─────────────────
        // Verifica que el usuario tenga permiso para ver este caso
        // mediante el Gate definido en App\Policies\CasoPolicy
        Gate::authorize('view', $caso);

        // ─── [Renderizado de vista] ─────────────────────────
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
        // ─── [Buscar el caso por expediente] ────────────────
        $caso = Caso::where('caso_numero_expediente', $expediente)->firstOrFail();

        // ─── [Autorización: permiso 'update'] ───────────────
        Gate::authorize('update', $caso);

        // ─── [Precarga de catálogos para selects] ──────────
        // Obtiene clientes activos, procuradores activos, tipos de
        // trámite, estados ordenados y demandados activos
        $clientes = Cliente::where('cliente_estado', 'activo')->get();
        $procuradores = Procurador::where('procurador_estado', 'activo')->get();
        $tramites = TipoTramite::all();
        $estados = EstadoCaso::orderBy('estado_orden')->get();
        $demandados = Demandado::where('demandado_estado', 'activo')->get();

        // ─── [Renderizado de vista] ─────────────────────────
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
        // ─── [Obtener el caso validado desde el Form Request] ──
        // El UpdateCasoRequest ya cargó el caso y verificó permisos
        $caso = $request->caso;

        // ─── [Validación y preparación de datos] ────────────
        $validated = $request->validated();

        // ─── [Si el usuario es Director] ────────────────────
        // Solo el Director puede modificar el campo 'caso_admisible'
        if ($request->esDirector()) {
            $validated['caso_admisible'] = $request->boolean('caso_admisible', true);
        }

        // ─── [Delegar actualización al servicio] ────────────
        $this->casoService->updateCaso($caso, $validated);

        // ─── [Redirección a vista de detalle] ───────────────
        return redirect()->route('casos.show', $expediente)
            ->with('success', 'Caso actualizado exitosamente.');
    }

    /**
     * Muestra el formulario de cierre de caso con resolución.
     *
     * Verifica permiso 'delete' mediante Gate (solo Director).
     *
     * @param  string  $expediente  Número de expediente del caso
     * @return View Vista cerrar con el caso
     *
     * @throws AuthorizationException Si no tiene permiso 'delete'
     * @throws ModelNotFoundException Si el expediente no existe
     */
    public function cerrar(string $expediente)
    {
        // ─── [Buscar el caso por expediente] ────────────────
        $caso = Caso::where('caso_numero_expediente', $expediente)->firstOrFail();

        // ─── [Autorización: permiso 'delete' (solo Director)] ─
        Gate::authorize('delete', $caso);

        // ─── [Renderizado del formulario de cierre] ─────────
        return view('casos.cerrar', compact('caso'));
    }

    /**
     * Procesa el cierre de un caso con resolución.
     *
     * Valida los datos de resolución (tipo, fecha, notas) y delega
     * el cierre en CasoService::closeCaso(). Verifica permiso 'delete'
     * mediante Gate (solo Director).
     *
     * @param  Request  $request  Datos de resolución
     * @param  string  $expediente  Número de expediente del caso
     * @return RedirectResponse Redirección a vista show con mensaje
     *
     * @throws AuthorizationException Si no tiene permiso 'delete'
     * @throws ModelNotFoundException Si el expediente no existe
     */
    public function storeCerrar(Request $request, string $expediente)
    {
        // ─── [Buscar el caso por expediente] ────────────────
        $caso = Caso::where('caso_numero_expediente', $expediente)->firstOrFail();

        // ─── [Autorización: permiso 'delete' (solo Director)] ─
        Gate::authorize('delete', $caso);

        // ─── [Validación de datos de resolución] ────────────
        // El tipo de resolución debe ser uno de los valores
        // predefinidos: ganado, perdido, conciliado, desistido, desestimado
        $validated = $request->validate([
            'resolucion_tipo' => 'required|in:ganado,perdido,conciliado,desistido,desestimado',
            'resolucion_fecha' => 'required|date',
            'resolucion_notas' => 'nullable|string|max:2000',
        ]);

        // ─── [Delegar cierre al servicio] ───────────────────
        $this->casoService->closeCaso($caso, $validated);

        // ─── [Redirección a vista de detalle] ───────────────
        return redirect()->route('casos.show', $expediente)
            ->with('success', 'Caso cerrado exitosamente con resolución.');
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
        // ─── [Buscar el caso por expediente] ────────────────
        $caso = Caso::where('caso_numero_expediente', $expediente)->firstOrFail();

        // ─── [Autorización: permiso 'delete' (solo Director)] ─
        Gate::authorize('delete', $caso);

        // ─── [Desactivación lógica del caso] ────────────────
        // Conserva el registro en la BD para integridad histórica
        $this->casoService->deactivateCaso($caso);

        // ─── [Redirección al índice con mensaje] ────────────
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
        // ─── [Buscar el caso con su procurador actual] ─────
        $caso = Caso::with(['procurador'])->where('caso_numero_expediente', $expediente)->firstOrFail();

        // ─── [Autorización: permiso 'reasignar'] ────────────
        Gate::authorize('reasignar', $caso);

        // ─── [Obtener procuradores disponibles] ─────────────
        // Excluye al procurador actualmente asignado al caso
        // para evitar reasignación al mismo responsable
        $procuradores = Procurador::where('procurador_estado', 'activo')
            ->where('procurador_id', '!=', $caso->procurador_id)
            ->get();

        // ─── [Renderizado de vista] ─────────────────────────
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
        // ─── [Buscar el caso por expediente] ────────────────
        $caso = Caso::where('caso_numero_expediente', $expediente)->firstOrFail();

        // ─── [Autorización: permiso 'reasignar'] ────────────
        Gate::authorize('reasignar', $caso);

        // ─── [Validación de datos de reasignación] ──────────
        // El procurador destino debe existir, estar activo y
        // ser diferente del procurador origen actual
        $validated = $request->validate([
            'procurador_destino_id' => 'required|exists:procuradores,procurador_id|different:procurador_origen_id',
            'reasignacion_motivo' => 'required|string',
        ]);

        // ─── [Delegar reasignación al servicio] ─────────────
        $this->casoService->reassignCaso($caso, $validated);

        // ─── [Redirección a vista de detalle] ───────────────
        return redirect()->route('casos.show', $expediente)
            ->with('success', 'Caso reasignado exitosamente.');
    }
}
