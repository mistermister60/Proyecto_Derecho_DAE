<?php

namespace Tests\Unit;

use App\Models\Rol;
use App\Models\Usuario;
use App\Services\PwaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PwaServiceTest extends TestCase
{
    use RefreshDatabase;

    private PwaService $pwaService;

    private Usuario $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear roles necesarios
        Rol::factory()->create(['rol_id' => 1, 'rol_nombre' => 'Director']);
        Rol::factory()->create(['rol_id' => 2, 'rol_nombre' => 'Procurador']);

        $this->pwaService = new PwaService;
        $this->user = Usuario::factory()->create();
    }

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

    public function test_subscribe_to_push_returns_false_for_non_existent_user(): void
    {
        $result = $this->pwaService->subscribeToPush(99999, []);

        $this->assertFalse($result);
    }

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

    public function test_get_user_push_subscription_returns_null_for_unsubscribed_user(): void
    {
        $result = $this->pwaService->getUserPushSubscription($this->user->usuario_id);

        $this->assertNull($result);
    }

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
