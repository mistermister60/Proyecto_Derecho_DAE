<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Modelo que representa un seguimiento o registro de actividad en un caso.
 *
 * Los seguimientos documentan las acciones realizadas sobre un caso,
 * como actualizaciones de estado, comentarios o eventos del sistema.
 *
 * @property int $seguimiento_id
 * @property int $caso_id
 * @property int|null $usuario_id
 * @property string $seguimiento_fecha
 * @property string $seguimiento_tipo
 * @property string $seguimiento_descripcion
 * @property string $seguimiento_estado
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Caso $caso
 * @property-read Usuario|null $usuario
 */
class Seguimiento extends Model
{
    use HasFactory;

    protected $table = 'seguimientos';

    protected $primaryKey = 'seguimiento_id';

    public $timestamps = true;

    protected $fillable = [
        'caso_id',
        'usuario_id',
        'seguimiento_fecha',
        'seguimiento_tipo',
        'seguimiento_descripcion',
        'seguimiento_estado',
    ];

    /**
     * Relación con el caso al que pertenece el seguimiento.
     */
    public function caso(): BelongsTo
    {
        return $this->belongsTo(Caso::class, 'caso_id');
    }

    /**
     * Relación con el usuario que realizó el seguimiento.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
