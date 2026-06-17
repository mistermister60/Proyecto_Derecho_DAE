<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\belongsTo;
class CaseFollow extends Model
{
    protected $table = 'casefollow';
    protected $primaryKey = 'casefollow_id';

    public function tramit(): belongsTo
    {
        return $this->belongsTo(Tramit::class, 'tramit_id', 'tramit_id');
    }

    public function client(): belongsTo
    {
        return $this->belongsTo(Client::class, 'client_id', 'client_id');
    }

    public function defendant(): belongsTo
    {
        return $this->belongsTo(Defendant::class, 'defendant_id', 'defendant_id');

    }
}
