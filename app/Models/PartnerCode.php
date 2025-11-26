<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PartnerCode extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'company',
        'email',
        'description',
        'code',
        'fund_serve_code',
        'type',
    ];
}
