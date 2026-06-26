<?php

namespace Tests\Feature;

use App\Http\Controllers\ClienteController;
use App\Models\Cliente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class ClienteControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_splits_full_name_into_first_and_last_name(): void
    {
        $controller = new ClienteController;

        $request = Request::create('/clientes', 'POST', [
            'nombre_completo' => 'Juan Pérez',
            'cliente_dni' => '12345678',
            'cliente_estado_civil' => 'Soltero',
            'cliente_telefono' => '555-1234',
            'cliente_direccion' => 'Calle Falsa 123',
            'cliente_numero_hijos' => 0,
            'cliente_profesion' => 'Abogado',
            'cliente_lugar_trabajo' => 'Despacho',
            'cliente_direccion_trabajo' => 'Av. Principal 456',
            'cliente_telefono_trabajo' => '999-888-777',
            'cliente_salario_mensual' => 3500.50,
        ]);

        $response = $controller->store($request);

        $this->assertSame(route('clientes.index'), $response->getTargetUrl());

        $this->assertDatabaseHas('clientes', [
            'cliente_nombre' => 'Juan',
            'cliente_apellido' => 'Pérez',
            'cliente_dni' => '12345678',
            'cliente_direccion_trabajo' => 'Av. Principal 456',
            'cliente_telefono_trabajo' => '999-888-777',
            'cliente_estado' => 'activo',
        ]);
    }

    public function test_update_saves_work_fields(): void
    {
        $cliente = Cliente::create([
            'cliente_nombre' => 'María',
            'cliente_apellido' => 'López',
            'cliente_dni' => '87654321',
            'cliente_estado_civil' => 'Casado',
            'cliente_telefono' => '555-4321',
            'cliente_direccion' => 'Av. Siempre Viva 742',
            'cliente_numero_hijos' => 1,
            'cliente_profesion' => 'Ingeniera',
            'cliente_lugar_trabajo' => 'Fabrica',
            'cliente_direccion_trabajo' => 'Calle Secundaria 123',
            'cliente_telefono_trabajo' => '111-222-333',
            'cliente_salario_mensual' => 4500.00,
            'cliente_estado' => 'activo',
        ]);

        $controller = new ClienteController;

        $request = Request::create('/clientes/'.$cliente->cliente_dni, 'PUT', [
            'cliente_nombre' => 'María José',
            'cliente_apellido' => 'López',
            'cliente_dni' => '87654321',
            'cliente_estado_civil' => 'Casado',
            'cliente_telefono' => '555-4321',
            'cliente_direccion' => 'Av. Siempre Viva 742',
            'cliente_numero_hijos' => 2,
            'cliente_profesion' => 'Ingeniera Senior',
            'cliente_lugar_trabajo' => 'Tech Corp',
            'cliente_direccion_trabajo' => 'Nueva Dirección de Trabajo 999',
            'cliente_telefono_trabajo' => '444-555-666',
            'cliente_salario_mensual' => 6000.00,
        ]);

        $response = $controller->update($request, $cliente->cliente_dni);

        $this->assertSame(route('clientes.show', $cliente->cliente_dni), $response->getTargetUrl());

        $this->assertDatabaseHas('clientes', [
            'cliente_id' => $cliente->cliente_id,
            'cliente_nombre' => 'María José',
            'cliente_profesion' => 'Ingeniera Senior',
            'cliente_lugar_trabajo' => 'Tech Corp',
            'cliente_direccion_trabajo' => 'Nueva Dirección de Trabajo 999',
            'cliente_telefono_trabajo' => '444-555-666',
            'cliente_salario_mensual' => 6000.00,
            'cliente_estado' => 'activo',
        ]);
    }

    public function test_can_activate_inactive_client(): void
    {
        $cliente = Cliente::create([
            'cliente_nombre' => 'Carlos',
            'cliente_apellido' => 'Gómez',
            'cliente_dni' => '99999999',
            'cliente_estado_civil' => 'Soltero',
            'cliente_telefono' => '555-9999',
            'cliente_direccion' => 'Calle Secundaria 456',
            'cliente_numero_hijos' => 0,
            'cliente_profesion' => 'Mecánico',
            'cliente_lugar_trabajo' => 'Taller',
            'cliente_salario_mensual' => 2000.00,
            'cliente_estado' => 'inactivo',
        ]);

        $controller = new ClienteController;

        $response = $controller->activar($cliente->cliente_dni);

        $this->assertSame(route('clientes.show', $cliente->cliente_dni), $response->getTargetUrl());

        $this->assertDatabaseHas('clientes', [
            'cliente_id' => $cliente->cliente_id,
            'cliente_estado' => 'activo',
        ]);
    }
}
