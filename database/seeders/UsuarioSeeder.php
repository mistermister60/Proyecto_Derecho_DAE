<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('usuarios')->insert([
            [
                'rol_id' => 1, // Director
                'procurador_id' => null,
                'usuario_nombre' => 'Director del Consultorio Jurídico',
                'email' => 'director@usap.edu',
                'contrasena' => Hash::make('director123'),
                'usuario_estado' => 'activo',
            ],
            [
                'rol_id' => 2, // Procurador
                'procurador_id' => 1,
                'usuario_nombre' => 'Iris Lizeth Rodríguez',
                'email' => 'iris.rodriguez@usap.edu',
                'contrasena' => Hash::make('procurador123'),
                'usuario_estado' => 'activo',
            ],
            [
                'rol_id' => 2,
                'procurador_id' => 2,
                'usuario_nombre' => 'Franklyn Geovanny Salgado',
                'email' => 'franklyn.salgado@usap.edu',
                'contrasena' => Hash::make('procurador123'),
                'usuario_estado' => 'activo',
            ],
            [
                'rol_id' => 2,
                'procurador_id' => 3,
                'usuario_nombre' => 'Indira Pauleth Galindo',
                'email' => 'indira.galindo@usap.edu',
                'contrasena' => Hash::make('procurador123'),
                'usuario_estado' => 'activo',
            ],
            [
                'rol_id' => 2,
                'procurador_id' => 4,
                'usuario_nombre' => 'Carlos Alberto Brizuela',
                'email' => 'carlos.brizuela@usap.edu',
                'contrasena' => Hash::make('procurador123'),
                'usuario_estado' => 'activo',
            ],
            [
                'rol_id' => 2,
                'procurador_id' => 5,
                'usuario_nombre' => 'Ena Elizabeth Flores',
                'email' => 'ena.flores@usap.edu',
                'contrasena' => Hash::make('procurador123'),
                'usuario_estado' => 'activo',
            ],
        ]);
    }
}
