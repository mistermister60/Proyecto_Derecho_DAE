<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\HasApiTokens;

/**
 * Modelo que representa un usuario del sistema de gestión de despachos judiciales.
 *
 * Los usuarios son las personas que acceden al sistema, con roles y permisos
 * específicos. Pueden estar asociados a un procurador y registran seguimientos.
 *
 * @property int $usuario_id
 * @property int $rol_id
 * @property int|null $procurador_id
 * @property string $usuario_nombre
 * @property string $email
 * @property string $contrasena
 * @property string $usuario_estado
 * @property string|null $remember_token
 * @property string|null $push_notification_token
 * @property string|null $push_subscription
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Rol $rol
 * @property-read Procurador|null $procurador
 * @property-read Collection<int, Seguimiento> $seguimientos
 */
class Usuario extends Model implements AuthenticatableContract, AuthorizableContract
{
    use AuthenticatableTrait, Authorizable, HasApiTokens, HasFactory;

    protected $table = 'usuarios';

    protected $primaryKey = 'usuario_id';

    public $timestamps = true;

    protected $fillable = [
        'rol_id',
        'procurador_id',
        'usuario_nombre',
        'email',
        'contrasena',
        'usuario_estado',
        'push_notification_token',
        'push_subscription',
    ];

    protected $hidden = [
        'contrasena',
        'remember_token',
        'push_notification_token',
        'push_subscription',
    ];

    /**
     * Obtener la contraseña para la autenticación.
     */
    public function getAuthPassword(): string
    {
        return $this->contrasena;
    }

    /**
     * Relación con el rol asignado al usuario.
     */
    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    /**
     * Relación con el procurador asociado al usuario.
     */
    public function procurador(): BelongsTo
    {
        return $this->belongsTo(Procurador::class, 'procurador_id');
    }

    /**
     * Relación con los seguimientos registrados por el usuario.
     */
    public function seguimientos(): HasMany
    {
        return $this->hasMany(Seguimiento::class, 'usuario_id');
    }
}
