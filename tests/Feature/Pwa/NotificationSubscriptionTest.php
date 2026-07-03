<?php

namespace Tests\Feature\Pwa;

use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    private Usuario $user;

    protected function setUp(): void
    {
        parent::setUp();

        Rol::factory()->create(['rol_id' => 1, 'rol_nombre' => 'Director']);
        Rol::factory()->create(['rol_id' => 2, 'rol_nombre' => 'Procurador']);

        $this->user = Usuario::factory()->create();
    }

    public function test_manifest_json_is_accessible(): void
    {
        $response = $this->get(route('pwa.manifest'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'name',
            'short_name',
            'description',
            'start_url',
            'display',
            'background_color',
            'theme_color',
            'icons',
            'shortcuts',
        ]);

        $content = $response->json();
        $this->assertStringContainsString('Procurador Legal', $content['name']);
        $this->assertEquals('#6777ef', $content['theme_color']);
    }

    public function test_manifest_json_has_law_related_content(): void
    {
        $response = $this->get(route('pwa.manifest'));
        $content = $response->json();

        $this->assertStringContainsString('Abogados', $content['description']);
        $this->assertStringContainsString('gestión', $content['description']);

        $shortcutUrls = array_column($content['shortcuts'], 'url');
        $this->assertContains('/dashboard', $shortcutUrls);
        $this->assertContains('/casos', $shortcutUrls);
        $this->assertContains('/clientes', $shortcutUrls);
        $this->assertContains('/agenda', $shortcutUrls);
    }

    public function test_manifest_has_icons(): void
    {
        $response = $this->get(route('pwa.manifest'));
        $icons = $response->json('icons');

        $this->assertNotEmpty($icons);

        $sources = array_column($icons, 'src');
        $this->assertContains('logo.svg', $sources);
        $this->assertContains('logo.png', $sources);
    }

    public function test_service_worker_is_accessible(): void
    {
        $response = $this->get(route('pwa.sw'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/javascript');
        $response->assertSee('pwa-app-v1', false);
        $response->assertSee('OFFLINE_URL', false);
        $response->assertSee('networkFirstNavigate', false);
    }

    public function test_offline_page_is_accessible(): void
    {
        $response = $this->get(route('pwa.offline'));

        $response->assertStatus(200);
        $response->assertSee('Sin conexión');
        $response->assertSee('Procurador Legal', false);
    }

    public function test_logo_svg_is_accessible(): void
    {
        $response = $this->get(route('pwa.logo_svg'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/svg+xml');
    }

    public function test_vapid_public_key_endpoint_requires_authentication(): void
    {
        $response = $this->getJson(route('pwa.vapid-key'));

        $response->assertStatus(401);
    }

    public function test_vapid_public_key_returns_not_configured_when_empty(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('pwa.vapid-key'));

        $response->assertStatus(404);
        $response->assertJson(['error' => 'Vapid public key not configured']);
    }

    public function test_subscribe_endpoint_requires_valid_subscription(): void
    {
        $response = $this->actingAs($this->user)->postJson(route('pwa.subscribe'), [
            'subscription' => 'invalid-json',
            'user_id' => $this->user->usuario_id,
        ]);

        $response->assertStatus(422);
    }

    public function test_subscribe_endpoint_requires_authentication(): void
    {
        $response = $this->postJson(route('pwa.subscribe'), [
            'subscription' => json_encode(['endpoint' => 'https://test.com']),
            'user_id' => $this->user->usuario_id,
        ]);

        $response->assertStatus(401);
    }

    public function test_health_endpoint_is_accessible(): void
    {
        $response = $this->get('/api/health');

        $response->assertStatus(204);
    }

    public function test_successful_subscription_flow(): void
    {
        $subscription = json_encode([
            'endpoint' => 'https://fcm.googleapis.com/fcm/send/test-endpoint-123',
            'keys' => [
                'p256dh' => 'test-p256dh-key-for-testing',
                'auth' => 'test-auth-key-for-testing',
            ],
        ]);

        $response = $this->actingAs($this->user)->postJson(route('pwa.subscribe'), [
            'subscription' => $subscription,
            'user_id' => $this->user->usuario_id,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Suscripción push registrada exitosamente']);

        $this->user->refresh();
        $this->assertNotNull($this->user->push_subscription);
    }
}
