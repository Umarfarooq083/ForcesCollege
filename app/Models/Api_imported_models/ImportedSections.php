<?php

namespace App\Models\Api_imported_models;

use Illuminate\Database\Eloquent\Model;

class ImportedSections extends Model
{
    protected $fillable = [
        'sectionName','classId','isActive','createdBy','sessionId','createdDate'
    ];
}
