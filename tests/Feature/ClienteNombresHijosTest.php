<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\Rol;
use App\Models\Usuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ClienteNombresHijosTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_persiste_nombres_de_hijos(): void
    {
        $rol = Rol::create(['rol_nombre' => 'Director', 'rol_descripcion' => 'Director', 'rol_estado' => 'activo']);
        $director = Usuario::create([
            'rol_id' => $rol->rol_id, 'procurador_id' => null,
            'usuario_nombre' => 'Director', 'email' => 'director@test.hn',
            'contrasena' => Hash::make('secret123'), 'usuario_estado' => 'activo',
        ]);

        $this->actingAs($director)->post(route('clientes.store'), [
            'nombre_completo' => 'Juan Pérez',
            'cliente_dni' => '0501199900022',
            'cliente_estado_civil' => 'Casado',
            'cliente_telefono' => '9999-1111',
            'cliente_direccion' => 'San Pedro Sula',
            'cliente_numero_hijos' => 2,
            'cliente_nombres_hijos' => 'Ana Pérez, Luis Pérez',
        ])->assertRedirect(route('clientes.index'));

        $this->assertDatabaseHas('clientes', [
            'cliente_dni' => '0501199900022',
            'cliente_nombres_hijos' => 'Ana Pérez, Luis Pérez',
        ]);
    }

    public function test_update_persiste_nombres_de_hijos(): void
    {
        $rol = Rol::create(['rol_nombre' => 'Director', 'rol_descripcion' => 'Director', 'rol_estado' => 'activo']);
        $director = Usuario::create([
            'rol_id' => $rol->rol_id, 'procurador_id' => null,
            'usuario_nombre' => 'Director', 'email' => 'director@test.hn',
            'contrasena' => Hash::make('secret123'), 'usuario_estado' => 'activo',
        ]);

        $cliente = Cliente::create([
            'cliente_nombre' => 'Juan', 'cliente_apellido' => 'Pérez', 'cliente_dni' => '0501199900022',
            'cliente_estado_civil' => 'Soltero', 'cliente_telefono' => '9999-1111',
            'cliente_direccion' => 'SPS', 'cliente_numero_hijos' => 0, 'cliente_estado' => 'activo',
        ]);

        $this->actingAs($director)->put(route('clientes.update', ['identidad' => $cliente->cliente_dni]), [
            'cliente_nombre' => 'Juan',
            'cliente_apellido' => 'Pérez Modificado',
            'cliente_dni' => '0501199900022',
            'cliente_estado_civil' => 'Casado',
            'cliente_telefono' => '9999-1111',
            'cliente_direccion' => 'San Pedro Sula',
            'cliente_numero_hijos' => 2,
            'cliente_nombres_hijos' => 'Ana Pérez, Luis Pérez', // Campo auditado[cite: 1]
        ])->assertRedirect(route('clientes.show', $cliente->cliente_dni));

        $this->assertDatabaseHas('clientes', [
            'cliente_id' => $cliente->cliente_id,
            'cliente_nombres_hijos' => 'Ana Pérez, Luis Pérez',
        ]);
    }
}
