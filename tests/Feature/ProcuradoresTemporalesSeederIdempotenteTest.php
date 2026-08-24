<?php

namespace Tests\Feature;

use App\Models\Procurador;
use Database\Seeders\ProcuradoresTemporalesSeeder;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * ═══════════════════════════════════════════════════════
 * TEST: Idempotencia de ProcuradoresTemporalesSeeder
 * ═══════════════════════════════════════════════════════
 * Ejecutar el seeder dos veces no debe duplicar los
 * procuradores (usa updateOrInsert por procurador_email).
 */
class ProcuradoresTemporalesSeederIdempotenteTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function seeder_no_duplica_procuradores_al_ejecutarse_dos_veces(): void
    {
        $this->seed(RolSeeder::class);

        $this->seed(ProcuradoresTemporalesSeeder::class);
        $primeraVez = Procurador::count();

        $this->seed(ProcuradoresTemporalesSeeder::class);
        $segundaVez = Procurador::count();

        $this->assertEquals($primeraVez, $segundaVez);
        $this->assertEquals(26, $segundaVez);
    }
}
