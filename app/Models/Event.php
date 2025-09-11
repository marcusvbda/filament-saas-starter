<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'customer_id',
        'contract_id',
        'company_id',
        'additional_data'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'start_date',
        'end_date',
    ];

    public $casts = [
        "additional_data" => "json"
    ];

    protected static function boot()
    {
        parent::boot();
        static::updating(function ($event) {
            if ($event->isDirty('contract_id')) {
                $contract = $event->contract;
                if ($contract) {
                    $contract->additional_data = [];
                    $contract->saveQuietly();
                }
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->BelongsTo(Company::class);
    }

    public function customer(): BelongsTo
    {
        return $this->BelongsTo(Customer::class);
    }

    public function contract(): MorphOne
    {
        return $this->morphOne(Contract::class, 'contractable');
    }

    public function getRenderPdfPayload(): array
    {
        return $this->load(["customer", "company"])->toArray();
    }

    public function eventFillUrl(): HasOne
    {
        return $this->hasOne(EventFillUrl::class);
    }
}
