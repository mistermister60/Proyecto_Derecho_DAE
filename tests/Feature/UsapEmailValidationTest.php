<?php

namespace Tests\Feature;

use App\Http\Requests\StoreProcuradorRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Verifica la validación de correo institucional @usap.edu
 * en login y en el registro de procuradores.
 */
class UsapEmailValidationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function login_rechaza_correo_no_usap(): void
    {
        Mail::fake();

        $response = $this->post(route('login'), [
            'email' => 'externo@gmail.com',
            'contrasena' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email' => 'El correo debe terminar en @usap.edu.']);
    }

    #[Test]
    public function login_acepta_correo_usap(): void
    {
        Mail::fake();

        $response = $this->post(route('login'), [
            'email' => 'director@usap.edu',
            'contrasena' => 'password123',
        ]);

        $response->assertSessionDoesntHaveErrors(['email' => 'El correo debe terminar en @usap.edu.']);
    }

    #[Test]
    public function registro_procurador_rechaza_correo_no_usap(): void
    {
        $rules = (new StoreProcuradorRequest)->rules();

        $validator = Validator::make(
            ['procurador_email' => 'externo@gmail.com'],
            $rules
        );

        $this->assertTrue($validator->fails());
        $this->assertArrayHasKey('procurador_email', $validator->errors()->toArray());
    }
}
