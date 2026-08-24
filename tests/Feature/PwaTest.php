<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * ═══════════════════════════════════════════════════════
 * TEST: Activos PWA y autenticación de rutas push
 * ═══════════════════════════════════════════════════════
 * - GET /manifest.json responde 200 con la estructura esperada.
 * - sw.js existe y es un service worker válido (archivo estático
 *   servido por el servidor web, no vía router de Laravel).
 * - Las rutas de suscripción push requieren autenticación (401).
 */
class PwaTest extends TestCase
{
    #[Test]
    public function manifest_json_responde_200(): void
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
    }

    #[Test]
    public function sw_js_existe_y_es_valido(): void
    {
        $path = public_path('sw.js');

        $this->assertFileExists($path);

        $content = file_get_contents($path);
        $this->assertStringContainsString('pwa-app-v1', $content);
        $this->assertStringContainsString('OFFLINE_URL', $content);
    }

    #[Test]
    public function rutas_push_requieren_autenticacion(): void
    {
        $this->getJson(route('pwa.vapid-key'))->assertStatus(401);
        $this->postJson(route('pwa.subscribe'), ['subscription' => 'x'])->assertStatus(401);
        $this->postJson(route('pwa.unsubscribe'), ['subscription' => 'x'])->assertStatus(401);
    }
}
