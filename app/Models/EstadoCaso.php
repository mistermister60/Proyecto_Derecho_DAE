<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Modelo que representa un estado posible de un caso judicial en el sistema.
 *
 * Los estados definen la etapa del pipeline del caso (ej: ingresado, en trámite,
 * resuelto, etc.) y pueden tener un orden, color y tipo asociados.
 *
 * @property int $estado_id
 * @property string $estado_nombre
 * @property int $estado_orden
 * @property string $estado_color
 * @property string $estado_tipo
 * @property string $estado_estado
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Caso> $casos
 */
class EstadoCaso extends Model
{
    use HasFactory;

    protected $table = 'estados_caso';

    protected $primaryKey = 'estado_id';

    public $timestamps = true;

    protected $fillable = [
        'estado_nombre',
        'estado_orden',
        'estado_color',
        'estado_tipo',
        'estado_estado',
    ];

    /**
     * Relación con los casos que tienen este estado.
     */
    public function casos(): HasMany
    {
        return $this->hasMany(Caso::class, 'estado_id', 'estado_id');
    }
}
