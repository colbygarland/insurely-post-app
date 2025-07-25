<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SummaryPrompt extends Model
{
    protected $table = 'summary_prompt';

    protected $fillable = ['prompt'];

    public static function getLatest()
    {
        return self::latest()->first();
    }
}
