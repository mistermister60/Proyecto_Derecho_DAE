<?php

namespace App\Http\Controllers;

use App\Models\Audiencia;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function index()
    {
        $audiencias = Audiencia::with(['caso.cliente', 'caso.tipoTramite', 'procurador'])
            ->orderBy('audiencia_fecha')
            ->orderBy('audiencia_hora')
            ->get();

        // Agrupar por mes para el calendario
        $audienciasPorMes = $audiencias->groupBy(fn($a) => \Carbon\Carbon::parse($a->audiencia_fecha)->format('Y-m'));

        $proximas = $audiencias->where('audiencia_fecha', '>=', now()->toDateString())
            ->take(10);

        return view('agenda.index', compact('audiencias', 'audienciasPorMes', 'proximas'));
    }
}
