<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contract extends Model
{
    protected $fillable = [
        'data'
    ];

    public $casts = [
        'data' => 'json',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
