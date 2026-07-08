<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDemandadoRequest;
use App\Http\Requests\UpdateDemandadoRequest;
use App\Models\Demandado;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controlador para la gestión de demandados.
 *
 * CRUD completo de demandados con búsqueda por DNI, nombre, apellido o teléfono.
 * Los registros usan desactivación lógica (cambio de estado) en lugar de
 * eliminación física. La búsqueda de registros se realiza por DNI (no por ID).
 * Sigue el mismo patrón que ClienteController.
 */
class DemandadoController extends Controller
{
    /**
     * Lista los demandados con paginación y búsqueda.
     *
     * Incluye contador de casos asociados. Busca por DNI, teléfono, nombre
     * o apellido. Ordena por apellido y nombre.
     *
     * @param  Request  $request  Contiene el parámetro opcional 'search'
     * @return View Vista index con demandados paginados
     */
    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));

        $demandados = Demandado::withCount('casos')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('demandado_dni', 'like', "%{$search}%")
                        ->orWhere('demandado_telefono', 'like', "%{$search}%")
                        ->orWhere('demandado_nombre', 'like', "%{$search}%")
                        ->orWhere('demandado_apellido', 'like', "%{$search}%");
                });
            })
            ->orderBy('demandado_apellido')
            ->orderBy('demandado_nombre')
            ->paginate(20);

        return view('demandados.index', compact('demandados'));
    }

    /**
     * Muestra el formulario de creación de un nuevo demandado.
     *
     * @return View Vista create del formulario
     */
    public function create()
    {
        return view('demandados.create');
    }

    /**
     * Registra un nuevo demandado en el sistema.
     *
     * Valida datos personales y laborales. Asigna estado 'activo' por defecto.
     *
     * @param  Request  $request  Datos del demandado
     * @return RedirectResponse Redirección al índice con mensaje
     */
    public function store(StoreDemandadoRequest $request)
    {
        $validated = $request->validated();

        $validated['demandado_estado'] = 'activo';

        Demandado::create($validated);

        return redirect()->route('demandados.index')
            ->with('success', 'Demandado registrado exitosamente.');
    }

    /**
     * Muestra los detalles de un demandado con sus casos asociados.
     *
     * Realiza eager loading de casos con estado, tipo de trámite y procurador.
     *
     * @param  string  $identidad  Número de DNI del demandado
     * @return View Vista show con demandado y relaciones
     *
     * @throws ModelNotFoundException Si el DNI no existe
     */
    public function show(string $identidad)
    {
        $demandado = Demandado::with(['casos.estado', 'casos.tipoTramite', 'casos.procurador'])
            ->where('demandado_dni', $identidad)
            ->firstOrFail();

        return view('demandados.show', compact('demandado'));
    }

    /**
     * Muestra el formulario de edición de un demandado.
     *
     * @param  string  $identidad  Número de DNI del demandado
     * @return View Vista edit con datos del demandado
     *
     * @throws ModelNotFoundException Si el DNI no existe
     */
    public function edit(string $identidad)
    {
        $demandado = Demandado::where('demandado_dni', $identidad)->firstOrFail();

        return view('demandados.edit', compact('demandado'));
    }

    /**
     * Actualiza los datos de un demandado existente.
     *
     * Valida campos editables. Excluye el DNI actual de la validación única
     * para permitir mantener el mismo valor.
     *
     * @param  Request  $request  Datos actualizados del demandado
     * @param  string  $identidad  Número de DNI del demandado
     * @return RedirectResponse Redirección a vista show con mensaje
     *
     * @throws ModelNotFoundException Si el DNI no existe
     */
    public function update(UpdateDemandadoRequest $request, string $identidad)
    {
        $demandado = Demandado::where('demandado_dni', $identidad)->firstOrFail();

        $validated = $request->validated();

        $demandado->update($validated);

        return redirect()->route('demandados.show', $identidad)
            ->with('success', 'Demandado actualizado exitosamente.');
    }

    /**
     * Desactiva un demandado (eliminación lógica).
     *
     * Cambia el estado del demandado a 'inactivo'. El registro se conserva
     * en la base de datos para integridad histórica.
     *
     * @param  string  $identidad  Número de DNI del demandado
     * @return RedirectResponse Redirección al índice con mensaje
     *
     * @throws ModelNotFoundException Si el DNI no existe
     */
    public function destroy(string $identidad)
    {
        $demandado = Demandado::where('demandado_dni', $identidad)->firstOrFail();
        $demandado->update(['demandado_estado' => 'inactivo']);

        return redirect()->route('demandados.index')
            ->with('success', 'Demandado desactivado exitosamente. El registro se conserva en el sistema.');
    }

    /**
     * Reactiva un demandado previamente desactivado.
     *
     * Cambia el estado del demandado a 'activo'.
     *
     * @param  string  $identidad  Número de DNI del demandado
     * @return RedirectResponse Redirección a vista show con mensaje
     *
     * @throws ModelNotFoundException Si el DNI no existe
     */
    public function activar(string $identidad)
    {
        $demandado = Demandado::where('demandado_dni', $identidad)->firstOrFail();
        $demandado->update(['demandado_estado' => 'activo']);

        return redirect()->route('demandados.show', $identidad)
            ->with('success', 'Demandado reactivado exitosamente.');
    }
}
