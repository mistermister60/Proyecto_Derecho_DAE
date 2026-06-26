<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    protected $table = 'documentos';
    protected $primaryKey = 'documento_id';

    protected $fillable = [
        'caso_id',
        'documento_nombre',
        'documento_tipo',
        'documento_ruta',
        'documento_tamano',
        'documento_descripcion',
        'documento_estado',
    ];

    public function caso()
    {
        return $this->belongsTo(Caso::class, 'caso_id');
    }
}
