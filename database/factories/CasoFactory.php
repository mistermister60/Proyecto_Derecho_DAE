<?php

namespace Database\Factories;

use App\Models\Caso;
use App\Models\Cliente;
use App\Models\Demandado;
use App\Models\EstadoCaso;
use App\Models\Procurador;
use App\Models\TipoTramite;
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
            'caso_numero_expediente' => '0501-' . date('Y') . '-' . $this->faker->unique()->numerify('#####'),
            'cliente_id' => Cliente::factory(),
            'demandado_id' => Demandado::factory(),
            'tipo_tramite_id' => TipoTramite::factory(),
            'procurador_id' => Procurador::factory(),
            'caso_parte_representada' => $this->faker->randomElement(['Demandante', 'Demandado']),
            'caso_juzgado' => $this->faker->optional()->company() . ' Juzgado',
            'caso_relacion_hechos' => $this->faker->paragraph(3),
            'caso_observaciones_director' => $this->faker->optional()->sentence(),
            'caso_fecha_interpuesta' => $this->faker->date(),
            'caso_fecha_asignacion' => $this->faker->date(),
            'caso_estado' => 'activo',
            'estado_id' => EstadoCaso::factory()->pipeline(),
            'resolucion_tipo' => null,
            'resolucion_fecha' => null,
            'resolucion_notas' => null,
        ];
    }
}