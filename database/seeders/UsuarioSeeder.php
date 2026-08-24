<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * ═══════════════════════════════════════════════════════
 * SEEDER: UsuarioSeeder
 * ═══════════════════════════════════════════════════════
 * Crea las cuentas de acceso al sistema para el Director,
 * la abogada (Karen Fernández) y los 5 Procuradores.
 *
 * - Depende de: RolSeeder, ProcuradorSeeder
 * - El Director no tiene procurador asociado (procurador_id = null)
 * - Usa updateOrInsert para ser idempotente (re-seed sin duplicados)
 */
class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        $directorId = DB::table('roles')->where('rol_nombre', 'Director')->value('rol_id');
        $procuradorId = DB::table('roles')->where('rol_nombre', 'Procurador')->value('rol_id');

        $usuarios = [
            [
                'rol_id' => $directorId,
                'procurador_id' => null,
                'usuario_nombre' => 'Director del Consultorio Jurídico',
                'email' => 'director@usap.edu',
                'contrasena' => Hash::make('password'),
                'usuario_estado' => 'activo',
            ],
            [
                'rol_id' => $directorId,
                'procurador_id' => null,
                'usuario_nombre' => 'Karen Fernández',
                'email' => 'karen.fernandez@usap.edu',
                'contrasena' => Hash::make('password'),
                'usuario_estado' => 'activo',
                'debe_cambiar_contrasena' => true,
            ],
            [
                'rol_id' => $procuradorId,
                'procurador_id' => 1,
                'usuario_nombre' => 'Iris Lizeth Rodríguez',
                'email' => 'iris.rodriguez@usap.edu',
                'contrasena' => Hash::make('Password123'),
                'usuario_estado' => 'activo',
            ],
            [
                'rol_id' => $procuradorId,
                'procurador_id' => 2,
                'usuario_nombre' => 'Franklyn Geovanny Salgado',
                'email' => 'franklyn.salgado@usap.edu',
                'contrasena' => Hash::make('Password123'),
                'usuario_estado' => 'activo',
            ],
            [
                'rol_id' => $procuradorId,
                'procurador_id' => 3,
                'usuario_nombre' => 'Indira Pauleth Galindo',
                'email' => 'indira.galindo@usap.edu',
                'contrasena' => Hash::make('Password123'),
                'usuario_estado' => 'activo',
            ],
            [
                'rol_id' => $procuradorId,
                'procurador_id' => 4,
                'usuario_nombre' => 'Carlos Alberto Brizuela',
                'email' => 'carlos.brizuela@usap.edu',
                'contrasena' => Hash::make('Password123'),
                'usuario_estado' => 'activo',
            ],
            [
                'rol_id' => $procuradorId,
                'procurador_id' => 5,
                'usuario_nombre' => 'Ena Elizabeth Flores',
                'email' => 'ena.flores@usap.edu',
                'contrasena' => Hash::make('Password123'),
                'usuario_estado' => 'activo',
            ],
        ];

        foreach ($usuarios as $u) {
            DB::table('usuarios')->updateOrInsert(['email' => $u['email']], $u);
        }
    }
}
