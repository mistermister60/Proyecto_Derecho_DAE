<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';
    protected $primaryKey = 'cliente_id';

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

    public function getNombreCompletoAttribute()
    {
        return "{$this->cliente_nombre} {$this->cliente_apellido}";
    }

    public function casos()
    {
        return $this->hasMany(Caso::class, 'cliente_id');
    }
}
