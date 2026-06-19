<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Client extends Model
{
    protected $table = 'client';
    protected $primaryKey = 'client_id';

    public function CaseFollow(): HasMany
    {
        return $this->hasMany(CaseFollow::class, 'client_id', 'client_id');
    }
}
