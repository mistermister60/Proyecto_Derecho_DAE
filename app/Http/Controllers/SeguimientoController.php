<?php

namespace App\Http\Controllers;

use App\Models\Caso;
use App\Models\Seguimiento;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

/**
 * Controlador para el seguimiento (bitácora) de casos.
 *
 * Permite registrar entradas de seguimiento en un caso. A diferencia de otros
 * controladores que reciben el número de expediente, este recibe el ID numérico
 * del caso directamente. Requiere autorización 'agregarSeguimiento'.
 */
class SeguimientoController extends Controller
{
    /**
     * Registra una nueva entrada de seguimiento en el caso.
     *
     * Valida tipo y descripción del seguimiento. Asigna automáticamente el
     * usuario autenticado como responsable y la fecha actual.
     *
     * @param  Request  $request  Datos del seguimiento
     * @param  int  $caso_id  ID numérico del caso (inconsistencia: otros usan $expediente)
     * @return RedirectResponse Redirección a la página anterior
     *
     * @throws AuthorizationException Si no tiene permiso 'agregarSeguimiento'
     * @throws ModelNotFoundException Si el caso no existe
     */
    public function store(Request $request, $caso_id)
    {
        $caso = Caso::findOrFail($caso_id);

        Gate::authorize('agregarSeguimiento', $caso);

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
