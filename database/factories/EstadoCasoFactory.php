<?php

namespace Database\Factories;

use App\Models\EstadoCaso;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EstadoCaso>
 */
class EstadoCasoFactory extends Factory
{
    protected $model = EstadoCaso::class;

    public function definition(): array
    {
        return [
            'estado_nombre' => fake()->unique()->word(),
            'estado_tipo' => fake()->randomElement(['pipeline', 'cerrado']),
            'estado_orden' => fake()->numberBetween(1, 10),
            'estado_color' => fake()->hexColor(),
        ];
    }

    public function pipeline(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado_tipo' => 'pipeline',
        ]);
    }

    public function cerrado(): static
    {
        return $this->state(fn (array $attributes) => [
            'estado_tipo' => 'cerrado',
        ]);
    }
}