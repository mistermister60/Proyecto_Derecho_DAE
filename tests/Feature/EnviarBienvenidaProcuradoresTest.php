<?php

namespace Tests\Feature;

use App\Mail\BienvenidaProcuradorMail;
use App\Models\Procurador;
use Database\Seeders\ProcuradoresTemporalesSeeder;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Verifica el comando procuradores:enviar-bienvenida.
 */
class EnviarBienvenidaProcuradoresTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolSeeder::class);
        $this->seed(ProcuradoresTemporalesSeeder::class);
    }

    #[Test]
    public function dry_run_no_envia_y_lista_procuradores(): void
    {
        Mail::fake();

        $this->artisan('procuradores:enviar-bienvenida', ['--dry-run' => true])
            ->assertSuccessful();

        Mail::assertNothingSent();
    }

    #[Test]
    public function envia_correos_a_los_procuradores_temporales(): void
    {
        Mail::fake();

        $this->artisan('procuradores:enviar-bienvenida')
            ->assertSuccessful();

        // Conteo dinámico: todos los procuradores con DNI temporal (TEMP-)
        // deben recibir el correo de bienvenida (hoy son 26 tras agregar
        // las cuentas 3240520 y 1230634).
        $esperados = Procurador::where('procurador_dni', 'like', 'TEMP-%')->count();

        Mail::assertSent(BienvenidaProcuradorMail::class, $esperados);
    }

    #[Test]
    public function opcion_dni_filtra_un_solo_procurador(): void
    {
        Mail::fake();

        $procurador = Procurador::first();

        $this->artisan('procuradores:enviar-bienvenida', ['--dni' => $procurador->procurador_dni])
            ->assertSuccessful();

        Mail::assertSent(BienvenidaProcuradorMail::class, 1);
    }
}
