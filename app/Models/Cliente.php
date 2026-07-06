<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Modelo que representa un cliente en el sistema de gestión de despachos judiciales.
 *
 * El cliente es la persona física que contrata los servicios legales del despacho.
 * Cada cliente puede tener uno o varios casos asociados.
 *
 * @property int $cliente_id
 * @property string $cliente_nombre
 * @property string $cliente_apellido
 * @property string $cliente_dni
 * @property string|null $cliente_estado_civil
 * @property string|null $cliente_telefono
 * @property string|null $cliente_direccion
 * @property int|null $cliente_numero_hijos
 * @property string|null $cliente_nombres_hijos
 * @property string|null $cliente_profesion
 * @property string|null $cliente_lugar_trabajo
 * @property string|null $cliente_direccion_trabajo
 * @property string|null $cliente_telefono_trabajo
 * @property float|null $cliente_salario_mensual
 * @property string $cliente_estado
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Caso> $casos
 */
class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $primaryKey = 'cliente_id';

    public $timestamps = true;

    protected $fillable = [
        'cliente_nombre',
        'cliente_apellido',
        'cliente_dni',
        'cliente_estado_civil',
        'cliente_telefono',
        'cliente_direccion',
        'cliente_numero_hijos',
        'cliente_nombres_hijos',
        'cliente_profesion',
        'cliente_lugar_trabajo',
        'cliente_direccion_trabajo',
        'cliente_telefono_trabajo',
        'cliente_salario_mensual',
        'cliente_estado',
    ];

    /**
     * Obtener el nombre completo del cliente.
     */
    public function getNombreCompletoAttribute(): string
    {
        return "{$this->cliente_nombre} {$this->cliente_apellido}";
    }

    /**
     * Relación con los casos asociados a este cliente.
     */
    public function casos(): HasMany
    {
        return $this->hasMany(Caso::class, 'cliente_id');
    }
}
