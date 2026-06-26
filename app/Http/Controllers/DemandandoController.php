<?php

namespace App\Http\Controllers;

use App\Models\Demandado;
use Illuminate\Http\Request;

class DemandandoController extends Controller
{
    public function index()
    {
        $demandados = Demandado::withCount('casos')
            ->orderBy('demandado_apellido')
            ->orderBy('demandado_nombre')
            ->get();

        return view('demandados.index', compact('demandados'));
    }

    public function create()
    {
        return view('demandados.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'demandado_nombre' => 'required|string|max:100',
            'demandado_apellido' => 'required|string|max:100',
            'demandado_dni' => 'required|string|max:19|unique:demandados,demandado_dni',
            'demandado_estado_civil' => 'nullable|string|max:50',
            'demandado_telefono' => 'nullable|string|max:29',
            'demandado_direccion' => 'nullable|string|max:200',
            'demandado_profesion' => 'nullable|string|max:200',
            'demandado_lugar_trabajo' => 'nullable|string|max:350',
            'demandado_telefono_trabajo' => 'nullable|string|max:29',
        ]);

        $validated['demandado_estado'] = 'activo';

        Demandado::create($validated);

        return redirect()->route('demandados.index')
            ->with('success', 'Demandado registrado exitosamente.');
    }

    public function show(string $identidad)
    {
        $demandado = Demandado::with(['casos.estado', 'casos.tipoTramite', 'casos.procurador'])
            ->where('demandado_dni', $identidad)
            ->firstOrFail();

        return view('demandados.show', compact('demandado'));
    }

    public function edit(string $identidad)
    {
        $demandado = Demandado::where('demandado_dni', $identidad)->firstOrFail();

        return view('demandados.edit', compact('demandado'));
    }

    public function update(Request $request, string $identidad)
    {
        $demandado = Demandado::where('demandado_dni', $identidad)->firstOrFail();

        $validated = $request->validate([
            'demandado_nombre' => 'required|string|max:100',
            'demandado_apellido' => 'required|string|max:100',
            'demandado_dni' => 'required|string|max:19|unique:demandados,demandado_dni,'.$demandado->demandado_id.',demandado_id',
            'demandado_estado_civil' => 'nullable|string|max:50',
            'demandado_telefono' => 'nullable|string|max:29',
            'demandado_direccion' => 'nullable|string|max:200',
            'demandado_profesion' => 'nullable|string|max:200',
            'demandado_lugar_trabajo' => 'nullable|string|max:350',
            'demandado_telefono_trabajo' => 'nullable|string|max:29',
        ]);

        $demandado->update($validated);

        return redirect()->route('demandados.show', $identidad)
            ->with('success', 'Demandado actualizado exitosamente.');
    }

    public function destroy(string $identidad)
    {
        $demandado = Demandado::where('demandado_dni', $identidad)->firstOrFail();
        $demandado->update(['demandado_estado' => 'inactivo']);

        return redirect()->route('demandados.index')
            ->with('success', 'Demandado desactivado exitosamente. El registro se conserva en el sistema.');
    }

    public function activar(string $identidad)
    {
        $demandado = Demandado::where('demandado_dni', $identidad)->firstOrFail();
        $demandado->update(['demandado_estado' => 'activo']);

        return redirect()->route('demandados.show', $identidad)
            ->with('success', 'Demandado reactivado exitosamente.');
    }
}
