<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnalyzePrompt extends Model
{
    protected $table = 'analyze_prompt';

    protected $fillable = ['prompt'];

    public static function getLatest()
    {
        return self::latest()->first();
    }
}
