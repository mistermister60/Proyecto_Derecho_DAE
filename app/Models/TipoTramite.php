<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoTramite extends Model
{
    protected $table = 'tipos_tramite';

    protected $primaryKey = 'tipo_tramite_id';

    protected $fillable = [
        'tramite_nombre',
        'tramite_descripcion',
        'tramite_estado',
    ];

    public function casos()
    {
        return $this->hasMany(Caso::class, 'tipo_tramite_id');
    }
}
