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
 * ═══════════════════════════════════════════════════════
 * TEST: Comando procuradores:enviar-bienvenida (filtro)
 * ═══════════════════════════════════════════════════════
 * El comando envía BienvenidaProcuradorMail a los procuradores
 * con debe_cambiar_contrasena = true.
 */
class EnviarBienvenidaFiltroTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolSeeder::class);
        $this->seed(ProcuradoresTemporalesSeeder::class);
    }

    #[Test]
    public function envia_a_procuradores_con_debe_cambiar_contrasena_true(): void
    {
        Mail::fake();

        $this->artisan('procuradores:enviar-bienvenida')->assertSuccessful();

        // Todos los procuradores temporales sembrados tienen
        // debe_cambiar_contrasena = true y deben recibir el correo.
        $destinatarios = Procurador::whereHas('usuario', fn ($q) => $q->where('debe_cambiar_contrasena', true))->get();

        $this->assertGreaterThan(0, $destinatarios->count());

        foreach ($destinatarios as $procurador) {
            Mail::assertSent(BienvenidaProcuradorMail::class, function (BienvenidaProcuradorMail $mail) use ($procurador) {
                return $mail->hasTo($procurador->procurador_email);
            });
        }
    }
}
