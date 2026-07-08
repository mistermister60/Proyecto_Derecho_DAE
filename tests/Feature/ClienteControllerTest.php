<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Tests del controlador de Clientes (ClienteController).
 *
 * Verifica las operaciones CRUD del módulo de clientes: creación con
 * división de nombre completo, actualización de campos, desactivación
 * (soft delete lógico) y reactivación de clientes inactivos.
 */
class ClienteControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Crea y retorna un usuario con rol Director para usar en los tests.
     *
     * Utiliza firstOrCreate para evitar duplicados del rol Director.
     *
     * @return Usuario Usuario director creado para la sesión de prueba.
     */
    private function director(): Usuario
    {
        $rol = Rol::firstOrCreate(
            ['rol_nombre' => 'Director'],
            ['rol_descripcion' => 'Director', 'rol_estado' => 'activo']
        );

        return Usuario::create([
            'rol_id' => $rol->rol_id,
            'procurador_id' => null,
            'usuario_nombre' => 'Director Test',
            'email' => 'director@test.hn',
            'contrasena' => Hash::make('Segura8!'),
            'usuario_estado' => 'activo',
        ]);
    }

    /**
     * Verifica que la creación de un cliente divida el nombre completo
     * y persista correctamente los campos separados.
     *
     * Happy path: envía 'nombre_completo' y verifica que se almacene
     * como 'cliente_nombre' y 'cliente_apellido' por separado.
     */
    public function test_store_splits_full_name_and_persists_cliente(): void
    {
        $this->actingAs($this->director())
            ->post(route('clientes.store'), [
                'nombre_completo' => 'Juan Pérez',
                'cliente_dni' => '0501199900011',
                'cliente_estado_civil' => 'Soltero',
                'cliente_telefono' => '9000-0001',
                'cliente_direccion' => 'Calle Falsa 123',
                'cliente_numero_hijos' => 0,
            ])
            ->assertRedirect(route('clientes.index'));

        $this->assertDatabaseHas('clientes', [
            'cliente_nombre' => 'Juan',
            'cliente_apellido' => 'Pérez',
            'cliente_dni' => '0501199900011',
            'cliente_estado' => 'activo',
        ]);
    }

    /**
     * Verifica que la actualización de un cliente persista los campos modificados.
     *
     * Happy path: actualiza el nombre, número de hijos y profesión,
     * y verifica que los cambios se reflejen en la base de datos.
     */
    public function test_update_saves_fields(): void
    {
        $cliente = Cliente::create([
            'cliente_nombre' => 'María',
            'cliente_apellido' => 'López',
            'cliente_dni' => '0501199900022',
            'cliente_estado_civil' => 'Casado',
            'cliente_telefono' => '9000-0002',
            'cliente_direccion' => 'Av. Principal',
            'cliente_numero_hijos' => 1,
            'cliente_estado' => 'activo',
        ]);

        $this->actingAs($this->director())
            ->put(route('clientes.update', $cliente->cliente_dni), [
                'cliente_nombre' => 'María José',
                'cliente_apellido' => 'López',
                'cliente_dni' => $cliente->cliente_dni,
                'cliente_estado_civil' => 'Casado',
                'cliente_telefono' => '9000-0002',
                'cliente_direccion' => 'Av. Principal',
                'cliente_numero_hijos' => 2,
                'cliente_profesion' => 'Ingeniera Senior',
            ])
            ->assertRedirect(route('clientes.show', $cliente->cliente_dni));

        $this->assertDatabaseHas('clientes', [
            'cliente_id' => $cliente->cliente_id,
            'cliente_nombre' => 'María José',
            'cliente_profesion' => 'Ingeniera Senior',
        ]);
    }

    /**
     * Verifica que la eliminación de un cliente lo desactive (soft delete lógico).
     *
     * Happy path: elimina un cliente activo y verifica que su estado
     * cambie a 'inactivo' en lugar de borrar el registro.
     */
    public function test_destroy_desactiva_cliente(): void
    {
        $cliente = Cliente::create([
            'cliente_nombre' => 'Carlos',
            'cliente_apellido' => 'Gómez',
            'cliente_dni' => '0501199900033',
            'cliente_estado_civil' => 'Soltero',
            'cliente_telefono' => '9000-0003',
            'cliente_direccion' => 'Calle 3',
            'cliente_numero_hijos' => 0,
            'cliente_estado' => 'activo',
        ]);

        $this->actingAs($this->director())
            ->delete(route('clientes.destroy', $cliente->cliente_dni))
            ->assertRedirect(route('clientes.index'));

        $this->assertDatabaseHas('clientes', [
            'cliente_id' => $cliente->cliente_id,
            'cliente_estado' => 'inactivo',
        ]);
    }

    /**
     * Verifica que un cliente inactivo pueda ser reactivado.
     *
     * Happy path: crea un cliente en estado 'inactivo', lo reactiva
     * y verifica que su estado cambie a 'activo'.
     */
    public function test_activar_reactiva_cliente_inactivo(): void
    {
        $cliente = Cliente::create([
            'cliente_nombre' => 'Carlos',
            'cliente_apellido' => 'Gómez',
            'cliente_dni' => '0501199900044',
            'cliente_estado_civil' => 'Soltero',
            'cliente_telefono' => '9000-0004',
            'cliente_direccion' => 'Calle 4',
            'cliente_numero_hijos' => 0,
            'cliente_estado' => 'inactivo',
        ]);

        $this->actingAs($this->director())
            ->post(route('clientes.activar', $cliente->cliente_dni))
            ->assertRedirect(route('clientes.show', $cliente->cliente_dni));

        $this->assertDatabaseHas('clientes', [
            'cliente_id' => $cliente->cliente_id,
            'cliente_estado' => 'activo',
        ]);
    }
}
