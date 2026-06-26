<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Caso extends Model
{
    protected $table = 'casos';
    protected $primaryKey = 'caso_id';

    protected $fillable = [
        'caso_numero_expediente',
        'cliente_id',
        'demandado_id',
        'tipo_tramite_id',
        'estado_id',
        'procurador_id',
        'caso_parte_representada',
        'caso_juzgado',
        'caso_fecha_interpuesta',
        'caso_relacion_hechos',
        'caso_observaciones_director',
        'caso_admisible',
        'caso_fecha_asignacion',
        'caso_estado',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function demandado()
    {
        return $this->belongsTo(Demandado::class, 'demandado_id');
    }

    public function tipoTramite()
    {
        return $this->belongsTo(TipoTramite::class, 'tipo_tramite_id');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoCaso::class, 'estado_id');
    }

    public function procurador()
    {
        return $this->belongsTo(Procurador::class, 'procurador_id');
    }

    public function seguimientos()
    {
        return $this->hasMany(Seguimiento::class, 'caso_id');
    }

    public function audiencias()
    {
        return $this->hasMany(Audiencia::class, 'caso_id');
    }

    public function reasignaciones()
    {
        return $this->hasMany(Reasignacion::class, 'caso_id');
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class, 'caso_id');
    }

    public function entrevistas()
    {
        return $this->hasMany(Entrevista::class, 'caso_id');
    }
}
