<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * ═══════════════════════════════════════════════════════
 * COMPONENT: AppLayout
 * ═══════════════════════════════════════════════════════
 * Layout principal de la aplicación DAE.
 *
 * Renderiza la plantilla base con navegación, sidebar y contenido
 * principal para usuarios autenticados.
 */
class AppLayout extends Component
{
    /**
     * Renderiza la vista del layout principal de la aplicación.
     *
     * @return View Vista 'layouts.app' con la estructura completa
     *              (header, sidebar, contenido principal, footer).
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
