<?php

namespace App\Models;

use App\Models\traits\hasCompany;
use Illuminate\Database\Eloquent\Model;

class CompanyInvitation extends Model
{
    use hasCompany;
    protected $fillable = [
        'email',
        'role',
    ];
}
