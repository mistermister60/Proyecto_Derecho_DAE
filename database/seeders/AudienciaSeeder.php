<?php

namespace Database\Seeders;

use App\Models\Audiencia;
use App\Models\Caso;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * ═══════════════════════════════════════════════════════
 * SEEDER: AudienciaSeeder
 * ═══════════════════════════════════════════════════════
 * Crea 8 audiencias de prueba distribuidas en el tiempo:
 * algunas pasadas (completadas), algunas presentes y
 * otras futuras (pendientes).
 *
 * - Depende de: CasoSeeder (solo casos activos)
 * - Asigna procuradores al azar (IDs 1-5)
 * - Las audiencias pasadas se crean con -5 y -3 días
 * - La audiencia de hoy (0 días) se marca pendiente
 */
class AudienciaSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Obtener casos activos ──────────────────────────
        // Solo los casos en estado 'activo' pueden tener
        // audiencias. Si no hay, se omite el seeder.
        $casos = Caso::where('caso_estado', 'activo')->pluck('caso_id', 'caso_numero_expediente')->toArray();

        if (empty($casos)) {
            $this->command->warn(' No hay casos activos para crear audiencias.');

            return;
        }

        $expedientes = array_keys($casos);

        // ─── Audiencias demo ─────────────────────────────────
        // Definición de audiencias con días relativos a hoy,
        // hora del día y estado (completada / pendiente).
        $audiencias = [
            ['dias' => -5, 'hora' => '09:00', 'estado' => 'completada'],
            ['dias' => -3, 'hora' => '10:30', 'estado' => 'completada'],
            ['dias' => 0, 'hora' => '08:00', 'estado' => 'pendiente'],
            ['dias' => 2, 'hora' => '11:00', 'estado' => 'pendiente'],
            ['dias' => 5, 'hora' => '14:00', 'estado' => 'pendiente'],
            ['dias' => 7, 'hora' => '09:30', 'estado' => 'pendiente'],
            ['dias' => 10, 'hora' => '15:00', 'estado' => 'pendiente'],
            ['dias' => 14, 'hora' => '08:30', 'estado' => 'pendiente'],
        ];

        foreach ($audiencias as $i => $aud) {
            $expediente = $expedientes[$i % count($expedientes)];

            Audiencia::create([
                'caso_id' => $casos[$expediente],
                'procurador_id' => rand(1, 5),
                'audiencia_fecha' => Carbon::now()->addDays($aud['dias'])->toDateString(),
                'audiencia_hora' => $aud['hora'],
                'audiencia_tipo' => 'Vista oral',
                'audiencia_estado' => $aud['estado'],
                'created_at' => Carbon::now()->subDays(15),
            ]);
        }

        $this->command->info(' 8 audiencias demo creadas correctamente.');
    }
}
