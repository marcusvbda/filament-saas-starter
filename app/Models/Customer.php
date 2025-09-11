<?php

namespace App\Models;

use App\Models\traits\hasCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use hasCompany;
    use SoftDeletes;

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
