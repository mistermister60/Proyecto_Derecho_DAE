<?php

namespace Tests\Feature\Pwa;

use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests de suscripción a notificaciones push PWA.
 *
 * Verifica el funcionamiento del manifiesto PWA, el service worker,
 * la página offline, los endpoints de clave VAPID, el flujo completo
 * de suscripción push y los requisitos de autenticación para cada endpoint.
 */
class NotificationSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Usuario de prueba utilizado en los escenarios de suscripción.
     */
    private Usuario $user;

    /**
     * Configuración inicial de cada test.
     *
     * Crea los roles Director y Procurador usando factories,
     * y crea un usuario de prueba para las solicitudes autenticadas.
     */
    protected function setUp(): void
    {
        parent::setUp();

        Rol::factory()->create(['rol_id' => 1, 'rol_nombre' => 'Director']);
        Rol::factory()->create(['rol_id' => 2, 'rol_nombre' => 'Procurador']);

        $this->user = Usuario::factory()->create();
    }

    /**
     * Verifica que el manifiesto JSON de la PWA sea accesible y tenga la estructura esperada.
     *
     * Happy path: accede a la ruta del manifiesto y verifica que contenga
     * las claves principales como name, short_name, description, icons, shortcuts.
     */
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

    /**
     * Verifica que el manifiesto contenga contenido relacionado al ámbito legal
     * y los accesos directos a las rutas principales.
     *
     * Valida que la descripción mencione "Abogados" y "gestión",
     * y que los shortcuts incluyan /dashboard, /casos, /clientes y /agenda.
     */
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

    /**
     * Verifica que el manifiesto incluya los íconos necesarios (SVG y PNG 192x192 y 512x512).
     */
    public function test_manifest_has_icons(): void
    {
        $response = $this->get(route('pwa.manifest'));
        $icons = $response->json('icons');

        $this->assertNotEmpty($icons);

        $sources = array_column($icons, 'src');
        $this->assertContains('logo.svg', $sources);
        $this->assertContains('icons/icon-192x192.png', $sources);
        $this->assertContains('icons/icon-512x512.png', $sources);
    }

    /**
     * Verifica que el archivo del service worker (sw.js) exista en el directorio público
     * y contenga las cadenas esperadas para la funcionalidad offline.
     */
    public function test_service_worker_file_exists_in_public(): void
    {
        $path = public_path('sw.js');
        $this->assertFileExists($path);

        $content = file_get_contents($path);
        $this->assertStringContainsString('pwa-app-v1', $content);
        $this->assertStringContainsString('OFFLINE_URL', $content);
        $this->assertStringContainsString('networkFirstNavigate', $content);
    }

    /**
     * Verifica que la página offline de la PWA sea accesible y muestre contenido relevante.
     *
     * Happy path: accede a la ruta offline y verifica que el contenido
     * incluya "Sin conexión" y "Procurador Legal".
     */
    public function test_offline_page_is_accessible(): void
    {
        $response = $this->get(route('pwa.offline'));

        $response->assertStatus(200);
        $response->assertSee('Sin conexión');
        $response->assertSee('Procurador Legal', false);
    }

    /**
     * Verifica que el endpoint del logo SVG sea accesible y retorne el tipo MIME correcto.
     */
    public function test_logo_svg_is_accessible(): void
    {
        $response = $this->get(route('pwa.logo_svg'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'image/svg+xml');
    }

    /**
     * Verifica que el endpoint de clave VAPID requiera autenticación.
     *
     * Security test: solicitud sin autenticar debe recibir HTTP 401.
     */
    public function test_vapid_public_key_endpoint_requires_authentication(): void
    {
        $response = $this->getJson(route('pwa.vapid-key'));

        $response->assertStatus(401);
    }

    /**
     * Verifica que el endpoint VAPID retorne 404 cuando no hay clave configurada.
     *
     * Edge case: usuario autenticado pero sin clave VAPID configurada
     * debe recibir un error 404 con mensaje descriptivo.
     */
    public function test_vapid_public_key_returns_not_configured_when_empty(): void
    {
        $response = $this->actingAs($this->user)->getJson(route('pwa.vapid-key'));

        $response->assertStatus(404);
        $response->assertJson(['error' => 'Vapid public key not configured']);
    }

    /**
     * Verifica que el endpoint de suscripción rechace datos de suscripción inválidos.
     *
     * Failure path: envía una suscripción en formato JSON inválido
     * y espera un error HTTP 422 (Unprocessable Entity).
     */
    public function test_subscribe_endpoint_requires_valid_subscription(): void
    {
        $response = $this->actingAs($this->user)->postJson(route('pwa.subscribe'), [
            'subscription' => 'invalid-json',
            'user_id' => $this->user->usuario_id,
        ]);

        $response->assertStatus(422);
    }

    /**
     * Verifica que el endpoint de suscripción requiera autenticación.
     *
     * Security test: solicitud POST sin autenticar debe recibir HTTP 401.
     */
    public function test_subscribe_endpoint_requires_authentication(): void
    {
        $response = $this->postJson(route('pwa.subscribe'), [
            'subscription' => json_encode(['endpoint' => 'https://test.com']),
            'user_id' => $this->user->usuario_id,
        ]);

        $response->assertStatus(401);
    }

    /**
     * Verifica que el endpoint de salud de la API esté accesible sin autenticación.
     */
    public function test_health_endpoint_is_accessible(): void
    {
        $response = $this->get('/api/health');

        $response->assertStatus(204);
    }

    /**
     * Verifica el flujo completo de suscripción push exitosa.
     *
     * Happy path: usuario autenticado envía una suscripción válida,
     * recibe confirmación con mensaje de éxito y los datos se persisten.
     */
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
