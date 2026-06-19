<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Tramit extends Model
{
    protected $table = 'tramit';
    protected $primaryKey = 'tramit_id';

    public function CaseFollow(): HasMany
    {
        return $this->hasMany(CaseFollow::class, 'tramit_id', 'tramit_id');
    }
    public function Interview(): HasMany
    {
        return $this->hasMany(Interview::class, 'tramit_id', 'tramit_id');
    }
}
