<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemandadoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('demandados')->insert([
            [
                'demandado_nombre' => 'Carlos Fernando',
                'demandado_apellido' => 'Rivera Mendoza',
                'demandado_dni' => '0501-1990-05678',
                'demandado_estado_civil' => 'Casado',
                'demandado_telefono' => '9876-6001',
                'demandado_direccion' => 'Col. Trejo, Calle 5, Casa 12, San Pedro Sula',
                'demandado_profesion' => 'Ingeniero en Sistemas',
                'demandado_lugar_trabajo' => 'Tigo Honduras',
                'demandado_telefono_trabajo' => '2567-5000',
                'demandado_estado' => 'activo',
            ],
            [
                'demandado_nombre' => 'Rosa María',
                'demandado_apellido' => 'Pineda Torres',
                'demandado_dni' => '0501-1992-06789',
                'demandado_estado_civil' => 'Divorciada',
                'demandado_telefono' => '9876-6002',
                'demandado_direccion' => 'Residencial Los Ángeles, Calle 3, Casa 10, SPS',
                'demandado_profesion' => 'Secretaria',
                'demandado_lugar_trabajo' => 'Municipalidad de SPS',
                'demandado_telefono_trabajo' => '2568-6000',
                'demandado_estado' => 'activo',
            ],
            [
                'demandado_nombre' => 'Diego Alejandro',
                'demandado_apellido' => 'Mendoza García',
                'demandado_dni' => '0501-1991-07890',
                'demandado_estado_civil' => 'Soltero',
                'demandado_telefono' => '9876-6003',
                'demandado_direccion' => 'Col. Satélite, Bloque 3, Casa 8, SPS',
                'demandado_profesion' => 'Mecánico Automotriz',
                'demandado_lugar_trabajo' => 'Taller El Chele',
                'demandado_telefono_trabajo' => '2569-7000',
                'demandado_estado' => 'activo',
            ],
            [
                'demandado_nombre' => 'Manuel de Jesús',
                'demandado_apellido' => 'Paz Mejía',
                'demandado_dni' => '0501-1985-08901',
                'demandado_estado_civil' => 'Casado',
                'demandado_telefono' => '9876-6004',
                'demandado_direccion' => 'Col. Bella Vista, Calle 10, Casa 3, SPS',
                'demandado_profesion' => 'Transportista',
                'demandado_lugar_trabajo' => 'Transportes del Norte',
                'demandado_telefono_trabajo' => '2570-8000',
                'demandado_estado' => 'activo',
            ],
            [
                'demandado_nombre' => 'Laura Beatriz',
                'demandado_apellido' => 'Pérez Martínez',
                'demandado_dni' => '0501-1993-04567',
                'demandado_estado_civil' => 'Casada',
                'demandado_telefono' => '9876-6005',
                'demandado_direccion' => 'Col. Los Andes, Calle 2, Casa 15, SPS',
                'demandado_profesion' => 'Enfermera',
                'demandado_lugar_trabajo' => 'Clínica San Jorge',
                'demandado_telefono_trabajo' => '2571-9000',
                'demandado_estado' => 'activo',
            ],
            [
                'demandado_nombre' => 'José Armando',
                'demandado_apellido' => 'Rivas Castro',
                'demandado_dni' => '0501-1988-02345',
                'demandado_estado_civil' => 'Casado',
                'demandado_telefono' => '9876-6006',
                'demandado_direccion' => 'Col. Moderna, Calle 6, Casa 18, SPS',
                'demandado_profesion' => 'Contador',
                'demandado_lugar_trabajo' => 'Contraloría General',
                'demandado_telefono_trabajo' => '2572-0000',
                'demandado_estado' => 'activo',
            ],
            [
                'demandado_nombre' => 'Marta Teresa',
                'demandado_apellido' => 'López Villanueva',
                'demandado_dni' => '0501-1995-01238',
                'demandado_estado_civil' => 'Casada',
                'demandado_telefono' => '9876-6007',
                'demandado_direccion' => 'Residencial El Carmen, Casa 8, SPS',
                'demandado_profesion' => 'Psicóloga',
                'demandado_lugar_trabajo' => 'Centro de Salud Mental',
                'demandado_telefono_trabajo' => '2573-1000',
                'demandado_estado' => 'activo',
            ],
            [
                'demandado_nombre' => 'Ricardo Antonio',
                'demandado_apellido' => 'Toro Quintanilla',
                'demandado_dni' => '0501-1987-05679',
                'demandado_estado_civil' => 'Casado',
                'demandado_telefono' => '9876-6008',
                'demandado_direccion' => 'Col. Villa Olímpica, Casa 4, SPS',
                'demandado_profesion' => 'Médico Cirujano',
                'demandado_lugar_trabajo' => 'Hospital Militar',
                'demandado_telefono_trabajo' => '2574-2000',
                'demandado_estado' => 'activo',
            ],
        ]);
    }
}
