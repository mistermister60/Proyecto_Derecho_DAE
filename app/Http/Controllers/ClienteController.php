<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\UpdateClienteRequest;
use App\Models\Cliente;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controlador para la gestión de clientes.
 *
 * CRUD completo de clientes con búsqueda por DNI, nombre, apellido o teléfono.
 * Los registros usan desactivación lógica (cambio de estado) en lugar de
 * eliminación física. La búsqueda de registros se realiza por DNI (no por ID).
 * La creación incluye división automática del nombre completo en nombre y apellido.
 */
class ClienteController extends Controller
{
    /**
     * Lista los clientes con paginación y búsqueda.
     *
     * Incluye contador de casos asociados. Busca por DNI, teléfono, nombre
     * o apellido. Ordena por apellido y nombre.
     *
     * @param  Request  $request  Contiene el parámetro opcional 'search'
     * @return View Vista index con clientes paginados
     */
    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));

        $clientes = Cliente::withCount('casos')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('cliente_dni', 'like', "%{$search}%")
                        ->orWhere('cliente_telefono', 'like', "%{$search}%")
                        ->orWhere('cliente_nombre', 'like', "%{$search}%")
                        ->orWhere('cliente_apellido', 'like', "%{$search}%");
                });
            })
            ->orderBy('cliente_apellido')
            ->orderBy('cliente_nombre')
            ->paginate(20);

        return view('clientes.index', compact('clientes'));
    }

    /**
     * Muestra el formulario de creación de un nuevo cliente.
     *
     * @return View Vista create del formulario
     */
    public function create()
    {
        return view('clientes.create');
    }

    /**
     * Registra un nuevo cliente en el sistema.
     *
     * Valida datos personales, laborales y familiares. Divide el campo
     * 'nombre_completo' en nombre y apellido. Asigna estado 'activo'
     * por defecto.
     *
     * @param  Request  $request  Datos del cliente con nombre_completo
     * @return RedirectResponse Redirección al índice con mensaje
     */
    public function store(StoreClienteRequest $request)
    {
        $validated = $request->validated();

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

        Cliente::create($data);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente registrado exitosamente.');
    }

    /**
     * Muestra los detalles de un cliente con sus casos asociados.
     *
     * Realiza eager loading de casos con estado, tipo de trámite y procurador.
     *
     * @param  string  $identidad  Número de DNI del cliente
     * @return View Vista show con cliente y relaciones
     *
     * @throws ModelNotFoundException Si el DNI no existe
     */
    public function show(string $identidad)
    {
        $cliente = Cliente::with(['casos.estado', 'casos.tipoTramite', 'casos.procurador'])
            ->where('cliente_dni', $identidad)
            ->firstOrFail();

        return view('clientes.show', compact('cliente'));
    }

    /**
     * Muestra el formulario de edición de un cliente.
     *
     * @param  string  $identidad  Número de DNI del cliente
     * @return View Vista edit con datos del cliente
     *
     * @throws ModelNotFoundException Si el DNI no existe
     */
    public function edit(string $identidad)
    {
        $cliente = Cliente::where('cliente_dni', $identidad)->firstOrFail();

        return view('clientes.edit', compact('cliente'));
    }

    /**
     * Actualiza los datos de un cliente existente.
     *
     * Valida todos los campos editables. Excluye el DNI actual de la
     * validación única para permitir mantener el mismo valor.
     *
     * @param  Request  $request  Datos actualizados del cliente
     * @param  string  $identidad  Número de DNI del cliente
     * @return RedirectResponse Redirección a vista show con mensaje
     *
     * @throws ModelNotFoundException Si el DNI no existe
     */
    public function update(UpdateClienteRequest $request, string $identidad)
    {
        $cliente = Cliente::where('cliente_dni', $identidad)->firstOrFail();

        $validated = $request->validated();

        $cliente->update($validated);

        return redirect()->route('clientes.show', $identidad)
            ->with('success', 'Cliente actualizado exitosamente.');
    }

    /**
     * Desactiva un cliente (eliminación lógica).
     *
     * Cambia el estado del cliente a 'inactivo'. El registro se conserva
     * en la base de datos para integridad histórica.
     *
     * @param  string  $identidad  Número de DNI del cliente
     * @return RedirectResponse Redirección al índice con mensaje
     *
     * @throws ModelNotFoundException Si el DNI no existe
     */
    public function destroy(string $identidad)
    {
        $cliente = Cliente::where('cliente_dni', $identidad)->firstOrFail();
        $cliente->update(['cliente_estado' => 'inactivo']);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente desactivado exitosamente. El registro se conserva en el sistema.');
    }

    /**
     * Reactiva un cliente previamente desactivado.
     *
     * Cambia el estado del cliente a 'activo'.
     *
     * @param  string  $identidad  Número de DNI del cliente
     * @return RedirectResponse Redirección a vista show con mensaje
     *
     * @throws ModelNotFoundException Si el DNI no existe
     */
    public function activar(string $identidad)
    {
        $cliente = Cliente::where('cliente_dni', $identidad)->firstOrFail();
        $cliente->update(['cliente_estado' => 'activo']);

        return redirect()->route('clientes.show', $identidad)
            ->with('success', 'Cliente reactivado exitosamente.');
    }
}
