<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Modelo que representa un documento adjunto a un caso judicial.
 *
 * Los documentos son archivos digitales (PDF, imágenes, etc.) asociados
 * a un caso, como escritos, pruebas, resoluciones, etc.
 *
 * @property int $documento_id
 * @property int $caso_id
 * @property string $documento_nombre
 * @property string|null $documento_tipo
 * @property string|null $documento_ruta
 * @property string|null $documento_tamano
 * @property string|null $documento_descripcion
 * @property string $documento_estado
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Caso $caso
 */
class Documento extends Model
{
    use HasFactory;

    protected $table = 'documentos';

    protected $primaryKey = 'documento_id';

    public $timestamps = true;

    protected $fillable = [
        'caso_id',
        'documento_nombre',
        'documento_tipo',
        'documento_ruta',
        'documento_tamano',
        'documento_descripcion',
        'documento_estado',
    ];

    /**
     * Relación con el caso al que pertenece el documento.
     */
    public function caso(): BelongsTo
    {
        return $this->belongsTo(Caso::class, 'caso_id');
    }
}
