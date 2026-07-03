<?php

namespace App\Http\Controllers;

use App\Models\Caso;
use App\Models\Seguimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SeguimientoController extends Controller
{
    public function store(Request $request, $caso_id)
    {
        $caso = Caso::findOrFail($caso_id);

        if (strtolower(auth()->user()->rol?->rol_nombre ?? '') === 'procurador' && $caso->procurador_id !== auth()->user()->procurador_id) {
            abort(403, 'No tienes permiso para agregar seguimientos a este caso.');
        }

        $request->validate([
            'seguimiento_tipo' => 'required|string|max:50',
            'seguimiento_descripcion' => 'required|string',
        ]);

        Seguimiento::create([
            'caso_id' => $caso_id,
            'usuario_id' => Auth::user()->usuario_id ?? Auth::id(),
            'seguimiento_fecha' => now()->toDateString(),
            'seguimiento_tipo' => $request->input('seguimiento_tipo'),
            'seguimiento_descripcion' => $request->input('seguimiento_descripcion'),
            'seguimiento_estado' => 'activo',
        ]);

        return redirect()->back()->with('success', 'Bitácora actualizada correctamente.');
    }
}
