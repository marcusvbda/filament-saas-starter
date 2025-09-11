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
        'integration_data',
        'additional_data'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public $casts = [
        'integration_data' => 'json',
        'additional_data' => 'json'
    ];

    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($contract) {
            $event = $contract->contractable;
            $contract->additional_data = [];
            if ($event) {
                $event->eventFillUrl()->delete();
            }
            $contract->saveQuietly();
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
}
