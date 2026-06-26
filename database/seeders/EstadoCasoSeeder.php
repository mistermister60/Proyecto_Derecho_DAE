<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoCasoSeeder extends Seeder
{
    public function run(): void
    {
        $estados = [
            ['estado_nombre' => 'Entrevista',              'estado_orden' => 1,  'estado_color' => '#9CA3AF', 'estado_tipo' => 'pipeline'],
            ['estado_nombre' => 'Admitido',                 'estado_orden' => 2,  'estado_color' => '#60A5FA', 'estado_tipo' => 'pipeline'],
            ['estado_nombre' => 'Poder conferido',          'estado_orden' => 3,  'estado_color' => '#3B82F6', 'estado_tipo' => 'pipeline'],
            ['estado_nombre' => 'Presentado al juzgado',    'estado_orden' => 4,  'estado_color' => '#2563EB', 'estado_tipo' => 'pipeline'],
            ['estado_nombre' => 'Admitido por el juzgado',  'estado_orden' => 5,  'estado_color' => '#1D4ED8', 'estado_tipo' => 'pipeline'],
            ['estado_nombre' => 'Audiencia señalada',       'estado_orden' => 6,  'estado_color' => '#F59E0B', 'estado_tipo' => 'pipeline'],
            ['estado_nombre' => 'En sentencia',             'estado_orden' => 7,  'estado_color' => '#D97706', 'estado_tipo' => 'pipeline'],
            ['estado_nombre' => 'Cerrado',                  'estado_orden' => 8,  'estado_color' => '#16A34A', 'estado_tipo' => 'pipeline'],
            ['estado_nombre' => 'Inadmisible',              'estado_orden' => 9,  'estado_color' => '#EF4444', 'estado_tipo' => 'especial'],
            ['estado_nombre' => 'Reasignado',               'estado_orden' => 10, 'estado_color' => '#A855F7', 'estado_tipo' => 'especial'],
            ['estado_nombre' => 'Atrasado',                 'estado_orden' => 11, 'estado_color' => '#DC2626', 'estado_tipo' => 'especial'],
        ];

        DB::table('estados_caso')->insert($estados);
    }
}
