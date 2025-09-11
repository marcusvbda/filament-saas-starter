<?php

namespace App\Models;

use App\Models\traits\hasCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractTemplate extends Model
{
    use hasCompany;
    use SoftDeletes;

    public $fillable = [
        'name',
        'content'
    ];

    public function company(): BelongsTo
    {
        return $this->BelongsTo(Company::class);
    }
}
