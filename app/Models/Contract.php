<?php

namespace App\Models;

use App\Models\traits\hasCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
