<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * ═══════════════════════════════════════════════════════
 * TEST: Rate limiting en POST /login
 * ═══════════════════════════════════════════════════════
 * Tras 5 intentos fallidos consecutivos, el siguiente intento
 * es bloqueado por AuthService (RateLimitExceededException).
 *
 * NOTA: AuthController::login captura RateLimitExceededException
 * (que extiende AuthenticationException) y retorna back() con el
 * mensaje de límite de intentos, por lo que la respuesta HTTP es
 * un redirect (302) con error en sesión, NO un 429. El test
 * verifica el bloqueo funcional (mensaje "Demasiados intentos").
 */
class LoginRateLimitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['cache.default' => 'array']);
        Session::flush();
    }

    #[Test]
    public function tras_cinco_intentos_fallidos_el_siguiente_es_bloqueado(): void
    {
        $email = 'atacante@usap.edu';
        $payload = ['email' => $email, 'contrasena' => 'wrongpass1'];

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login.post'), $payload)->assertRedirect();
        }

        $response = $this->post(route('login.post'), $payload);

        $response->assertRedirect();
        $response->assertSessionHasErrors('email');

        $errores = Session::get('errors');
        $this->assertNotNull($errores);
        $this->assertStringContainsString(
            'Demasiados intentos',
            $errores->first('email')
        );
    }
}
