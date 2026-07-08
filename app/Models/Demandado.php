<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Modelo que representa un demandado en el sistema de gestión de despachos judiciales.
 *
 * El demandado es la persona o entidad contra la cual se interpone una demanda.
 * Puede estar asociado a uno o varios casos.
 *
 * @property int $demandado_id
 * @property string $demandado_nombre
 * @property string $demandado_apellido
 * @property string $demandado_dni
 * @property string|null $demandado_estado_civil
 * @property string|null $demandado_telefono
 * @property string|null $demandado_direccion
 * @property string|null $demandado_profesion
 * @property string|null $demandado_lugar_trabajo
 * @property string|null $demandado_telefono_trabajo
 * @property string $demandado_estado
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Caso> $casos
 */
class Demandado extends Model
{
    use HasFactory;

    protected $table = 'demandados';

    protected $primaryKey = 'demandado_id';

    public $timestamps = true;

    protected $fillable = [
        'demandado_nombre',
        'demandado_apellido',
        'demandado_dni',
        'demandado_estado_civil',
        'demandado_telefono',
        'demandado_direccion',
        'demandado_profesion',
        'demandado_lugar_trabajo',
        'demandado_telefono_trabajo',
        'demandado_estado',
    ];

    /**
     * Obtener el nombre completo del demandado.
     */
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->demandado_nombre} {$this->demandado_apellido}";
    }

    /**
     * Relación con los casos donde esta persona es demandada.
     */
    public function casos(): HasMany
    {
        return $this->hasMany(Caso::class, 'demandado_id');
    }
}
