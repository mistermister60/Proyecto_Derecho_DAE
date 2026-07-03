<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entrevista extends Model
{
    protected $table = 'entrevistas';

    protected $primaryKey = 'entrevista_id';

    protected $fillable = [
        'caso_id',
        'procurador_id',
        'entrevista_fecha',
        'entrevista_relacion_hechos',
        'entrevista_observaciones',
        'entrevista_estado',
    ];

    protected function casts(): array
    {
        return [
            'entrevista_fecha' => 'date',
        ];
    }

    public function caso()
    {
        return $this->belongsTo(Caso::class, 'caso_id');
    }

    public function procurador()
    {
        return $this->belongsTo(Procurador::class, 'procurador_id');
    }
}
