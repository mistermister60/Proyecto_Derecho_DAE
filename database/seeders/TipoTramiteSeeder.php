<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * ═══════════════════════════════════════════════════════
 * SEEDER: TipoTramiteSeeder
 * ═══════════════════════════════════════════════════════
 * Siembra los tipos de trámite legal disponibles en el
 * consultorio jurídico.
 *
 * - 6 tipos de trámite comunes en derecho de familia y civil
 * - Todos se crean en estado 'activo'
 * - Independiente, puede ejecutarse en cualquier orden
 */
class TipoTramiteSeeder extends Seeder
{
    public function run(): void
    {
        // ─── Trámites legales ───────────────────────────────
        // Cubren las áreas más comunes: divorcios, alimentos,
        // paternidad y ejecuciones forzosas.
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
