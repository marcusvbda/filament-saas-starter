<?php

namespace App\Models;

use App\Models\traits\hasCompany;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use hasCompany;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'contract_template_id',
        'company_id',
        'witnesses',
        'integration_data',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public $casts = [
        'integration_data' => 'json',
        'witnesses' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($contract) {
            $contractable = $contract->contractable;
            $contractable->eventFillUrl()->delete();
            $contractable->additional_data = [];
            $contractable->saveQuietly();
        });
    }

    public function contractTemplate(): BelongsTo
    {
        return $this->BelongsTo(ContractTemplate::class);
    }

    public function getFileName(): string
    {
        $dateTime = Carbon::now()->format("d/m/Y - H:i");
        return $this->name . " [$dateTime].pdf";
    }

    public function contractable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getReplacePayload()
    {
        $event = $this->contractable;
        $customer = $event->customer;

        $result = [
            "customers" => $customer->only(["name", "email", "phone", "document"]),
            "event" => $event->only(["start_date", "end_date"]),
            "contract" => $this->only(["name", "witnesses"]),
            "additional_data" => collect(data_get($event, "additional_data", []))->map(fn($x) => is_array($x) ? implodeSuffix(", ", $x) : $x)->toArray()
        ];
        return $result;
    }
}
