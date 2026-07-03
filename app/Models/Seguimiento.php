<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seguimiento extends Model
{
    protected $table = 'seguimientos';

    protected $primaryKey = 'seguimiento_id';

    protected $fillable = [
        'caso_id',
        'usuario_id',
        'seguimiento_fecha',
        'seguimiento_tipo',
        'seguimiento_descripcion',
        'seguimiento_estado',
    ];

    public function caso()
    {
        return $this->belongsTo(Caso::class, 'caso_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }
}
