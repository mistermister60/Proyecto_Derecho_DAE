<?php

namespace App\Http\Controllers;

use App\Enums\RolEnum;
use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * ═══════════════════════════════════════════════════════
 * CONTROLADOR: ClienteController
 * ═══════════════════════════════════════════════════════
 * CRUD completo de clientes con búsqueda por DNI, nombre,
 * apellido o teléfono. Los registros usan desactivación
 * lógica (cambio de estado) en lugar de eliminación física.
 * La búsqueda de registros se realiza por DNI (no por ID).
 * La creación incluye división automática del nombre completo
 * en nombre y apellido.
 * ───────────────────────────────────────────────────────
 * Rutas protegidas: middleware ['auth', 'otp', 'password.changed']
 * Roles: Director (ve todos), Procurador (solo sus clientes)
 * Validación: StoreClienteRequest / UpdateClienteRequest
 */
class ClienteController extends Controller
{
    /**
     * ═══════════════════════════════════════════════════════
     * index
     * ───────────────────────────────────────────────────────
     * Lista los clientes con paginación y búsqueda.
     * Incluye contador de casos asociados. Busca por DNI,
     * teléfono, nombre o apellido. Ordena por apellido y nombre.
     * Filtra por rol: Procurador solo ve sus clientes.
     * ═══════════════════════════════════════════════════════
     *
     * @param  Request  $request  Contiene el parámetro opcional 'search' y 'estado'
     * @return View Vista index con clientes paginados
     */
    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));
        $estado = $request->query('estado', '');

        // ─── [Consulta base con contador de casos] ────────────
        // Incluye withCount para mostrar número de casos por cliente
        // El closure filtra por procurador si el usuario es Procurador
        $clientes = Cliente::withCount(['casos' => function ($q) {
            // ─── [Si es Procurador: solo cuenta sus casos] ──────
            if (RolEnum::equals(auth()->user()->rol?->rol_nombre, RolEnum::PROCURADOR)) {
                $q->where('procurador_id', auth()->user()->procurador_id);
            }
        }])
            // ─── [Búsqueda por múltiples campos] ────────────────
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('cliente_dni', 'like', "%{$search}%")
                        ->orWhere('cliente_telefono', 'like', "%{$search}%")
                        ->orWhere('cliente_nombre', 'like', "%{$search}%")
                        ->orWhere('cliente_apellido', 'like', "%{$search}%");
                });
            })
            // ─── [Filtro por estado activo/inactivo] ────────────
            ->when($estado, function ($query, $estado) {
                $query->where('cliente_estado', $estado);
            })
            // ─── [Ordenamiento y paginación] ────────────────────
            ->orderBy('cliente_apellido')
            ->orderBy('cliente_nombre')
            ->paginate(20);

        // ─── [Renderizado de vista] ────────────────────────────
        return view('clientes.index', compact('clientes'));
    }

    /**
     * ═══════════════════════════════════════════════════════
     * search
     * ───────────────────────────────────────────────────────
     * Busca clientes para autocomplete/typeahead (API).
     * Retorna JSON con clientes activos que coinciden.
     * ═══════════════════════════════════════════════════════
     *
     * @param  Request  $request  Parámetros 'q' (query) y opcional 'limit'
     * @return JsonResponse JSON con clientes que coinciden
     */
    public function search(Request $request)
    {
        $query = trim($request->query('q', ''));
        $limit = min((int) $request->query('limit', 10), 50);

        if ($query === '') {
            return response()->json([]);
        }

        // ─── [Consulta de búsqueda con filtros] ───────────────
        $clientes = Cliente::where(function ($queryBuilder) use ($query) {
            $queryBuilder->where('cliente_dni', 'like', "%{$query}%")
                ->orWhere('cliente_telefono', 'like', "%{$query}%")
                ->orWhere('cliente_nombre', 'like', "%{$query}%")
                ->orWhere('cliente_apellido', 'like', "%{$query}%");
        })
            ->where('cliente_estado', 'activo')
            ->orderBy('cliente_apellido')
            ->orderBy('cliente_nombre')
            ->limit($limit)
            ->get(['cliente_dni', 'cliente_nombre', 'cliente_apellido', 'cliente_telefono', 'cliente_estado'])
            // ─── [Mapeo a formato para typeahead] ──────────────
            ->map(function ($cliente) {
                return [
                    'id' => $cliente->cliente_dni,
                    'nombre_completo' => $cliente->nombre_completo,
                    'dni' => $cliente->cliente_dni,
                    'telefono' => $cliente->cliente_telefono,
                    'estado' => $cliente->cliente_estado,
                    'url' => route('clientes.show', $cliente->cliente_dni),
                ];
            });

        return response()->json($clientes);
    }

    /**
     * ═══════════════════════════════════════════════════════
     * create
     * ───────────────────────────────────────────────────────
     * Muestra el formulario de creación de un nuevo cliente.
     * ═══════════════════════════════════════════════════════
     *
     * @return View Vista create del formulario
     */
    public function create()
    {
        return view('clientes.create');
    }

    /**
     * ═══════════════════════════════════════════════════════
     * store
     * ───────────────────────────────────────────────────────
     * Registra un nuevo cliente en el sistema.
     * Valida datos personales, laborales y familiares.
     * Divide el campo 'nombre_completo' en nombre y apellido.
     * Asigna estado 'activo' por defecto.
     * ═══════════════════════════════════════════════════════
     *
     * @param  Request  $request  Datos del cliente con nombre_completo
     * @return RedirectResponse Redirección al índice con mensaje
     */
    public function store(StoreClienteRequest $request)
    {
        $validated = $request->validated();

        // ─── [División del nombre completo] ────────────────────
        // Separa el nombre_completo en nombre (primera palabra)
        // y apellido (resto) usando regex para espacios múltiples
        $parts = preg_split('/\s+/', trim($validated['nombre_completo']), 2);
        $data = [
            'cliente_nombre' => $parts[0] ?? '',
            'cliente_apellido' => $parts[1] ?? '',
            'cliente_dni' => $validated['cliente_dni'],
            'cliente_estado_civil' => $validated['cliente_estado_civil'],
            'cliente_telefono' => $validated['cliente_telefono'],
            'cliente_direccion' => $validated['cliente_direccion'],
            'cliente_numero_hijos' => $validated['cliente_numero_hijos'] ?? 0,
            'cliente_nombres_hijos' => $validated['cliente_nombres_hijos'] ?? null,
            'cliente_profesion' => $validated['cliente_profesion'] ?? null,
            'cliente_lugar_trabajo' => $validated['cliente_lugar_trabajo'] ?? null,
            'cliente_direccion_trabajo' => $validated['cliente_direccion_trabajo'] ?? null,
            'cliente_telefono_trabajo' => $validated['cliente_telefono_trabajo'] ?? null,
            'cliente_salario_mensual' => $validated['cliente_salario_mensual'] ?? null,
            'cliente_estado' => 'activo',
        ];

        // ─── [Creación del registro] ───────────────────────────
        Cliente::create($data);

        // ─── [Redirección con mensaje] ─────────────────────────
        return redirect()->route('clientes.index')
            ->with('success', 'Cliente registrado exitosamente.');
    }

    /**
     * ═══════════════════════════════════════════════════════
     * show
     * ───────────────────────────────────────────────────────
     * Muestra los detalles de un cliente con sus casos asociados.
     * Realiza eager loading de casos con estado, tipo de trámite y procurador.
     * Los clientes son compartidos: cualquier usuario autenticado puede verlos.
     * ═══════════════════════════════════════════════════════
     *
     * @param  string  $identidad  Número de DNI del cliente
     * @return View Vista show con cliente y relaciones
     *
     * @throws ModelNotFoundException Si el DNI no existe
     */
    public function show(string $identidad)
    {
        // ─── [Consulta con eager loading] ──────────────────────
        $cliente = Cliente::with(['casos.estado', 'casos.tipoTramite', 'casos.procurador'])
            ->where('cliente_dni', $identidad)
            ->firstOrFail();

        // ─── [Renderizado de vista] ────────────────────────────
        return view('clientes.show', compact('cliente'));
    }

    /**
     * ═══════════════════════════════════════════════════════
     * edit
     * ───────────────────────────────────────────────────────
     * Muestra el formulario de edición de un cliente.
     * ═══════════════════════════════════════════════════════
     *
     * @param  string  $identidad  Número de DNI del cliente
     * @return View Vista edit con datos del cliente
     *
     * @throws ModelNotFoundException Si el DNI no existe
     */
    public function edit(string $identidad)
    {
        // ─── [Búsqueda del cliente por DNI] ────────────────────
        $cliente = Cliente::where('cliente_dni', $identidad)->firstOrFail();

        // ─── [Renderizado de vista] ────────────────────────────
        return view('clientes.edit', compact('cliente'));
    }

    /**
     * ═══════════════════════════════════════════════════════
     * update
     * ───────────────────────────────────────────────────────
     * Actualiza los datos de un cliente existente.
     * Valida todos los campos editables. Excluye el DNI actual
     * de la validación única para permitir mantener el mismo valor.
     * ═══════════════════════════════════════════════════════
     *
     * @param  Request  $request  Datos actualizados del cliente
     * @param  string  $identidad  Número de DNI del cliente
     * @return RedirectResponse Redirección a vista show con mensaje
     *
     * @throws ModelNotFoundException Si el DNI no existe
     */
    public function update(UpdateClienteRequest $request, string $identidad)
    {
        // ─── [Búsqueda del cliente] ────────────────────────────
        $cliente = Cliente::where('cliente_dni', $identidad)->firstOrFail();

        // ─── [Actualización con datos validados] ───────────────
        $validated = $request->validated();
        $cliente->update($validated);

        // ─── [Redirección con mensaje] ─────────────────────────
        return redirect()->route('clientes.show', $identidad)
            ->with('success', 'Cliente actualizado exitosamente.');
    }

    /**
     * ═══════════════════════════════════════════════════════
     * destroy
     * ───────────────────────────────────────────────────────
     * Desactiva un cliente (eliminación lógica).
     * Cambia el estado del cliente a 'inactivo'. El registro
     * se conserva en la base de datos para integridad histórica.
     * ═══════════════════════════════════════════════════════
     *
     * @param  string  $identidad  Número de DNI del cliente
     * @return RedirectResponse Redirección al índice con mensaje
     *
     * @throws ModelNotFoundException Si el DNI no existe
     */
    public function destroy(string $identidad)
    {
        // ─── [Búsqueda y desactivación] ────────────────────────
        $cliente = Cliente::where('cliente_dni', $identidad)->firstOrFail();
        $cliente->update(['cliente_estado' => 'inactivo']);

        // ─── [Redirección con mensaje] ─────────────────────────
        return redirect()->route('clientes.index')
            ->with('success', 'Cliente desactivado exitosamente. El registro se conserva en el sistema.');
    }

    /**
     * ═══════════════════════════════════════════════════════
     * activar
     * ───────────────────────────────────────────────────────
     * Reactiva un cliente previamente desactivado.
     * Cambia el estado del cliente a 'activo'.
     * ═══════════════════════════════════════════════════════
     *
     * @param  string  $identidad  Número de DNI del cliente
     * @return RedirectResponse Redirección a vista show con mensaje
     *
     * @throws ModelNotFoundException Si el DNI no existe
     */
    public function activar(string $identidad)
    {
        // ─── [Búsqueda y reactivación] ─────────────────────────
        $cliente = Cliente::where('cliente_dni', $identidad)->firstOrFail();
        $cliente->update(['cliente_estado' => 'activo']);

        // ─── [Redirección con mensaje] ─────────────────────────
        return redirect()->route('clientes.show', $identidad)
            ->with('success', 'Cliente reactivado exitosamente.');
    }
}
