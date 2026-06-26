<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoCaso extends Model
{
    protected $table = 'estados_caso';
    protected $primaryKey = 'estado_id';

    protected $fillable = [
        'estado_nombre',
        'estado_orden',
        'estado_color',
        'estado_tipo',
        'estado_estado',
    ];

    public function casos()
    {
        return $this->hasMany(Caso::class, 'estado_id');
    }
}
