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
            'procurador_nombre' => $this->faker->firstName(),
            'procurador_apellido' => $this->faker->lastName(),
            'procurador_dni' => $this->faker->unique()->numerify('####-####-#####'),
            'procurador_carnet' => $this->faker->unique()->numerify('CAR-#####'),
            'procurador_fecha_nacimiento' => $this->faker->date('Y-m-d', '-30 years'),
            'procurador_genero' => $this->faker->randomElement(['Masculino', 'Femenino']),
            'procurador_email' => $this->faker->unique()->safeEmail(),
            'procurador_telefono' => $this->faker->numerify('+504 ####-####'),
            'procurador_direccion' => $this->faker->address(),
            'procurador_estado' => 'activo',
        ];
    }
}
