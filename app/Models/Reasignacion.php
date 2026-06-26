<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reasignacion extends Model
{
    protected $table = 'reasignaciones';
    protected $primaryKey = 'reasignacion_id';

    protected $fillable = [
        'caso_id',
        'procurador_origen_id',
        'procurador_destino_id',
        'reasignacion_fecha',
        'reasignacion_motivo',
        'reasignacion_observaciones',
        'reasignacion_estado',
    ];

    public function caso()
    {
        return $this->belongsTo(Caso::class, 'caso_id');
    }

    public function procuradorOrigen()
    {
        return $this->belongsTo(Procurador::class, 'procurador_origen_id');
    }

    public function procuradorDestino()
    {
        return $this->belongsTo(Procurador::class, 'procurador_destino_id');
    }
}
