<?php

namespace Tests\Unit;

use App\Models\Rol;
use App\Models\Usuario;
use App\Services\PwaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests unitarios del servicio PwaService.
 *
 * Verifica el funcionamiento del servicio de notificaciones push progresivas (PWA),
 * incluyendo la suscripción, cancelación de suscripción, consulta de suscripciones
 * y validación de datos de suscripción.
 */
class PwaServiceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Instancia del servicio PWA bajo prueba.
     */
    private PwaService $pwaService;

    /**
     * Usuario de prueba utilizado en los escenarios.
     */
    private Usuario $user;

    /**
     * Configuración inicial de cada test.
     *
     * Crea los roles necesarios (Director, Procurador) utilizando factories,
     * instancia el PwaService y crea un usuario de prueba.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Crear roles necesarios
        Rol::factory()->create(['rol_id' => 1, 'rol_nombre' => 'Director']);
        Rol::factory()->create(['rol_id' => 2, 'rol_nombre' => 'Procurador']);

        $this->pwaService = new PwaService;
        $this->user = Usuario::factory()->create();
    }

    /**
     * Verifica que un usuario válido pueda suscribirse a notificaciones push.
     *
     * Happy path: proporciona un endpoint y claves de suscripción válidas,
     * espera que el servicio retorne true y que los datos se persistan
     * correctamente en el usuario.
     */
    public function test_subscribe_to_push_returns_true_for_valid_user(): void
    {
        $subscription = [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/test-endpoint',
            'keys' => [
                'p256dh' => 'test-p256dh-key',
                'auth' => 'test-auth-key',
            ],
        ];

        $result = $this->pwaService->subscribeToPush($this->user->usuario_id, $subscription);

        $this->assertTrue($result);

        $this->user->refresh();
        $this->assertNotNull($this->user->push_subscription);
        $this->assertEquals($subscription['endpoint'], $this->user->push_notification_token);
    }

    /**
     * Verifica que la suscripción falle para un usuario inexistente.
     *
     * Edge case: utiliza un ID de usuario que no existe en la base de datos
     * y espera que el servicio retorne false.
     */
    public function test_subscribe_to_push_returns_false_for_non_existent_user(): void
    {
        $result = $this->pwaService->subscribeToPush(99999, []);

        $this->assertFalse($result);
    }

    /**
     * Verifica que cancelar la suscripción push limpie los datos almacenados.
     *
     * Happy path: suscribe al usuario primero, luego cancela la suscripción
     * y verifica que tanto push_subscription como push_notification_token
     * queden en null.
     */
    public function test_unsubscribe_from_push_clears_subscription(): void
    {
        $subscription = [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/test-endpoint',
            'keys' => [
                'p256dh' => 'test-p256dh-key',
                'auth' => 'test-auth-key',
            ],
        ];

        // Subscribe first
        $this->pwaService->subscribeToPush($this->user->usuario_id, $subscription);

        // Then unsubscribe
        $result = $this->pwaService->unsubscribeFromPush($this->user->usuario_id, $subscription);

        $this->assertTrue($result);

        $this->user->refresh();
        $this->assertNull($this->user->push_subscription);
        $this->assertNull($this->user->push_notification_token);
    }

    /**
     * Verifica que se pueda recuperar la suscripción push de un usuario suscrito.
     *
     * Happy path: suscribe al usuario, luego consulta la suscripción
     * y verifica que los datos retornados coincidan con los registrados.
     */
    public function test_get_user_push_subscription_returns_subscription(): void
    {
        $subscription = [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/test-endpoint',
            'keys' => [
                'p256dh' => 'test-p256dh-key',
                'auth' => 'test-auth-key',
            ],
        ];

        $this->pwaService->subscribeToPush($this->user->usuario_id, $subscription);

        $retrieved = $this->pwaService->getUserPushSubscription($this->user->usuario_id);

        $this->assertNotNull($retrieved);
        $this->assertEquals($subscription['endpoint'], $retrieved['endpoint']);
    }

    /**
     * Verifica que se retorne null al consultar la suscripción de un usuario no suscrito.
     *
     * Edge case: usuario sin suscripción activa debe retornar null.
     */
    public function test_get_user_push_subscription_returns_null_for_unsubscribed_user(): void
    {
        $result = $this->pwaService->getUserPushSubscription($this->user->usuario_id);

        $this->assertNull($result);
    }

    /**
     * Verifica que una suscripción con estructura completa sea válida.
     *
     * Happy path: proporciona un endpoint y ambas claves (p256dh, auth),
     * espera que validateSubscription retorne true.
     */
    public function test_validate_subscription_returns_true_for_valid_subscription(): void
    {
        $subscription = [
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/test',
            'keys' => [
                'p256dh' => 'valid-p256dh-key',
                'auth' => 'valid-auth-key',
            ],
        ];

        $result = $this->pwaService->validateSubscription($subscription);

        $this->assertTrue($result);
    }

    /**
     * Verifica que una suscripción incompleta sea rechazada.
     *
     * Failure path: prueba múltiples variantes de suscripciones inválidas
     * (vacía, sin keys, keys incompletas) y espera que todas retornen false.
     */
    public function test_validate_subscription_returns_false_when_missing_keys(): void
    {
        $invalidSubscriptions = [
            [],
            ['endpoint' => 'https://test.com'],
            ['endpoint' => 'https://test.com', 'keys' => []],
            ['endpoint' => 'https://test.com', 'keys' => ['p256dh' => 'test']],
        ];

        foreach ($invalidSubscriptions as $subscription) {
            $this->assertFalse(
                $this->pwaService->validateSubscription($subscription),
                'Expected validation to fail for: '.json_encode($subscription)
            );
        }
    }
}
