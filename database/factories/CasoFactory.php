<?php

namespace Database\Factories;

use App\Models\Caso;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Caso>
 */
class CasoFactory extends Factory
{
    protected $model = Caso::class;

    public function definition(): array
    {
        return [
            'caso_numero_expediente' => '0501-' . date('Y') . '-' . fake()->unique()->numerify('#####'),
            'cliente_id' => null,
            'demandado_id' => null,
            'tipo_tramite_id' => null,
            'procurador_id' => null,
            'caso_parte_representada' => fake()->randomElement(['Demandante', 'Demandado']),
            'caso_juzgado' => fake()->optional()->company() . ' Juzgado',
            'caso_relacion_hechos' => fake()->paragraph(3),
            'caso_observaciones_director' => fake()->optional()->sentence(),
            'caso_fecha_interpuesta' => fake()->date(),
            'caso_fecha_asignacion' => fake()->date(),
            'caso_estado' => 'activo',
            'estado_id' => 1,
            'resolucion_tipo' => null,
            'resolucion_fecha' => null,
            'resolucion_notas' => null,
        ];
    }
}