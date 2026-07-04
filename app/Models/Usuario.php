<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Laravel\Sanctum\HasApiTokens;

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

    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    public function procurador()
    {
        return $this->belongsTo(Procurador::class, 'procurador_id');
    }

    public function seguimientos()
    {
        return $this->hasMany(Seguimiento::class, 'usuario_id');
    }
}
