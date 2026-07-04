<?php

namespace App\Http\Controllers;

use App\Models\Procurador;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ProcuradorController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));

        $procuradores = Procurador::withCount('casos')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('procurador_dni', 'like', "%{$search}%")
                        ->orWhere('procurador_telefono', 'like', "%{$search}%")
                        ->orWhere('procurador_nombre', 'like', "%{$search}%")
                        ->orWhere('procurador_apellido', 'like', "%{$search}%")
                        ->orWhere('procurador_email', 'like', "%{$search}%");
                });
            })
            ->orderBy('procurador_apellido')
            ->orderBy('procurador_nombre')
            ->paginate(20);

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
            'procurador_carnet' => 'required|string|max:20',
            'procurador_fecha_nacimiento' => 'required|date',
            'procurador_genero' => 'required|string|max:25',
            'procurador_email' => 'required|email|max:150',
            'procurador_telefono' => 'required|string|max:29',
            'procurador_direccion' => 'required|string|max:200',
        ]);

        $validated['procurador_estado'] = 'activo';

        DB::transaction(function () use ($validated) {
            $procurador = Procurador::create($validated);
            Usuario::create([
                'rol_id' => Rol::where('rol_nombre', 'Procurador')->value('rol_id'),
                'procurador_id' => $procurador->procurador_id,
                'usuario_nombre' => $validated['procurador_nombre'].' '.$validated['procurador_apellido'],
                'email' => $validated['procurador_email'],
                'contrasena' => Hash::make('Procurador'.$validated['procurador_dni'].'!'),
                'usuario_estado' => 'activo',
            ]);
        });

        return redirect()->route('procuradores.index')
            ->with('success', 'Procurador y usuario registrados exitosamente.');
    }

    public function show(string $identidad)
    {
        $procurador = Procurador::with(['casos.estado', 'casos.tipoTramite', 'casos.procurador', 'usuario'])
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
            'procurador_carnet' => 'required|string|max:20|unique:procuradores,procurador_carnet,'.$procurador->procurador_id.',procurador_id',
            'procurador_fecha_nacimiento' => 'required|date',
            'procurador_genero' => 'required|string|max:25',
            'procurador_email' => 'required|email|max:150|unique:procuradores,procurador_email,'.$procurador->procurador_id.',procurador_id',
            'procurador_telefono' => 'required|string|max:29',
            'procurador_direccion' => 'required|string|max:200',
        ]);

        $procurador->update($validated);

        return redirect()->route('procuradores.show', $identidad)
            ->with('success', 'Procurador actualizado exitosamente.');
    }

    public function destroy(string $identidad)
    {
        $procurador = Procurador::where('procurador_dni', $identidad)->firstOrFail();

        DB::transaction(function () use ($procurador) {
            $procurador->update(['procurador_estado' => 'inactivo']);
            if ($procurador->usuario) {
                $procurador->usuario->update(['usuario_estado' => 'inactivo']);
            }
        });

        return redirect()->route('procuradores.index')
            ->with('success', 'Procurador desactivado exitosamente. El registro se conserva en el sistema.');
    }

    public function activar(string $identidad)
    {
        $procurador = Procurador::where('procurador_dni', $identidad)->firstOrFail();

        DB::transaction(function () use ($procurador) {
            $procurador->update(['procurador_estado' => 'activo']);

            if ($procurador->usuario) {
                $procurador->usuario->update(['usuario_estado' => 'activo']);
            }
        });

        return redirect()->route('procuradores.show', $identidad)
            ->with('success', 'Procurador reactivado exitosamente.');
    }
}
