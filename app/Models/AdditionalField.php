<?php

namespace App\Models;

use App\Models\traits\hasCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdditionalField extends Model
{
    use hasCompany;
    use SoftDeletes;

    protected $fillable = [
        'fieldable',
        'data',
        'company_id',
        'sort_order'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'data' => "json"
    ];

    public function fieldable(): MorphTo
    {
        return $this->morphTo();
    }
}
