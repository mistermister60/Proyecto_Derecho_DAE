<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoTramiteSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('tipos_tramite')->insert([
            ['tramite_nombre' => 'Disolución por mutuo acuerdo', 'tramite_estado' => 'activo'],
            ['tramite_nombre' => 'Divorcio contencioso', 'tramite_estado' => 'activo'],
            ['tramite_nombre' => 'Demanda de alimentos', 'tramite_estado' => 'activo'],
            ['tramite_nombre' => 'Revisión de demanda de alimentos', 'tramite_estado' => 'activo'],
            ['tramite_nombre' => 'Reconocimiento forzoso de paternidad', 'tramite_estado' => 'activo'],
            ['tramite_nombre' => 'Solicitud de ejecución forzosa', 'tramite_estado' => 'activo'],
        ]);
    }
}
