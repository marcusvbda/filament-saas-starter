<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'zipcode',
        'district',
        'street',
        'number',
        'complement',
        'state',
        'city',
        'addressable_type',
        'addressable_id',
    ];

    public function addressable()
    {
        return $this->morphTo();
    }
}
