<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Modelo que representa una audiencia judicial dentro de un caso.
 *
 * Las audiencias son citaciones programadas ante un juzgado, asociadas
 * a un caso y asignadas a un procurador.
 *
 * @property int $audiencia_id
 * @property int $caso_id
 * @property int $procurador_id
 * @property string $audiencia_fecha
 * @property string|null $audiencia_hora
 * @property string|null $audiencia_juzgado
 * @property string|null $audiencia_tipo
 * @property string $audiencia_estado
 * @property string|null $audiencia_observaciones
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Caso $caso
 * @property-read Procurador $procurador
 */
class Audiencia extends Model
{
    use HasFactory;

    protected $table = 'audiencias';

    protected $primaryKey = 'audiencia_id';

    public $timestamps = true;

    protected $fillable = [
        'caso_id',
        'procurador_id',
        'audiencia_fecha',
        'audiencia_hora',
        'audiencia_juzgado',
        'audiencia_tipo',
        'audiencia_estado',
        'audiencia_observaciones',
    ];

    protected function casts(): array
    {
        return [
            'audiencia_fecha' => 'date',
        ];
    }

    /**
     * Relación con el caso al que pertenece la audiencia.
     */
    public function caso(): BelongsTo
    {
        return $this->belongsTo(Caso::class, 'caso_id');
    }

    /**
     * Relación con el procurador asignado a la audiencia.
     */
    public function procurador(): BelongsTo
    {
        return $this->belongsTo(Procurador::class, 'procurador_id');
    }
}
