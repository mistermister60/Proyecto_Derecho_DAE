<?php

namespace App\Http\Controllers;

use App\Models\Caso;
use App\Models\Entrevista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class EntrevistaController extends Controller
{
    public function store(Request $request, string $expediente)
    {
        $caso = Caso::where('caso_numero_expediente', $expediente)->firstOrFail();
        Gate::authorize('update', $caso);

        $validated = $request->validate([
            'entrevista_fecha'          => 'required|date',
            'entrevista_relacion_hechos' => 'required|string',
            'entrevista_observaciones'  => 'nullable|string',
        ]);

        $validated['caso_id']       = $caso->caso_id;
        $validated['procurador_id'] = $caso->procurador_id;
        $validated['entrevista_estado'] = 'activo';

        Entrevista::create($validated);

        return back()->with('success', 'Entrevista registrada.');
    }

    public function destroy(string $expediente, int $entrevista_id)
    {
        $caso = Caso::where('caso_numero_expediente', $expediente)->firstOrFail();
        Gate::authorize('update', $caso);

        $entrevista = Entrevista::where('entrevista_id', $entrevista_id)
            ->where('caso_id', $caso->caso_id)
            ->firstOrFail();

        $entrevista->delete();

        return back()->with('success', 'Entrevista eliminada.');
    }
}
