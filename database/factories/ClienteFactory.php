<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cliente>
 */
class ClienteFactory extends Factory
{
    protected $model = Cliente::class;

    public function definition(): array
    {
        return [
            'cliente_nombre' => fake()->firstName(),
            'cliente_apellido' => fake()->lastName(),
            'cliente_dni' => fake()->unique()->numerify('####-####-#####'),
            'cliente_estado_civil' => fake()->randomElement(['Soltero', 'Casado', 'Divorciado', 'Viudo']),
            'cliente_telefono' => fake()->numerify('+504 ####-####'),
            'cliente_direccion' => fake()->address(),
            'cliente_numero_hijos' => fake()->numberBetween(0, 5),
            'cliente_nombres_hijos' => fake()->optional()->sentence(3),
            'cliente_profesion' => fake()->optional()->jobTitle(),
            'cliente_lugar_trabajo' => fake()->optional()->company(),
            'cliente_direccion_trabajo' => fake()->optional()->address(),
            'cliente_telefono_trabajo' => fake()->optional()->numerify('+504 ####-####'),
            'cliente_salario_mensual' => fake()->optional()->randomFloat(2, 5000, 50000),
        ];
    }
}
