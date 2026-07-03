<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audiencia extends Model
{
    protected $table = 'audiencias';

    protected $primaryKey = 'audiencia_id';

    protected $fillable = [
        'caso_id',
        'procurador_id',
        'audiencia_fecha',
        'audiencia_hora',
        'audiencia_juzgado',
        'audiencia_tipo',
        'audiencia_estado',
        'audiencia_observaciones',
    ];

    protected function casts(): array
    {
        return [
            'audiencia_fecha' => 'date',
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
