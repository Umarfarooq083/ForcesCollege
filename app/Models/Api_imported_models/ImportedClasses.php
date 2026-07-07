<?php

namespace App\Models\Api_imported_models;

use Illuminate\Database\Eloquent\Model;

class ImportedClasses extends Model
{
    protected $fillable = [
        'className','imported_class_id','internal_class_id','isActive'
    ];
}
