<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AudienciaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('audiencias')->insert([
            [
                'caso_id' => 1, // 0501-2026-00431
                'procurador_id' => 1,
                'audiencia_fecha' => '2026-06-25',
                'audiencia_hora' => '08:30:00',
                'audiencia_juzgado' => 'J-7',
                'audiencia_tipo' => 'Audiencia preliminar',
                'audiencia_estado' => 'pendiente',
                'audiencia_observaciones' => 'Presentar pruebas documentales.',
                'created_at' => '2026-06-15 14:00:00',
                'updated_at' => '2026-06-15 14:00:00',
            ],
            [
                'caso_id' => 4, // 0501-2026-00428
                'procurador_id' => 1,
                'audiencia_fecha' => '2026-06-25',
                'audiencia_hora' => '10:00:00',
                'audiencia_juzgado' => 'J-3',
                'audiencia_tipo' => 'Conciliación',
                'audiencia_estado' => 'pendiente',
                'audiencia_observaciones' => 'Citar a ambas partes para acuerdo.',
                'created_at' => '2026-06-10 15:00:00',
                'updated_at' => '2026-06-10 15:00:00',
            ],
            [
                'caso_id' => 1,
                'procurador_id' => 1,
                'audiencia_fecha' => '2026-06-26',
                'audiencia_hora' => '11:30:00',
                'audiencia_juzgado' => 'J-7',
                'audiencia_tipo' => 'Audiencia de pruebas',
                'audiencia_estado' => 'pendiente',
                'audiencia_observaciones' => 'Desahogo de pruebas testimoniales.',
                'created_at' => '2026-06-15 14:30:00',
                'updated_at' => '2026-06-15 14:30:00',
            ],
            [
                'caso_id' => 7, // 0501-2026-00430
                'procurador_id' => 4,
                'audiencia_fecha' => '2026-06-26',
                'audiencia_hora' => '14:00:00',
                'audiencia_juzgado' => 'J-3',
                'audiencia_tipo' => 'Sentencia',
                'audiencia_estado' => 'pendiente',
                'audiencia_observaciones' => 'Lectura de sentencia de disolución.',
                'created_at' => '2026-05-02 09:00:00',
                'updated_at' => '2026-05-02 09:00:00',
            ],
            [
                'caso_id' => 3, // 0501-2026-00429
                'procurador_id' => 2,
                'audiencia_fecha' => '2026-06-27',
                'audiencia_hora' => '08:30:00',
                'audiencia_juzgado' => 'J-8',
                'audiencia_tipo' => 'Audiencia inicial',
                'audiencia_estado' => 'pendiente',
                'audiencia_observaciones' => 'Primera comparecencia.',
                'created_at' => '2026-05-21 08:00:00',
                'updated_at' => '2026-05-21 08:00:00',
            ],
            [
                'caso_id' => 5, // 0501-2026-00427
                'procurador_id' => 3,
                'audiencia_fecha' => '2026-06-30',
                'audiencia_hora' => '10:00:00',
                'audiencia_juzgado' => 'J-7',
                'audiencia_tipo' => 'Toma de muestras ADN',
                'audiencia_estado' => 'pendiente',
                'audiencia_observaciones' => 'Coordinación con Medicina Forense.',
                'created_at' => '2026-04-06 14:00:00',
                'updated_at' => '2026-04-06 14:00:00',
            ],
            [
                'caso_id' => 6, // 0501-2026-00433
                'procurador_id' => 4,
                'audiencia_fecha' => '2026-07-01',
                'audiencia_hora' => '14:30:00',
                'audiencia_juzgado' => 'J-5',
                'audiencia_tipo' => 'Audiencia preliminar',
                'audiencia_estado' => 'pendiente',
                'audiencia_observaciones' => 'Presentar propuesta de convenio.',
                'created_at' => '2026-04-21 16:00:00',
                'updated_at' => '2026-04-21 16:00:00',
            ],
            [
                'caso_id' => 12, // 0501-2026-00437
                'procurador_id' => 1,
                'audiencia_fecha' => '2026-07-03',
                'audiencia_hora' => '09:00:00',
                'audiencia_juzgado' => 'J-3',
                'audiencia_tipo' => 'Lectura de sentencia',
                'audiencia_estado' => 'pendiente',
                'audiencia_observaciones' => 'Disolución por mutuo acuerdo.',
                'created_at' => '2026-04-01 13:00:00',
                'updated_at' => '2026-04-01 13:00:00',
            ],
        ]);
    }
}
