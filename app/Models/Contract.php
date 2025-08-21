<?php

namespace App\Models;

use App\Models\traits\hasCompany;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Contract extends Model
{
    use hasCompany;

    protected $fillable = [
        'name',
        'contract_template_id',
        'company_id',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

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
