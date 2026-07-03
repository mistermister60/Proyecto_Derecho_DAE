<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    protected $table = 'roles';

    protected $primaryKey = 'rol_id';

    protected $fillable = [
        'rol_nombre',
        'rol_descripcion',
        'rol_estado',
    ];

    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'rol_id');
    }
}
