<?php

namespace App\Http\Controllers;

use App\Models\Audiencia;
use App\Models\Caso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AudienciaController extends Controller
{
    public function store(Request $request, string $expediente)
    {
        $caso = Caso::where('caso_numero_expediente', $expediente)->firstOrFail();
        Gate::authorize('update', $caso);

        $validated = $request->validate([
            'audiencia_fecha' => 'required|date',
            'audiencia_hora' => 'nullable|date_format:H:i',
            'audiencia_tipo' => 'required|string|max:100',
            'audiencia_juzgado' => 'nullable|string|max:50',
            'audiencia_observaciones' => 'nullable|string',
        ]);

        $validated['caso_id'] = $caso->caso_id;
        $validated['procurador_id'] = $caso->procurador_id;
        $validated['audiencia_estado'] = 'pendiente';

        Audiencia::create($validated);

        return back()->with('success', 'Audiencia agendada.');
    }

    public function destroy(string $expediente, int $audiencia_id)
    {
        $caso = Caso::where('caso_numero_expediente', $expediente)->firstOrFail();
        Gate::authorize('update', $caso);

        $audiencia = Audiencia::where('audiencia_id', $audiencia_id)
            ->where('caso_id', $caso->caso_id)
            ->firstOrFail();

        $audiencia->delete();

        return back()->with('success', 'Audiencia eliminada.');
    }
}
