<?php

namespace Database\Factories;

use App\Models\Reasignacion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reasignacion>
 */
class ReasignacionFactory extends Factory
{
    protected $model = Reasignacion::class;

    public function definition(): array
    {
        return [
            'caso_id' => null,
            'procurador_origen_id' => null,
            'procurador_destino_id' => null,
            'reasignacion_motivo' => fake()->sentence(),
            'reasignacion_fecha' => fake()->dateTime(),
            'reasignacion_estado' => 'completada',
        ];
    }
}
