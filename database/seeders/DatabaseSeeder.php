<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * ═══════════════════════════════════════════════════════
 * SEEDER: DatabaseSeeder
 * ═══════════════════════════════════════════════════════
 * Orquestador principal de seeders. Su único propósito es
 * invocar cada seeder en el orden correcto.
 *
 * - Ordena la ejecución respetando dependencias por foreign keys
 * - Catálogos base se ejecutan primero (independientes)
 * - Datos de prueba dependen de los catálogos
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─── ORDEN DE EJECUCIÓN ─────────────────────────────
        // El orden importa por las foreign keys:
        // 1. Roles        (independiente)
        // 2. EstadoCaso   (independiente)
        // 3. TipoTramite  (independiente)
        // 4. Procuradores (independiente)
        // 5. Clientes     (independiente)
        // 6. Demandados   (independiente)
        // 7. Usuarios     (depende de roles y procuradores)
        // 8. Casos        (depende de clientes, procuradores, estados, tipos)
        // 9. Audiencias   (depende de casos)
        $this->call([
            // Catálogos base (sin dependencias)
            RolSeeder::class,
            EstadoCasoSeeder::class,
            TipoTramiteSeeder::class,
            ProcuradorSeeder::class,

            // Datos de prueba (dependen de catálogos)
            UsuarioSeeder::class,
        ]);
    }
}
