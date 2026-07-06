<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * Layout principal de la aplicación DAE.
 *
 * Renderiza la plantilla base con navegación, sidebar y contenido
 * principal para usuarios autenticados.
 */
class AppLayout extends Component
{
    /**
     * Renderiza la vista del layout principal de la aplicación.
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
