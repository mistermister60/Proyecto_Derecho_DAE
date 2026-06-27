<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
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
            'nombre_completo' => 'required|string|max:200',
            'cliente_dni' => 'required|string|max:20|unique:clientes,cliente_dni',
            'cliente_estado_civil' => 'required|string|max:30',
            'cliente_telefono' => 'required|string|max:20',
            'cliente_direccion' => 'required|string',
            'cliente_numero_hijos' => 'nullable|integer|min:0',
            'cliente_profesion' => 'nullable|string|max:100',
            'cliente_lugar_trabajo' => 'nullable|string|max:100',
            'cliente_direccion_trabajo' => 'nullable|string|max:350',
            'cliente_telefono_trabajo' => 'nullable|string|max:29',
            'cliente_salario_mensual' => 'nullable|numeric|min:0',
        ]);

        $parts = preg_split('/\s+/', trim($validated['nombre_completo']), 2);
        $data = [
            'cliente_nombre' => $parts[0] ?? '',
            'cliente_apellido' => $parts[1] ?? '',
            'cliente_dni' => $validated['cliente_dni'],
            'cliente_estado_civil' => $validated['cliente_estado_civil'],
            'cliente_telefono' => $validated['cliente_telefono'],
            'cliente_direccion' => $validated['cliente_direccion'],
            'cliente_numero_hijos' => $validated['cliente_numero_hijos'] ?? 0,
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
            'cliente_dni' => 'required|string|max:20|unique:clientes,cliente_dni,'.$cliente->cliente_id.',cliente_id',
            'cliente_estado_civil' => 'required|string|max:30',
            'cliente_telefono' => 'required|string|max:20',
            'cliente_direccion' => 'required|string',
            'cliente_numero_hijos' => 'nullable|integer|min:0',
            'cliente_profesion' => 'nullable|string|max:100',
            'cliente_lugar_trabajo' => 'nullable|string|max:100',
            'cliente_direccion_trabajo' => 'nullable|string|max:350',
            'cliente_telefono_trabajo' => 'nullable|string|max:29',
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

    public function activar(string $identidad)
    {
        $cliente = Cliente::where('cliente_dni', $identidad)->firstOrFail();
        $cliente->update(['cliente_estado' => 'activo']);

        return redirect()->route('clientes.show', $identidad)
            ->with('success', 'Cliente reactivado exitosamente.');
    }
}
