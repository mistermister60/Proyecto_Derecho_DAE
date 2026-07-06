<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Modelo que representa un rol de usuario en el sistema de gestión de despachos judiciales.
 *
 * Los roles definen los permisos y responsabilidades de cada usuario dentro del sistema,
 * como administrador, procurador, director, etc.
 *
 * @property int $rol_id
 * @property string $rol_nombre
 * @property string|null $rol_descripcion
 * @property string $rol_estado
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection<int, Usuario> $usuarios
 */
class Rol extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $primaryKey = 'rol_id';

    public $timestamps = true;

    protected $fillable = [
        'rol_nombre',
        'rol_descripcion',
        'rol_estado',
    ];

    /**
     * Relación con los usuarios que tienen este rol.
     */
    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class, 'rol_id');
    }
}
