<?php

namespace App\Http\Controllers;

use App\Enums\RolEnum;
use App\Http\Requests\StoreDemandadoRequest;
use App\Http\Requests\UpdateDemandadoRequest;
use App\Models\Demandado;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * ═══════════════════════════════════════════════════════
 * CONTROLADOR: DemandadoController
 * ═══════════════════════════════════════════════════════
 * CRUD completo de demandados con búsqueda por DNI, nombre,
 * apellido o teléfono. Los registros usan desactivación
 * lógica (cambio de estado) en lugar de eliminación física.
 * La búsqueda de registros se realiza por DNI (no por ID).
 * Sigue el mismo patrón que ClienteController.
 * ───────────────────────────────────────────────────────
 * Rutas protegidas: middleware ['auth', 'otp', 'password.changed']
 * Roles: Director (ve todos), Procurador (solo sus demandados)
 * Validación: StoreDemandadoRequest / UpdateDemandadoRequest
 */
class DemandadoController extends Controller
{
    /**
     * ═══════════════════════════════════════════════════════
     * index
     * ───────────────────────────────────────────────────────
     * Lista los demandados con paginación y búsqueda.
     * Incluye contador de casos asociados. Busca por DNI,
     * teléfono, nombre o apellido. Ordena por apellido y nombre.
     * Filtra por rol: Procurador solo ve sus demandados.
     * ═══════════════════════════════════════════════════════
     *
     * @param  Request  $request  Contiene el parámetro opcional 'search'
     * @return View Vista index con demandados paginados
     */
    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));

        // ─── [Consulta base con contador de casos] ────────────
        $demandados = Demandado::withCount(['casos' => function ($q) {
            // ─── [Si es Procurador: solo cuenta sus casos] ──────
            if (RolEnum::equals(auth()->user()->rol?->rol_nombre, RolEnum::PROCURADOR)) {
                $q->where('procurador_id', auth()->user()->procurador_id);
            }
        }])
            // ─── [Búsqueda por múltiples campos] ────────────────
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('demandado_dni', 'like', "%{$search}%")
                        ->orWhere('demandado_telefono', 'like', "%{$search}%")
                        ->orWhere('demandado_nombre', 'like', "%{$search}%")
                        ->orWhere('demandado_apellido', 'like', "%{$search}%");
                });
            })
            // ─── [Ordenamiento y paginación] ────────────────────
            ->orderBy('demandado_apellido')
            ->orderBy('demandado_nombre')
            ->paginate(20);

        // ─── [Renderizado de vista] ────────────────────────────
        return view('demandados.index', compact('demandados'));
    }

    /**
     * ═══════════════════════════════════════════════════════
     * create
     * ───────────────────────────────────────────────────────
     * Muestra el formulario de creación de un nuevo demandado.
     * ═══════════════════════════════════════════════════════
     *
     * @return View Vista create del formulario
     */
    public function create()
    {
        return view('demandados.create');
    }

    /**
     * ═══════════════════════════════════════════════════════
     * store
     * ───────────────────────────────────────────────────────
     * Registra un nuevo demandado en el sistema.
     * Valida datos personales y laborales. Asigna estado 'activo'
     * por defecto.
     * ═══════════════════════════════════════════════════════
     *
     * @param  Request  $request  Datos del demandado
     * @return RedirectResponse Redirección al índice con mensaje
     */
    public function store(StoreDemandadoRequest $request)
    {
        $validated = $request->validated();

        // ─── [Asignación de estado por defecto] ────────────────
        $validated['demandado_estado'] = 'activo';

        // ─── [Creación del registro] ───────────────────────────
        Demandado::create($validated);

        // ─── [Redirección con mensaje] ─────────────────────────
        return redirect()->route('demandados.index')
            ->with('success', 'Demandado registrado exitosamente.');
    }

    /**
     * ═══════════════════════════════════════════════════════
     * show
     * ───────────────────────────────────────────────────────
     * Muestra los detalles de un demandado con sus casos asociados.
     * Realiza eager loading de casos con estado, tipo de trámite y procurador.
     * Los demandados son compartidos: cualquier usuario autenticado puede verlos.
     * ═══════════════════════════════════════════════════════
     *
     * @param  string  $identidad  Número de DNI del demandado
     * @return View Vista show con demandado y relaciones
     *
     * @throws ModelNotFoundException Si el DNI no existe
     */
    public function show(string $identidad)
    {
        // ─── [Consulta con eager loading] ──────────────────────
        $demandado = Demandado::with(['casos.estado', 'casos.tipoTramite', 'casos.procurador'])
            ->where('demandado_dni', $identidad)
            ->firstOrFail();

        // ─── [Renderizado de vista] ────────────────────────────
        return view('demandados.show', compact('demandado'));
    }

    /**
     * ═══════════════════════════════════════════════════════
     * edit
     * ───────────────────────────────────────────────────────
     * Muestra el formulario de edición de un demandado.
     * ═══════════════════════════════════════════════════════
     *
     * @param  string  $identidad  Número de DNI del demandado
     * @return View Vista edit con datos del demandado
     *
     * @throws ModelNotFoundException Si el DNI no existe
     */
    public function edit(string $identidad)
    {
        // ─── [Búsqueda del demandado por DNI] ──────────────────
        $demandado = Demandado::where('demandado_dni', $identidad)->firstOrFail();

        // ─── [Renderizado de vista] ────────────────────────────
        return view('demandados.edit', compact('demandado'));
    }

    /**
     * ═══════════════════════════════════════════════════════
     * update
     * ───────────────────────────────────────────────────────
     * Actualiza los datos de un demandado existente.
     * Valida campos editables. Excluye el DNI actual de la
     * validación única para permitir mantener el mismo valor.
     * ═══════════════════════════════════════════════════════
     *
     * @param  Request  $request  Datos actualizados del demandado
     * @param  string  $identidad  Número de DNI del demandado
     * @return RedirectResponse Redirección a vista show con mensaje
     *
     * @throws ModelNotFoundException Si el DNI no existe
     */
    public function update(UpdateDemandadoRequest $request, string $identidad)
    {
        // ─── [Búsqueda del demandado] ──────────────────────────
        $demandado = Demandado::where('demandado_dni', $identidad)->firstOrFail();

        // ─── [Actualización con datos validados] ───────────────
        $validated = $request->validated();
        $demandado->update($validated);

        // ─── [Redirección con mensaje] ─────────────────────────
        return redirect()->route('demandados.show', $identidad)
            ->with('success', 'Demandado actualizado exitosamente.');
    }

    /**
     * ═══════════════════════════════════════════════════════
     * destroy
     * ───────────────────────────────────────────────────────
     * Desactiva un demandado (eliminación lógica).
     * Cambia el estado del demandado a 'inactivo'. El registro
     * se conserva en la base de datos para integridad histórica.
     * ═══════════════════════════════════════════════════════
     *
     * @param  string  $identidad  Número de DNI del demandado
     * @return RedirectResponse Redirección al índice con mensaje
     *
     * @throws ModelNotFoundException Si el DNI no existe
     */
    public function destroy(string $identidad)
    {
        // ─── [Búsqueda y desactivación] ────────────────────────
        $demandado = Demandado::where('demandado_dni', $identidad)->firstOrFail();
        $demandado->update(['demandado_estado' => 'inactivo']);

        // ─── [Redirección con mensaje] ─────────────────────────
        return redirect()->route('demandados.index')
            ->with('success', 'Demandado desactivado exitosamente. El registro se conserva en el sistema.');
    }

    /**
     * ═══════════════════════════════════════════════════════
     * activar
     * ───────────────────────────────────────────────────────
     * Reactiva un demandado previamente desactivado.
     * Cambia el estado del demandado a 'activo'.
     * ═══════════════════════════════════════════════════════
     *
     * @param  string  $identidad  Número de DNI del demandado
     * @return RedirectResponse Redirección a vista show con mensaje
     *
     * @throws ModelNotFoundException Si el DNI no existe
     */
    public function activar(string $identidad)
    {
        // ─── [Búsqueda y reactivación] ─────────────────────────
        $demandado = Demandado::where('demandado_dni', $identidad)->firstOrFail();
        $demandado->update(['demandado_estado' => 'activo']);

        // ─── [Redirección con mensaje] ─────────────────────────
        return redirect()->route('demandados.show', $identidad)
            ->with('success', 'Demandado reactivado exitosamente.');
    }
}
