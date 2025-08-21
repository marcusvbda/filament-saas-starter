<?php

namespace App\Models;

use App\Models\traits\hasCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    use hasCompany;
    protected $fillable = [
        'name',
        'email',
        'phone',
        'document',
        'company_id'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function contract(): BelongsTo
    {
        return $this->BelongsTo(Contract::class);
    }
}
