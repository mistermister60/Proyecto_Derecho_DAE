<?php

namespace Database\Factories;

use App\Models\Procurador;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Procurador>
 */
class ProcuradorFactory extends Factory
{
    protected $model = Procurador::class;

    public function definition(): array
    {
        return [
            'procurador_nombre' => fake()->firstName(),
            'procurador_apellido' => fake()->lastName(),
            'procurador_dni' => fake()->unique()->numerify('####-####-#####'),
            'procurador_carnet' => fake()->optional()->unique()->bothify('CAR-#####'),
            'procurador_fecha_nacimiento' => fake()->date('Y-m-d', '-30 years'),
            'procurador_genero' => fake()->randomElement(['Masculino', 'Femenino']),
            'procurador_email' => fake()->unique()->safeEmail(),
            'procurador_telefono' => fake()->numerify('+504 ####-####'),
            'procurador_direccion' => fake()->address(),
            'procurador_estado' => 'activo',
        ];
    }
}