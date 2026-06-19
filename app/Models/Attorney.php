<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne; 
use Illuminate\Database\Eloquent\Relations\HasMany;
class Attorney extends Model
{
    protected $table = 'attorney';
    protected $primaryKey = 'attorney_id';

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'attorney_id', 'attorney_id');
    }
    public function Interview(): HasMany
    {
        return $this->hasMany(Interview::class, 'attorney_id', 'attorney_id');
    }
    public function CaseInform(): HasMany
    {
        return $this->hasMany(CaseInform::class, 'attorney_id', 'attorney_id');
    }
}
