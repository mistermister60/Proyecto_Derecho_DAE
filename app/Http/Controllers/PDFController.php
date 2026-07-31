<?php

namespace App\Http\Controllers;

use App\Models\Caso;
use App\Models\Procurador;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * ═══════════════════════════════════════════════════════
 * CONTROLADOR: PDFController
 * ═══════════════════════════════════════════════════════
 * Generación de documentos PDF para el sistema.
 * Dos tipos de documentos:
 * 1. Seguimiento de caso: historial completo del expediente
 * 2. Constancia de practicante: certificación de procurador
 * ───────────────────────────────────────────────────────
 * Usa DomPDF (barryvdh/laravel-dompdf) para renderizado.
 * Rutas protegidas: middleware ['auth', 'otp', 'password.changed']
 * Roles: Director (ambos), Procurador (solo constancia propia)
 */
class PDFController extends Controller
{
    /**
     * ═══════════════════════════════════════════════════════
     * seguimiento
     * ───────────────────────────────────────────────────────
     * Genera y descarga el PDF de seguimiento de un caso.
     * Carga el caso con todas sus relaciones: cliente, estado,
     * procurador, y seguimientos (con usuario, ordenados por
     * fecha descendente). Renderiza la vista pdf.seguimiento
     * y fuerza la descarga con nombre basado en el expediente.
     * ═══════════════════════════════════════════════════════
     *
     * @param  string  $expediente  Número de expediente del caso
     * @return BinaryFileResponse Descarga del PDF
     */
    public function seguimiento(string $expediente)
    {
        // ─── [Búsqueda del caso con relaciones completas] ───────
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

        // ─── [Autorización: permiso 'view'] ─────────────────
        // Solo el Director o el procurador asignado al caso
        // pueden descargar el PDF de seguimiento (mismo criterio
        // que CasoController::show).
        Gate::authorize('view', $caso);

        // ─── [Generación del PDF] ───────────────────────────────
        $pdf = Pdf::loadView('pdf.seguimiento', compact('caso'));

        // ─── [Descarga forzada con nombre descriptivo] ──────────
        return $pdf->download("Seguimiento_{$expediente}.pdf");
    }

    /**
     * ═══════════════════════════════════════════════════════
     * constanciaPracticante
     * ───────────────────────────────────────────────────────
     * Genera y descarga la constancia de practicante (procurador).
     * Busca el procurador por DNI, incluye contador de casos
     * activos. Renderiza la vista pdf.constancia y fuerza la
     * descarga con nombre basado en el nombre completo.
     * ═══════════════════════════════════════════════════════
     *
     * @param  string  $identidad  Número de DNI del procurador
     * @return BinaryFileResponse Descarga del PDF
     */
    public function constanciaPracticante(string $identidad)
    {
        // ─── [Búsqueda del procurador con contador de casos activos] ─
        $procurador = Procurador::where('procurador_dni', $identidad)
            ->withCount(['casos as casos_activos' => fn ($q) => $q->where('caso_estado', 'activo')])
            ->firstOrFail();

        // ─── [Generación del PDF] ──────────────────────────────────
        $pdf = Pdf::loadView('pdf.constancia', compact('procurador'));

        // ─── [Descarga forzada con nombre descriptivo] ─────────────
        return $pdf->download("Constancia_{$procurador->nombre_completo}.pdf");
    }
}
