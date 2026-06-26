<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Caso;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::withCount('casos')
            ->orderBy('cliente_apellido')
            ->orderBy('cliente_nombre')
            ->get();

        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_nombre' => 'required|string|max:100',
            'cliente_apellido' => 'required|string|max:100',
            'cliente_dni' => 'required|string|max:20|unique:clientes,cliente_dni',
            'cliente_estado_civil' => 'required|string|max:30',
            'cliente_telefono' => 'required|string|max:20',
            'cliente_direccion' => 'required|string',
            'cliente_numero_hijos' => 'nullable|integer|min:0',
            'cliente_profesion' => 'nullable|string|max:100',
            'cliente_lugar_trabajo' => 'nullable|string|max:100',
            'cliente_salario_mensual' => 'nullable|numeric|min:0',
        ]);

        $validated['cliente_estado'] = 'activo';

        Cliente::create($validated);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente registrado exitosamente.');
    }

    public function show(string $identidad)
    {
        $cliente = Cliente::with(['casos.estado', 'casos.tipoTramite', 'casos.procurador'])
            ->where('cliente_dni', $identidad)
            ->firstOrFail();

        return view('clientes.show', compact('cliente'));
    }

    public function edit(string $identidad)
    {
        $cliente = Cliente::where('cliente_dni', $identidad)->firstOrFail();
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, string $identidad)
    {
        $cliente = Cliente::where('cliente_dni', $identidad)->firstOrFail();

        $validated = $request->validate([
            'cliente_nombre' => 'required|string|max:100',
            'cliente_apellido' => 'required|string|max:100',
            'cliente_dni' => 'required|string|max:20|unique:clientes,cliente_dni,' . $cliente->cliente_id . ',cliente_id',
            'cliente_estado_civil' => 'required|string|max:30',
            'cliente_telefono' => 'required|string|max:20',
            'cliente_direccion' => 'required|string',
            'cliente_numero_hijos' => 'nullable|integer|min:0',
            'cliente_profesion' => 'nullable|string|max:100',
            'cliente_lugar_trabajo' => 'nullable|string|max:100',
            'cliente_salario_mensual' => 'nullable|numeric|min:0',
        ]);

        $cliente->update($validated);

        return redirect()->route('clientes.show', $identidad)
            ->with('success', 'Cliente actualizado exitosamente.');
    }

    public function destroy(string $identidad)
    {
        $cliente = Cliente::where('cliente_dni', $identidad)->firstOrFail();
        $cliente->update(['cliente_estado' => 'inactivo']);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente desactivado exitosamente. El registro se conserva en el sistema.');
    }
}
