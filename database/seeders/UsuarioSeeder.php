<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * ═══════════════════════════════════════════════════════
 * SEEDER: UsuarioSeeder
 * ═══════════════════════════════════════════════════════
 * Crea las cuentas de acceso al sistema para el Director
 * y los 5 Procuradores.
 *
 * - Depende de: RolSeeder, ProcuradorSeeder
 * - Cada usuario tiene contraseña 'password' (hash Bcrypt)
 * - El Director no tiene procurador asociado (procurador_id = null)
 * - Los procuradores se vinculan por procurador_id
 */
class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Obtener IDs de roles ───────────────────────────
        // Se consultan dinámicamente para no depender de IDs
        // fijos, permitiendo ejecución en cualquier orden.
        $directorId = DB::table('roles')->where('rol_nombre', 'Director')->value('rol_id');
        $procuradorId = DB::table('roles')->where('rol_nombre', 'Procurador')->value('rol_id');

        // ─── Usuarios del sistema ───────────────────────────
        // Se crea un usuario por rol. El Director (rol_id=1)
        // omite 2FA; los procuradores requieren OTP por email.
        DB::table('usuarios')->insert([
            [
                'rol_id' => $directorId,
                'procurador_id' => null,
                'usuario_nombre' => 'Director del Consultorio Jurídico',
                'email' => 'director@usap.edu',
                'contrasena' => Hash::make('password'),
                'usuario_estado' => 'activo',
            ],
            [
                'rol_id' => $procuradorId,
                'procurador_id' => 1,
                'usuario_nombre' => 'Iris Lizeth Rodríguez',
                'email' => 'iris.rodriguez@usap.edu',
                'contrasena' => Hash::make('password'),
                'usuario_estado' => 'activo',
            ],
            [
                'rol_id' => $procuradorId,
                'procurador_id' => 2,
                'usuario_nombre' => 'Franklyn Geovanny Salgado',
                'email' => 'franklyn.salgado@usap.edu',
                'contrasena' => Hash::make('password'),
                'usuario_estado' => 'activo',
            ],
            [
                'rol_id' => $procuradorId,
                'procurador_id' => 3,
                'usuario_nombre' => 'Indira Pauleth Galindo',
                'email' => 'indira.galindo@usap.edu',
                'contrasena' => Hash::make('password'),
                'usuario_estado' => 'activo',
            ],
            [
                'rol_id' => $procuradorId,
                'procurador_id' => 4,
                'usuario_nombre' => 'Carlos Alberto Brizuela',
                'email' => 'carlos.brizuela@usap.edu',
                'contrasena' => Hash::make('password'),
                'usuario_estado' => 'activo',
            ],
            [
                'rol_id' => $procuradorId,
                'procurador_id' => 5,
                'usuario_nombre' => 'Ena Elizabeth Flores',
                'email' => 'ena.flores@usap.edu',
                'contrasena' => Hash::make('password'),
                'usuario_estado' => 'activo',
            ],
        ]);
    }
}
