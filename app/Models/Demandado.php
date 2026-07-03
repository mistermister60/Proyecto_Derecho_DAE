<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Demandado extends Model
{
    protected $table = 'demandados';

    protected $primaryKey = 'demandado_id';

    protected $fillable = [
        'demandado_nombre',
        'demandado_apellido',
        'demandado_dni',
        'demandado_estado_civil',
        'demandado_telefono',
        'demandado_direccion',
        'demandado_profesion',
        'demandado_lugar_trabajo',
        'demandado_telefono_trabajo',
        'demandado_estado',
    ];

    public function getNombreCompletoAttribute()
    {
        return "{$this->demandado_nombre} {$this->demandado_apellido}";
    }

    public function casos()
    {
        return $this->hasMany(Caso::class, 'demandado_id');
    }
}
