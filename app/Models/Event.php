<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Event extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'customer_id',
        'contract_id',
        'company_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'start_date',
        'end_date',
    ];

    public function company(): BelongsTo
    {
        return $this->BelongsTo(Company::class);
    }

    public function customer(): BelongsTo
    {
        return $this->BelongsTo(Customer::class);
    }

    public function contracts(): MorphMany
    {
        return $this->morphMany(Contract::class, 'contractable');
    }
}
