<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Modelo que representa un tipo de trámite judicial en el sistema.
 *
 * Los tipos de trámite categorizan los casos según la materia legal
 * (ej: divorcio, pensión alimenticia, sucesión, etc.).
 *
 * @property int $tipo_tramite_id
 * @property string $tramite_nombre
 * @property string|null $tramite_descripcion
 * @property string $tramite_estado
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Caso> $casos
 */
class TipoTramite extends Model
{
    use HasFactory;

    protected $table = 'tipos_tramite';

    protected $primaryKey = 'tipo_tramite_id';

    public $timestamps = true;

    protected $fillable = [
        'tramite_nombre',
        'tramite_descripcion',
        'tramite_estado',
    ];

    /**
     * Relación con los casos de este tipo de trámite.
     */
    public function casos(): HasMany
    {
        return $this->hasMany(Caso::class, 'tipo_tramite_id');
    }
}
