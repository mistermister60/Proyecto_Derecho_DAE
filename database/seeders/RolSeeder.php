<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * ═══════════════════════════════════════════════════════
 * SEEDER: RolSeeder
 * ═══════════════════════════════════════════════════════
 * Siembra los roles base del sistema: Director y Procurador.
 *
 * - Ejecutar primero (no depende de otros seeders)
 * - Tanto para desarrollo como producción
 * - Define la estructura jerárquica del consultorio
 */
class RolSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Roles del sistema ──────────────────────────────
        // Director: administrador del consultorio, omite 2FA
        // Procurador: estudiante practicante, pasa por 2FA OTP
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
