<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class OTPTestSeeder extends Seeder
{
    /**
     * Inserta procuradores y sus usuarios de forma segura sin afectar registros existentes.
     */
    public function run(): void
    {
        $directorRoleId = DB::table('roles')
            ->where('rol_nombre', 'Director')
            ->value('rol_id');

        if (! $directorRoleId) {
            $directorRoleId = DB::table('roles')->insertGetId([
                'rol_nombre' => 'Director',
                'rol_descripcion' => 'Rol creado por OTPTestSeeder',
                'rol_estado' => 'activo',
            ]);
        }

        $procuradorRoleId = DB::table('roles')
            ->where('rol_nombre', 'Procurador')
            ->value('rol_id');

        if (! $procuradorRoleId) {
            $procuradorRoleId = DB::table('roles')->insertGetId([
                'rol_nombre' => 'Procurador',
                'rol_descripcion' => 'Rol creado por OTPTestSeeder',
                'rol_estado' => 'activo',
            ]);
        }

        $procuradores = [
            [
                'procurador_nombre' => 'Carlos',
                'procurador_apellido' => 'Fuentes',
                'procurador_dni' => '0501-1991-11111',
                'procurador_carnet' => '1001-26',
                'procurador_fecha_nacimiento' => '2026-07-17',
                'procurador_genero' => 'Masculino',
                'procurador_email' => '1240245@usap.edu',
                'procurador_telefono' => '9999-1111',
                'procurador_direccion' => 'Col. San Miguel',
                'procurador_estado' => 'activo',
            ],
            [
                'procurador_nombre' => 'Jose Elias',
                'procurador_apellido' => 'Ramos',
                'procurador_dni' => '0501-1992-22222',
                'procurador_carnet' => '1002-26',
                'procurador_fecha_nacimiento' => '1992-05-14',
                'procurador_genero' => 'Masculino',
                'procurador_email' => '3180215@usap.edu',
                'procurador_telefono' => '9888-2222',
                'procurador_direccion' => 'Col. Miraflores',
                'procurador_estado' => 'activo',
            ],
        ];

        // Se cambiaron los correos para que no colisionen con los de procuradores
        // e incluyan los correos requeridos en el test.
        $directores = [
            [
                'usuario_nombre' => 'Director Test',
                'email' => 'test@usap.edu',
            ],
            [
                'usuario_nombre' => 'Director General',
                'email' => 'test2@usap.edu',
            ],
        ];

        foreach ($procuradores as $procuradorData) {
            $existingProcurador = DB::table('procuradores')
                ->where('procurador_dni', $procuradorData['procurador_dni'])
                ->orWhere('procurador_email', $procuradorData['procurador_email'])
                ->first();

            $procuradorId = $existingProcurador?->procurador_id;

            if (! $existingProcurador) {
                $procuradorId = DB::table('procuradores')->insertGetId($procuradorData);
            }

            $existingUsuario = DB::table('usuarios')
                ->where('email', $procuradorData['procurador_email'])
                ->first();

            if (! $existingUsuario) {
                DB::table('usuarios')->insert([
                    'rol_id' => $procuradorRoleId,
                    'procurador_id' => $procuradorId,
                    'usuario_nombre' => $procuradorData['procurador_nombre'].' '.$procuradorData['procurador_apellido'],
                    'email' => $procuradorData['procurador_email'],
                    'contrasena' => Hash::make('password'),
                    'usuario_estado' => 'activo',
                ]);
            }
        }

        foreach ($directores as $directorData) {
            $existingDirector = DB::table('usuarios')
                ->where('email', $directorData['email'])
                ->first();

            if (! $existingDirector) {
                DB::table('usuarios')->insert([
                    'rol_id' => $directorRoleId,
                    'procurador_id' => null,
                    'usuario_nombre' => $directorData['usuario_nombre'],
                    'email' => $directorData['email'],
                    'contrasena' => Hash::make('password'),
                    'usuario_estado' => 'activo',
                ]);
            }
        }
    }
}