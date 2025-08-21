<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractTemplate extends Model
{
    public $fillable = [
        'name',
        'content'
    ];

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function company(): BelongsTo
    {
        return $this->BelongsTo(Company::class);
    }
}
