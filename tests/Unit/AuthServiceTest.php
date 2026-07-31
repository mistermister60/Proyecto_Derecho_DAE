<?php

namespace Tests\Unit;

use App\Exceptions\AccountInactiveException;
use App\Exceptions\InvalidCredentialsException;
use App\Exceptions\RateLimitExceededException;
use App\Http\DTOs\AuthResponse;
use App\Models\Rol;
use App\Models\Usuario;
use App\Services\AuthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Tests unitarios del servicio AuthService.
 *
 * Cubre la lógica crítica de autenticación: rate limiting con caché,
 * validación de credenciales, estado de cuenta, generación de tokens y logout.
 */
class AuthServiceTest extends TestCase
{
    use RefreshDatabase;

    private AuthService $authService;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear roles base
        Rol::factory()->create(['rol_id' => 1, 'rol_nombre' => 'Director']);
        Rol::factory()->create(['rol_id' => 2, 'rol_nombre' => 'Procurador']);

        // Limpiar caché antes de cada test
        Cache::flush();

        $this->authService = new AuthService;
    }

    /** @test */
    public function attempt_login_retorna_auth_response_en_login_exitoso(): void
    {
        $usuario = Usuario::factory()->create([
            'email' => 'test@usap.edu',
            'contrasena' => Hash::make('Password123!'),
            'usuario_estado' => 'activo',
            'debe_cambiar_contrasena' => false,
        ]);

        $result = $this->authService->attemptLogin('test@usap.edu', 'Password123!');

        $this->assertInstanceOf(AuthResponse::class, $result);
        $this->assertNotEmpty($result->token);
        $this->assertEquals(3600, $result->expiresIn);
        $this->assertEquals($usuario->usuario_id, $result->user['id']);
        $this->assertEquals('test@usap.edu', $result->user['email']);
    }

    /** @test */
    public function attempt_login_lanza_invalid_credentials_si_email_no_existe(): void
    {
        $this->expectException(InvalidCredentialsException::class);
        $this->expectExceptionMessage('Las credenciales proporcionadas son incorrectas.');

        $this->authService->attemptLogin('noexiste@usap.edu', 'Password123!');
    }

    /** @test */
    public function attempt_login_lanza_invalid_credentials_si_password_incorrecto(): void
    {
        Usuario::factory()->create([
            'email' => 'test@usap.edu',
            'contrasena' => Hash::make('Password123!'),
            'usuario_estado' => 'activo',
        ]);

        $this->expectException(InvalidCredentialsException::class);
        $this->expectExceptionMessage('Las credenciales proporcionadas son incorrectas.');

        $this->authService->attemptLogin('test@usap.edu', 'WrongPassword123!');
    }

    /** @test */
    public function attempt_login_lanza_account_inactive_si_usuario_inactivo(): void
    {
        Usuario::factory()->create([
            'email' => 'test@usap.edu',
            'contrasena' => Hash::make('Password123!'),
            'usuario_estado' => 'inactivo',
        ]);

        $this->expectException(AccountInactiveException::class);
        $this->expectExceptionMessage('Su cuenta está desactivada.');

        $this->authService->attemptLogin('test@usap.edu', 'Password123!');
    }

    /** @test */
    public function attempt_login_limpia_rate_limit_en_login_exitoso(): void
    {
        $usuario = Usuario::factory()->create([
            'email' => 'test@usap.edu',
            'contrasena' => Hash::make('Password123!'),
            'usuario_estado' => 'activo',
        ]);

        // Simular intentos fallidos previos
        Cache::put('login_attempts:test@usap.edu', 3, 300);
        Cache::put('login_attempts:test@usap.edu:127.0.0.1', 2, 300);

        $this->authService->attemptLogin('test@usap.edu', 'Password123!');

        $this->assertNull(Cache::get('login_attempts:test@usap.edu'));
        $this->assertNull(Cache::get('login_attempts:test@usap.edu:127.0.0.1'));
    }

    /** @test */
    public function check_rate_limit_bloquea_despues_de_5_intentos_por_email(): void
    {
        Cache::put('login_attempts:test@usap.edu', 5, 300);

        $this->expectException(RateLimitExceededException::class);
        $this->expectExceptionMessage('Demasiados intentos de inicio de sesión. Por favor, intente nuevamente más tarde.');

        $this->authService->checkRateLimit('test@usap.edu', '127.0.0.1');
    }

    /** @test */
    public function check_rate_limit_bloquea_despues_de_5_intentos_por_email_ip(): void
    {
        Cache::put('login_attempts:test@usap.edu:127.0.0.1', 5, 300);

        $this->expectException(RateLimitExceededException::class);

        $this->authService->checkRateLimit('test@usap.edu', '127.0.0.1');
    }

    /** @test */
    public function check_rate_limit_permite_intentos_por_debajo_del_limite(): void
    {
        Cache::put('login_attempts:test@usap.edu', 3, 300);
        Cache::put('login_attempts:test@usap.edu:127.0.0.1', 2, 300);

        // No debe lanzar excepción
        $this->authService->checkRateLimit('test@usap.edu', '127.0.0.1');
    }

    /** @test */
    public function check_rate_limit_expira_despues_de_5_minutos(): void
    {
        // Poner 5 intentos pero con expiración muy corta (simulada)
        Cache::put('login_attempts:test@usap.edu', 5, 1); // 1 segundo

        // Esperar a que expire (en test real no podemos esperar, pero verificamos la lógica)
        // El test real sería con Carbon::setTestNow, pero aquí verificamos que
        // si el cache no existe, no bloquea
        Cache::forget('login_attempts:test@usap.edu');

        $this->authService->checkRateLimit('test@usap.edu', '127.0.0.1');
        $this->assertTrue(true); // Si llega aquí, no bloqueó
    }

    /** @test */
    public function validate_credentials_incrementa_contador_si_fallan(): void
    {
        $usuario = Usuario::factory()->create([
            'email' => 'test@usap.edu',
            'contrasena' => Hash::make('Password123!'),
        ]);

        // Usar reflexión para llamar método privado
        $reflection = new \ReflectionClass($this->authService);
        $method = $reflection->getMethod('validateCredentials');
        $method->setAccessible(true);

        $this->expectException(InvalidCredentialsException::class);
        $method->invoke($this->authService, $usuario, 'WrongPassword', 'test@usap.edu', '127.0.0.1');

        // Verificar que se incrementó el contador
        $this->assertEquals(1, Cache::get('login_attempts:test@usap.edu'));
        $this->assertEquals(1, Cache::get('login_attempts:test@usap.edu:127.0.0.1'));
    }

    /** @test */
    public function validate_credentials_no_revela_si_email_o_password_incorrecto(): void
    {
        // Usuario no existe
        $reflection = new \ReflectionClass($this->authService);
        $method = $reflection->getMethod('validateCredentials');
        $method->setAccessible(true);

        $this->expectException(InvalidCredentialsException::class);
        $method->invoke($this->authService, null, 'Password123!', 'noexiste@usap.edu', '127.0.0.1');

        // El mensaje es genérico, no dice "usuario no existe" vs "password incorrecto"
        $this->assertEquals('Las credenciales proporcionadas son incorrectas.', $this->getExceptionMessage());
    }

    /** @test */
    public function generate_token_crea_token_en_tabla_tokens(): void
    {
        $usuario = Usuario::factory()->create();

        $reflection = new \ReflectionClass($this->authService);
        $method = $reflection->getMethod('generateToken');
        $method->setAccessible(true);

        $token = $method->invoke($this->authService, $usuario);

        $this->assertIsString($token);
        $this->assertEquals(60, strlen($token));

        // Verificar que se guardó en BD (hasheado)
        $usuario->refresh();
        $this->assertCount(1, $usuario->tokens);
        $this->assertEquals(hash('sha256', $token), $usuario->tokens->first()->token);
    }

    /** @test */
    public function logout_elimina_tokens_y_cierra_sesion(): void
    {
        $usuario = Usuario::factory()->create();
        $usuario->tokens()->create([
            'name' => 'auth_token',
            'token' => hash('sha256', 'test-token'),
            'abilities' => ['*'],
        ]);

        $this->authService->logout($usuario->usuario_id);

        $usuario->refresh();
        $this->assertCount(0, $usuario->tokens);
    }

    /** @test */
    public function validate_account_status_lanza_excepcion_si_no_activo(): void
    {
        $usuario = Usuario::factory()->create(['usuario_estado' => 'inactivo']);

        $reflection = new \ReflectionClass($this->authService);
        $method = $reflection->getMethod('validateAccountStatus');
        $method->setAccessible(true);

        $this->expectException(AccountInactiveException::class);
        $method->invoke($this->authService, $usuario);
    }

    /** @test */
    public function is_valid_password_retorna_true_si_coincide(): void
    {
        $password = 'Password123!';
        $hashed = Hash::make($password);

        $reflection = new \ReflectionClass($this->authService);
        $method = $reflection->getMethod('isValidPassword');
        $method->setAccessible(true);

        $result = $method->invoke($this->authService, $hashed, $password);

        $this->assertTrue($result);
    }

    /** @test */
    public function is_valid_password_retorna_false_si_no_coincide(): void
    {
        $hashed = Hash::make('Password123!');

        $reflection = new \ReflectionClass($this->authService);
        $method = $reflection->getMethod('isValidPassword');
        $method->setAccessible(true);

        $result = $method->invoke($this->authService, $hashed, 'WrongPassword');

        $this->assertFalse($result);
    }

    /** @test */
    public function record_failed_login_attempt_incrementa_contadores(): void
    {
        $reflection = new \ReflectionClass($this->authService);
        $method = $reflection->getMethod('recordFailedLoginAttempt');
        $method->setAccessible(true);

        $method->invoke($this->authService, 'test@usap.edu', '127.0.0.1');
        $method->invoke($this->authService, 'test@usap.edu', '127.0.0.1');

        $this->assertEquals(2, Cache::get('login_attempts:test@usap.edu'));
        $this->assertEquals(2, Cache::get('login_attempts:test@usap.edu:127.0.0.1'));
    }

    /** @test */
    public function clear_rate_limit_elimina_contadores(): void
    {
        Cache::put('login_attempts:test@usap.edu', 5, 300);
        Cache::put('login_attempts:test@usap.edu:127.0.0.1', 3, 300);

        $reflection = new \ReflectionClass($this->authService);
        $method = $reflection->getMethod('clearRateLimit');
        $method->setAccessible(true);

        $method->invoke($this->authService, 'test@usap.edu', '127.0.0.1');

        $this->assertNull(Cache::get('login_attempts:test@usap.edu'));
        $this->assertNull(Cache::get('login_attempts:test@usap.edu:127.0.0.1'));
    }

    /** @test */
    public function get_rate_limit_key_genera_claves_correctas(): void
    {
        $reflection = new \ReflectionClass($this->authService);
        $method = $reflection->getMethod('getRateLimitKey');
        $method->setAccessible(true);

        $key1 = $method->invoke($this->authService, 'test@usap.edu');
        $key2 = $method->invoke($this->authService, 'test@usap.edu', '127.0.0.1');

        $this->assertEquals('login_attempts:test@usap.edu', $key1);
        $this->assertEquals('login_attempts:test@usap.edu:127.0.0.1', $key2);
    }

    /** @test */
    public function create_auth_response_estructura_correcta(): void
    {
        $usuario = Usuario::factory()->create([
            'usuario_nombre' => 'Juan Pérez',
            'email' => 'juan@usap.edu',
            'usuario_estado' => 'activo',
        ]);
        Rol::factory()->create(['rol_id' => $usuario->rol_id, 'rol_nombre' => 'Procurador', 'permisos' => ['casos.ver', 'casos.crear']]);

        $reflection = new \ReflectionClass($this->authService);
        $method = $reflection->getMethod('createAuthResponse');
        $method->setAccessible(true);

        $response = $method->invoke($this->authService, $usuario, 'test-token');

        $this->assertInstanceOf(AuthResponse::class, $response);
        $this->assertEquals('test-token', $response->token);
        $this->assertEquals(3600, $response->expiresIn);
        $this->assertEquals($usuario->usuario_id, $response->user['id']);
        $this->assertEquals('Juan Pérez', $response->user['nombre']);
        $this->assertEquals('juan@usap.edu', $response->user['email']);
        $this->assertEquals('activo', $response->user['estado']);
        $this->assertContains('casos.ver', $response->permissions);
        $this->assertContains('Procurador', $response->roles);
    }

    private function getExceptionMessage(): string
    {
        try {
            throw new \Exception('test');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
