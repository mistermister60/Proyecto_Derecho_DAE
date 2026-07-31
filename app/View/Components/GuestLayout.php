<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

/**
 * ═══════════════════════════════════════════════════════
 * COMPONENT: GuestLayout
 * ═══════════════════════════════════════════════════════
 * Layout para invitados (no autenticados).
 *
 * Renderiza la plantilla base para páginas de login y acceso público.
 */
class GuestLayout extends Component
{
    /**
     * Renderiza la vista del layout para invitados.
     *
     * @return View Vista 'layouts.guest' con estructura simplificada
     *              para páginas públicas (login, registro, etc.).
     */
    public function render(): View
    {
        return view('layouts.guest');
    }
}
