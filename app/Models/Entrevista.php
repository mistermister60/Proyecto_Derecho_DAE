<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Modelo que representa una entrevista realizada a un cliente en el contexto de un caso.
 *
 * Las entrevistas registran las reuniones entre el procurador y el cliente,
 * documentando la relación de hechos y observaciones relevantes.
 *
 * @property int $entrevista_id
 * @property int $caso_id
 * @property int $procurador_id
 * @property string $entrevista_fecha
 * @property string|null $entrevista_relacion_hechos
 * @property string|null $entrevista_observaciones
 * @property string $entrevista_estado
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Caso $caso
 * @property-read Procurador $procurador
 */
class Entrevista extends Model
{
    use HasFactory;

    protected $table = 'entrevistas';

    protected $primaryKey = 'entrevista_id';

    public $timestamps = true;

    protected $fillable = [
        'caso_id',
        'procurador_id',
        'entrevista_fecha',
        'entrevista_relacion_hechos',
        'entrevista_observaciones',
        'entrevista_estado',
    ];

    protected function casts(): array
    {
        return [
            'entrevista_fecha' => 'date',
        ];
    }

    /**
     * Relación con el caso al que pertenece la entrevista.
     */
    public function caso(): BelongsTo
    {
        return $this->belongsTo(Caso::class, 'caso_id');
    }

    /**
     * Relación con el procurador que realizó la entrevista.
     */
    public function procurador(): BelongsTo
    {
        return $this->belongsTo(Procurador::class, 'procurador_id');
    }
}
