<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $table = 'documents';

    protected $fillable = [
        'name',
        'type',
        'file_name',
        'updatedBy',
    ];

    public static $TYPE = ['underwriting_manuals', 'product_feature_guides', 'wording_booklets'];

    public function getUpdatedBy()
    {
        return User::find($this->updatedBy);
    }
}
