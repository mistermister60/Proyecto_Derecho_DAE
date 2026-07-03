<?php

namespace App\Observers;

use App\Models\Caso;
use Illuminate\Support\Facades\Log;

class CasoObserver
{
    public function created(Caso $caso): void
    {
        Log::channel('audit')->info('Expediente Creado', [
            'expediente' => $caso->caso_numero_expediente,
            'usuario_id' => auth()->id() ?? 'Sistema/Consola',
        ]);
    }

    /**
     * Dispara un log inmutable de auditoria cada vez que un expediente es modificado.
     */
    public function updated(Caso $caso): void
    {
        $usuario = auth()->user();
        $cambios = [];

        foreach ($caso->getChanges() as $campo => $nuevoValor) {
            // Omitir campo de actualizacion automatica de tiempo
            if ($campo === 'updated_at') continue;

            $cambios[$campo] = [
                'antes' => $caso->getOriginal($campo),
                'ahora' => $nuevoValor
            ];
        }

        if (!empty($cambios)) {
            Log::channel('audit')->info('Expediente Modificado', [
                'expediente' => $caso->caso_numero_expediente,
                'usuario_id' => $usuario?->usuario_id ?? 'Sistema/Consola',
                'cambios' => $cambios
            ]);
        }
    }
}