<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsTo;
class CaseInform extends Model
{
    protected $table = 'caseinform';
    protected $primaryKey = 'caseinform_id';

    public function attorney(): belongsTo
    {
        return $this->belongsTo(Attorney::class, 'attorney_id', 'attorney_id');
    }
    public function cases(): belongsTo
    {
        return $this->belongsTo(Cases::class, 'cases_id', 'cases_id');
    }
}
