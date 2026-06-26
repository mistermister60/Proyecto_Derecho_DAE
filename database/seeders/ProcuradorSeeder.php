<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProcuradorSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('procuradores')->insert([
            [
                'procurador_nombre' => 'Iris Lizeth',
                'procurador_apellido' => 'Rodríguez',
                'procurador_dni' => '0501-1998-01234',
                'procurador_carnet' => '0257-26',
                'procurador_fecha_nacimiento' => '1998-03-15',
                'procurador_genero' => 'Femenino',
                'procurador_email' => 'iris.rodriguez@usap.edu',
                'procurador_telefono' => '9876-5432',
                'procurador_estado' => 'activo',
            ],
            [
                'procurador_nombre' => 'Franklyn Geovanny',
                'procurador_apellido' => 'Salgado Pineda',
                'procurador_dni' => '0501-1997-05678',
                'procurador_carnet' => '0312-26',
                'procurador_fecha_nacimiento' => '1997-07-22',
                'procurador_genero' => 'Masculino',
                'procurador_email' => 'franklyn.salgado@usap.edu',
                'procurador_telefono' => '9654-3210',
                'procurador_estado' => 'activo',
            ],
            [
                'procurador_nombre' => 'Indira Pauleth',
                'procurador_apellido' => 'Galindo Vásquez',
                'procurador_dni' => '0501-1999-03456',
                'procurador_carnet' => '0189-26',
                'procurador_fecha_nacimiento' => '1999-11-08',
                'procurador_genero' => 'Femenino',
                'procurador_email' => 'indira.galindo@usap.edu',
                'procurador_telefono' => '9234-5678',
                'procurador_estado' => 'activo',
            ],
            [
                'procurador_nombre' => 'Carlos Alberto',
                'procurador_apellido' => 'Brizuela Zamora',
                'procurador_dni' => '0501-1996-07890',
                'procurador_carnet' => '0421-26',
                'procurador_fecha_nacimiento' => '1996-05-30',
                'procurador_genero' => 'Masculino',
                'procurador_email' => 'carlos.brizuela@usap.edu',
                'procurador_telefono' => '9345-6789',
                'procurador_estado' => 'activo',
            ],
            [
                'procurador_nombre' => 'Ena Elizabeth',
                'procurador_apellido' => 'Flores Álvarez',
                'procurador_dni' => '0501-2000-04567',
                'procurador_carnet' => '0098-26',
                'procurador_fecha_nacimiento' => '2000-01-20',
                'procurador_genero' => 'Femenino',
                'procurador_email' => 'ena.flores@usap.edu',
                'procurador_telefono' => '9567-8901',
                'procurador_estado' => 'activo',
            ],
        ]);
    }
}
