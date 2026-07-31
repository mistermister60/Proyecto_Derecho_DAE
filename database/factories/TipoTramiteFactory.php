<?php

namespace Database\Factories;

use App\Models\TipoTramite;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TipoTramite>
 */
class TipoTramiteFactory extends Factory
{
    protected $model = TipoTramite::class;

    public function definition(): array
    {
        return [
            'tramite_nombre' => fake()->unique()->randomElement(['Civil', 'Penal', 'Laboral', 'Administrativo', 'Familia', 'Mercantil', 'Constitucional']),
        ];
    }
}
