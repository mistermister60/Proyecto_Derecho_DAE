<?php

namespace App\Http\Controllers;

use App\Models\Audiencia;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
public function index()
{
    // 1. Iniciamos la consulta con tus relaciones y ordenamientos originales
    $query = Audiencia::with(['caso.cliente', 'caso.tipoTramite', 'procurador'])
        ->orderBy('audiencia_fecha')
        ->orderBy('audiencia_hora');

    // 2. Si es procurador, filtramos por sus casos. Si es Director, se salta este IF y ve todo.
    if (strtolower(auth()->user()->rol?->rol_nombre ?? '') === 'procurador') {
        $query->whereHas('caso', function($q) {
            $q->where('procurador_id', auth()->user()->procurador_id);
        });
    }

    $audiencias = $query->get();

    // --- Todo tu flujo de abajo se mantiene idéntico ---
    // Agrupar por mes para el calendario
    $audienciasPorMes = $audiencias->groupBy(fn($a) => \Carbon\Carbon::parse($a->audiencia_fecha)->format('Y-m'));

    $proximas = $audiencias->where('audiencia_fecha', '>=', now()->toDateString())
        ->take(10);

    return view('agenda.index', compact('audiencias', 'audienciasPorMes', 'proximas'));
}
}
