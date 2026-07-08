<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Modelo que representa una reasignación de un caso entre procuradores.
 *
 * Las reasignaciones registran el cambio de un caso de un procurador origen
 * a un procurador destino, incluyendo el motivo y observaciones.
 *
 * @property int $reasignacion_id
 * @property int $caso_id
 * @property int $procurador_origen_id
 * @property int $procurador_destino_id
 * @property string $reasignacion_fecha
 * @property string|null $reasignacion_motivo
 * @property string|null $reasignacion_observaciones
 * @property string $reasignacion_estado
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Caso $caso
 * @property-read Procurador $procuradorOrigen
 * @property-read Procurador $procuradorDestino
 */
class Reasignacion extends Model
{
    use HasFactory;

    protected $table = 'reasignaciones';

    protected $primaryKey = 'reasignacion_id';

    public $timestamps = true;

    protected $fillable = [
        'caso_id',
        'procurador_origen_id',
        'procurador_destino_id',
        'reasignacion_fecha',
        'reasignacion_motivo',
        'reasignacion_observaciones',
        'reasignacion_estado',
    ];

    protected function casts(): array
    {
        return [
            'reasignacion_fecha' => 'date',
        ];
    }

    /**
     * Relación con el caso que fue reasignado.
     */
    public function caso(): BelongsTo
    {
        return $this->belongsTo(Caso::class, 'caso_id');
    }

    /**
     * Relación con el procurador de origen (que entrega el caso).
     */
    public function procuradorOrigen(): BelongsTo
    {
        return $this->belongsTo(Procurador::class, 'procurador_origen_id');
    }

    /**
     * Relación con el procurador de destino (que recibe el caso).
     */
    public function procuradorDestino(): BelongsTo
    {
        return $this->belongsTo(Procurador::class, 'procurador_destino_id');
    }
}
