<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Defendant extends Model
{
    protected $table = 'defendant';
    protected $primaryKey = 'defendant_id';

    public function CaseFollow(): HasMany
    {
        return $this->hasMany(CaseFollow::class, 'defendant_id', 'defendant_id');
    }
}
