<?php

namespace App\Http\Controllers;

use App\Http\Requests\PerfilProcuradorRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * ═══════════════════════════════════════════════════════
 * CONTROLADOR: PerfilProcuradorController
 * ═══════════════════════════════════════════════════════
 * Permite al procurador completar su perfil demográfico
 * (DNI, fecha de nacimiento, celular y contacto de
 * emergencia) en su primer inicio de sesión. El acceso lo
 * tiene el propio procurador autenticado.
 */
class PerfilProcuradorController extends Controller
{
    /**
     * Muestra el formulario de completado de perfil.
     */
    public function show(): View
    {
        $procurador = auth()->user()->procurador;

        return view('procuradores.completar-perfil', compact('procurador'));
    }

    /**
     * Guarda los datos demográficos del procurador.
     */
    public function store(PerfilProcuradorRequest $request): RedirectResponse
    {
        $procurador = auth()->user()->procurador;

        $procurador->update($request->validated());

        return redirect()->route('dashboard')
            ->with('success', 'Perfil completado. ¡Bienvenido al Consultorio Jurídico DAE!');
    }
}
