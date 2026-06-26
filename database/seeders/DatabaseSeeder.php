<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Catálogos (ya existentes)
            RolSeeder::class,
            EstadoCasoSeeder::class,
            TipoTramiteSeeder::class,
            ProcuradorSeeder::class,

            // Datos de prueba
            ClienteSeeder::class,
            DemandadoSeeder::class,
            UsuarioSeeder::class,
            CasoSeeder::class,
            AudienciaSeeder::class,
        ]);
    }
}
