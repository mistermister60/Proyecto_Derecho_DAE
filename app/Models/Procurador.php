<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * Modelo que representa un procurador (abogado) en el sistema de gestión de despachos judiciales.
 *
 * Los procuradores son los profesionales que representan legalmente a los clientes
 * y están asignados a uno o varios casos. Cada procurador puede tener un usuario
 * asociado para acceder al sistema.
 *
 * @property int $procurador_id
 * @property string $procurador_nombre
 * @property string $procurador_apellido
 * @property string $procurador_dni
 * @property string|null $procurador_carnet
 * @property string $procurador_fecha_nacimiento
 * @property string $procurador_genero
 * @property string $procurador_email
 * @property string|null $procurador_telefono
 * @property string|null $procurador_direccion
 * @property string $procurador_estado
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Caso> $casos
 * @property-read Collection<int, Audiencia> $audiencias
 * @property-read Collection<int, Reasignacion> $reasignacionesOrigen
 * @property-read Collection<int, Reasignacion> $reasignacionesDestino
 * @property-read Usuario|null $usuario
 */
class Procurador extends Model
{
    use HasFactory;

    protected $table = 'procuradores';

    protected $primaryKey = 'procurador_id';

    public $timestamps = true;

    protected $fillable = [
        'procurador_nombre',
        'procurador_apellido',
        'procurador_dni',
        'procurador_carnet',
        'procurador_fecha_nacimiento',
        'procurador_genero',
        'procurador_email',
        'procurador_telefono',
        'procurador_direccion',
        'procurador_estado',
    ];

    protected function casts(): array
    {
        return [
            'procurador_fecha_nacimiento' => 'date',
        ];
    }

    /**
     * Obtener el nombre completo del procurador.
     */
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->procurador_nombre} {$this->procurador_apellido}";
    }

    /**
     * Relación con los casos asignados a este procurador.
     */
    public function casos(): HasMany
    {
        return $this->hasMany(Caso::class, 'procurador_id');
    }

    /**
     * Relación con las audiencias asignadas a este procurador.
     */
    public function audiencias(): HasMany
    {
        return $this->hasMany(Audiencia::class, 'procurador_id');
    }

    /**
     * Relación con las reasignaciones donde este procurador fue el origen.
     */
    public function reasignacionesOrigen(): HasMany
    {
        return $this->hasMany(Reasignacion::class, 'procurador_origen_id');
    }

    /**
     * Relación con las reasignaciones donde este procurador fue el destino.
     */
    public function reasignacionesDestino(): HasMany
    {
        return $this->hasMany(Reasignacion::class, 'procurador_destino_id');
    }

    /**
     * Relación con el usuario asociado a este procurador.
     */
    public function usuario(): HasOne
    {
        return $this->hasOne(Usuario::class, 'procurador_id', 'procurador_id');
    }
}
