<?php

namespace App\Models;

use App\Models\traits\hasCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractTemplate extends Model
{
    use hasCompany;

    public $fillable = [
        'name',
        'content'
    ];

    public function company(): BelongsTo
    {
        return $this->BelongsTo(Company::class);
    }
}
