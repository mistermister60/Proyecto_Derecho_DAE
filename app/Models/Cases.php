<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Cases extends Model
{
    protected $table = 'case';
    protected $primaryKey = 'cases_id';

    public function CaseInform(): HasMany
    {
        return $this->hasMany(CaseInform::class, 'cases_id', 'cases_id');
    }
}
