<?php

namespace App\Observers;

use App\Models\Caso;
use Illuminate\Support\Facades\Log;

/**
 * Observer para el modelo Caso.
 *
 * Responde a eventos del ciclo de vida del modelo (created, updated, etc.)
 * para ejecutar acciones secundarias como logging, notificaciones
 * o actualizaciones relacionadas.
 */
class CasoObserver
{
    /**
     * Maneja el evento 'created' del modelo Caso.
     *
     * Registra en el log de auditoría la creación de un nuevo expediente,
     * incluyendo el número de expediente y el usuario que lo creó.
     *
     * @param  Caso  $caso  La instancia del caso recién creado.
     */
    public function created(Caso $caso): void
    {
        Log::channel('audit')->info('Expediente Creado', [
            'expediente' => $caso->caso_numero_expediente,
            'usuario_id' => auth()->id() ?? 'Sistema/Consola',
        ]);
    }

    /**
     * Maneja el evento 'updated' del modelo Caso.
     *
     * Dispara un log inmutable de auditoría cada vez que un expediente es modificado.
     * Compara los valores anteriores y nuevos de cada campo (excepto updated_at)
     * y los registra en el canal de auditoría.
     *
     * @param  Caso  $caso  La instancia del caso actualizado.
     */
    public function updated(Caso $caso): void
    {
        $usuario = auth()->user();
        $cambios = [];

        foreach ($caso->getChanges() as $campo => $nuevoValor) {
            // Omitir campo de actualizacion automatica de tiempo
            if ($campo === 'updated_at') {
                continue;
            }

            $cambios[$campo] = [
                'antes' => $caso->getOriginal($campo),
                'ahora' => $nuevoValor,
            ];
        }

        if (! empty($cambios)) {
            Log::channel('audit')->info('Expediente Modificado', [
                'expediente' => $caso->caso_numero_expediente,
                'usuario_id' => $usuario?->usuario_id ?? 'Sistema/Consola',
                'cambios' => $cambios,
            ]);
        }
    }
}
