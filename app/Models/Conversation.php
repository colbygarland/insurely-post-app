<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'message',
        'phone',
        'first_name',
        'last_name',
        'email',
    ];
}
