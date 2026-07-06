<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Modelo que representa un caso judicial en el sistema de gestión de despachos.
 *
 * Un caso es el expediente central del sistema, vinculando a un cliente con un demandado,
 * asignado a un procurador y categorizado por tipo de trámite y estado.
 *
 * @property int $caso_id
 * @property string $caso_numero_expediente
 * @property int $cliente_id
 * @property int|null $demandado_id
 * @property int $tipo_tramite_id
 * @property int $estado_id
 * @property int $procurador_id
 * @property string|null $caso_parte_representada
 * @property string|null $caso_juzgado
 * @property string|null $caso_fecha_interpuesta
 * @property string|null $caso_relacion_hechos
 * @property string|null $caso_observaciones_director
 * @property bool|null $caso_admisible
 * @property string|null $caso_fecha_asignacion
 * @property string $caso_estado
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Cliente $cliente
 * @property-read Demandado|null $demandado
 * @property-read TipoTramite $tipoTramite
 * @property-read EstadoCaso $estado
 * @property-read Procurador $procurador
 * @property-read Collection<int, Seguimiento> $seguimientos
 * @property-read Collection<int, Audiencia> $audiencias
 * @property-read Collection<int, Reasignacion> $reasignaciones
 * @property-read Collection<int, Documento> $documentos
 * @property-read Collection<int, Entrevista> $entrevistas
 */
class Caso extends Model
{
    use HasFactory;

    protected $table = 'casos';

    protected $primaryKey = 'caso_id';

    public $timestamps = true;

    protected $fillable = [
        'caso_numero_expediente',
        'cliente_id',
        'demandado_id',
        'tipo_tramite_id',
        'estado_id',
        'procurador_id',
        'caso_parte_representada',
        'caso_juzgado',
        'caso_fecha_interpuesta',
        'caso_relacion_hechos',
        'caso_observaciones_director',
        'caso_admisible',
        'caso_fecha_asignacion',
        'caso_estado',
    ];

    protected function casts(): array
    {
        return [
            'caso_admisible' => 'boolean',
            'caso_fecha_interpuesta' => 'date',
            'caso_fecha_asignacion' => 'date',
        ];
    }

    /**
     * Relación con el cliente asociado al caso.
     */
    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    /**
     * Relación con el demandado asociado al caso.
     */
    public function demandado(): BelongsTo
    {
        return $this->belongsTo(Demandado::class, 'demandado_id');
    }

    /**
     * Relación con el tipo de trámite del caso.
     */
    public function tipoTramite(): BelongsTo
    {
        return $this->belongsTo(TipoTramite::class, 'tipo_tramite_id');
    }

    /**
     * Relación con el estado actual del caso.
     */
    public function estado(): BelongsTo
    {
        return $this->belongsTo(EstadoCaso::class, 'estado_id');
    }

    /**
     * Relación con el procurador asignado al caso.
     */
    public function procurador(): BelongsTo
    {
        return $this->belongsTo(Procurador::class, 'procurador_id');
    }

    /**
     * Relación con los seguimientos del caso.
     */
    public function seguimientos(): HasMany
    {
        return $this->hasMany(Seguimiento::class, 'caso_id');
    }

    /**
     * Relación con las audiencias del caso.
     */
    public function audiencias(): HasMany
    {
        return $this->hasMany(Audiencia::class, 'caso_id');
    }

    /**
     * Relación con las reasignaciones del caso.
     */
    public function reasignaciones(): HasMany
    {
        return $this->hasMany(Reasignacion::class, 'caso_id');
    }

    /**
     * Relación con los documentos del caso.
     */
    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class, 'caso_id');
    }

    /**
     * Relación con las entrevistas del caso.
     */
    public function entrevistas(): HasMany
    {
        return $this->hasMany(Entrevista::class, 'caso_id');
    }
}
