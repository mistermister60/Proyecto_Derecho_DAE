<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsTo;
class Interview extends Model
{
    protected $table = 'interview';
    protected $primaryKey = 'interview_id';

    public function tramit(): belongsTo
    {
        return $this->belongsTo(Tramit::class, 'tramit_id', 'tramit_id');
    }
    public function attorney(): belongsTo
    {
        return $this->belongsTo(Attorney::class, 'attorney_id', 'attorney_id');
    }
}
