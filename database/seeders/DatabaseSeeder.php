<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolSeeder::class,
            EstadoCasoSeeder::class,
            TipoTramiteSeeder::class,
            ProcuradorSeeder::class,
        ]);
    }
}
