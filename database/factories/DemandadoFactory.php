<?php

namespace Database\Factories;

use App\Models\Demandado;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Demandado>
 */
class DemandadoFactory extends Factory
{
    protected $model = Demandado::class;

    public function definition(): array
    {
        return [
            'demandado_nombre' => fake()->firstName(),
            'demandado_apellido' => fake()->lastName(),
            'demandado_dni' => fake()->unique()->numerify('####-####-#####'),
            'demandado_estado_civil' => fake()->optional()->randomElement(['Soltero', 'Casado', 'Divorciado', 'Viudo']),
            'demandado_telefono' => fake()->optional()->numerify('+504 ####-####'),
            'demandado_direccion' => fake()->address(),
            'demandado_profesion' => fake()->optional()->jobTitle(),
            'demandado_lugar_trabajo' => fake()->optional()->company(),
            'demandado_telefono_trabajo' => fake()->optional()->numerify('+504 ####-####'),
        ];
    }
}