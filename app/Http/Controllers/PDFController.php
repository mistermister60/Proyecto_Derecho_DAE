<?php

namespace App\Http\Controllers;

use App\Models\Caso;
use App\Models\Procurador;
use Barryvdh\DomPDF\Facade\Pdf;

class PDFController extends Controller
{
    public function seguimiento(string $expediente)
    {
        $caso = Caso::where('caso_numero_expediente', $expediente)
            ->with([
                'cliente',
                'estado',
                'procurador',
                'seguimientos' => function ($q) {
                    $q->with('usuario')
                        ->orderBy('created_at', 'desc');
                },
            ])
            ->firstOrFail();

        $pdf = Pdf::loadView('pdf.seguimiento', compact('caso'));

        return $pdf->download("Seguimiento_{$expediente}.pdf");
    }

    public function constanciaPracticante(string $identidad)
    {
        $procurador = Procurador::where('procurador_dni', $identidad)
            ->withCount(['casos as casos_activos' => fn ($q) => $q->where('caso_estado', 'activo')])
            ->firstOrFail();

        $pdf = Pdf::loadView('pdf.constancia', compact('procurador'));

        return $pdf->download("Constancia_{$procurador->nombre_completo}.pdf");
    }
}
