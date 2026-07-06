<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests funcionales de ejemplo generados por Laravel Breeze.
 *
 * Verifica que las rutas principales de la aplicación respondan correctamente:
 * redirección de raíz, carga de página de inicio de sesión y endpoint de salud.
 */
class ExampleTest extends TestCase
{
    /**
     * Verifica que la ruta raíz redirige al login cuando no hay sesión.
     *
     * Un usuario no autenticado que accede a '/' debe recibir
     * una redirección HTTP 302 hacia la página de inicio de sesión.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        // Root redirects to login when not authenticated (302)
        $response->assertStatus(302);
    }

    /**
     * Verifica que la página de inicio de sesión se carga correctamente.
     *
     * La ruta GET /login debe retornar un código HTTP 200
     * y mostrar el formulario de autenticación.
     */
    public function test_login_page_loads_successfully(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    /**
     * Verifica que el endpoint de salud de la API responde correctamente.
     *
     * La ruta GET /api/health debe retornar un código HTTP 204
     * indicando que la aplicación está operativa.
     */
    public function test_health_endpoint_returns_ok(): void
    {
        $response = $this->get('/api/health');

        $response->assertStatus(204);
    }
}
