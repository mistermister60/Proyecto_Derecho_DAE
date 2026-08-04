<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * ═══════════════════════════════════════════════════════
 * SEEDER: ProcuradorSeeder
 * ═══════════════════════════════════════════════════════
 * Siembra 5 procuradores (estudiantes practicantes) con
 * datos realistas para el consultorio jurídico.
 *
 * - Cada procurador tiene datos personales completos
 * - Induce registros en la tabla 'procuradores'
 * - Independiente, no depende de otros seeders
 * - Los usuarios se crean aparte en UsuarioSeeder
 */
class ProcuradorSeeder extends Seeder
{
    public function run(): void
    {

        DB::table('procuradores')->insert([
            [
                'procurador_nombre' => 'Karen',
                'procurador_apellido' => 'Fernandez',
                'procurador_dni' => '0501-1998-01234',
                'procurador_carnet' => '0257-26',
                'procurador_fecha_nacimiento' => '1998-03-15',
                'procurador_genero' => 'Femenino',
                'procurador_email' => 'karen.fernandez@usap.edu',
                'procurador_telefono' => '9876-5432',
                'procurador_estado' => 'activo',
            ],
        ]);
    }
}
