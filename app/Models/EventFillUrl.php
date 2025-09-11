<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventFillUrl extends Model
{
    protected $fillable = [
        'key',
        'filled',
        'event_id'
    ];

    public $casts = [
        "filled" => "boolean"
    ];

    public function event(): BelongsTo
    {
        return $this->BelongsTo(Event::class);
    }
}
