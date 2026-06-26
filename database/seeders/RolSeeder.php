<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'rol_nombre' => 'Director',
                'rol_descripcion' => 'Director del Consultorio Jurídico',
                'rol_estado' => 'activo',
            ],
            [
                'rol_nombre' => 'Procurador',
                'rol_descripcion' => 'Estudiante practicante',
                'rol_estado' => 'activo',
            ],
        ]);
    }
}
