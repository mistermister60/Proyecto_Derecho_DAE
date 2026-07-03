<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    protected $table = 'usuarios';

    protected $primaryKey = 'usuario_id';

    protected $fillable = [
        'rol_id',
        'procurador_id',
        'usuario_nombre',
        'email',
        'contrasena',
        'usuario_estado',
    ];

    protected $hidden = [
        'contrasena',
        'remember_token',
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
