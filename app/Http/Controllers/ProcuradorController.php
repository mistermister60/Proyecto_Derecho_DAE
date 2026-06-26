<?php

namespace App\Http\Controllers;

use App\Models\Procurador;
use Illuminate\Http\Request;

class ProcuradorController extends Controller
{
    public function index()
    {
        $procuradores = Procurador::withCount('casos')
            ->orderBy('procurador_apellido')
            ->orderBy('procurador_nombre')
            ->get();

        return view('procuradores.index', compact('procuradores'));
    }

    public function create()
    {
        return view('procuradores.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'procurador_nombre' => 'required|string|max:100',
            'procurador_apellido' => 'required|string|max:100',
            'procurador_dni' => 'required|string|max:19|unique:procuradores,procurador_dni',
            'procurador_carnet' => 'nullable|string|max:20',
            'procurador_fecha_nacimiento' => 'nullable|date',
            'procurador_genero' => 'nullable|string|max:25',
            'procurador_email' => 'nullable|email|max:150',
            'procurador_telefono' => 'nullable|string|max:29',
            'procurador_direccion' => 'nullable|string|max:200',
        ]);

        $validated['procurador_estado'] = 'activo';

        Procurador::create($validated);

        return redirect()->route('procuradores.index')
            ->with('success', 'Procurador registrado exitosamente.');
    }

    public function show(string $identidad)
    {
        $procurador = Procurador::with(['casos.estado', 'casos.tipoTramite', 'casos.procurador'])
            ->where('procurador_dni', $identidad)
            ->firstOrFail();

        return view('procuradores.show', compact('procurador'));
    }

    public function edit(string $identidad)
    {
        $procurador = Procurador::where('procurador_dni', $identidad)->firstOrFail();

        return view('procuradores.edit', compact('procurador'));
    }

    public function update(Request $request, string $identidad)
    {
        $procurador = Procurador::where('procurador_dni', $identidad)->firstOrFail();

        $validated = $request->validate([
            'procurador_nombre' => 'required|string|max:100',
            'procurador_apellido' => 'required|string|max:100',
            'procurador_dni' => 'required|string|max:19|unique:procuradores,procurador_dni,'.$procurador->procurador_id.',procurador_id',
            'procurador_carnet' => 'nullable|string|max:20|unique:procuradores,procurador_carnet,'.$procurador->procurador_id.',procurador_id',
            'procurador_fecha_nacimiento' => 'required|date',
            'procurador_genero' => 'required|string|max:25',
            'procurador_email' => 'required|email|max:150|unique:procuradores,procurador_email,'.$procurador->procurador_id.',procurador_id',
            'procurador_telefono' => 'nullable|string|max:29',
            'procurador_direccion' => 'nullable|string|max:200',
        ]);

        $procurador->update($validated);

        return redirect()->route('procuradores.show', $identidad)
            ->with('success', 'Procurador actualizado exitosamente.');
    }

    public function destroy(string $identidad)
    {
        $procurador = Procurador::where('procurador_dni', $identidad)->firstOrFail();
        $procurador->update(['procurador_estado' => 'inactivo']);

        return redirect()->route('procuradores.index')
            ->with('success', 'Procurador desactivado exitosamente. El registro se conserva en el sistema.');
    }

    public function activar(string $identidad)
    {
        $procurador = Procurador::where('procurador_dni', $identidad)->firstOrFail();
        $procurador->update(['procurador_estado' => 'activo']);

        return redirect()->route('procuradores.show', $identidad)
            ->with('success', 'Procurador reactivado exitosamente.');
    }
}
