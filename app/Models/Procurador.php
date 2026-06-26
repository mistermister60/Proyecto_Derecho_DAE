<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Procurador extends Model
{
    protected $table = 'procuradores';
    protected $primaryKey = 'procurador_id';

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

    public function getNombreCompletoAttribute()
    {
        return "{$this->procurador_nombre} {$this->procurador_apellido}";
    }

    public function casos()
    {
        return $this->hasMany(Caso::class, 'procurador_id');
    }

    public function audiencias()
    {
        return $this->hasMany(Audiencia::class, 'procurador_id');
    }

    public function reasignacionesOrigen()
    {
        return $this->hasMany(Reasignacion::class, 'procurador_origen_id');
    }

    public function reasignacionesDestino()
    {
        return $this->hasMany(Reasignacion::class, 'procurador_destino_id');
    }
}
