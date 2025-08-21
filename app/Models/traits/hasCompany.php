<?php

namespace App\Models\traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Wallo\FilamentCompanies\FilamentCompanies;

trait hasCompany
{
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
        return $this->BelongsTo(FilamentCompanies::companyModel());
    }
}
