<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
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

    protected static function booted()
    {
        static::creating(function ($customer) {
            $user = auth()->user();
            $company = $user?->primaryCompany();
            if (!empty($company)) {
                $customer->company_id = $company->id;
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->BelongsTo(Company::class);
    }

    public function contract(): BelongsTo
    {
        return $this->BelongsTo(Contract::class);
    }
}
