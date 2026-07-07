<?php

namespace App\Models\Api_imported_models;

use Illuminate\Database\Eloquent\Model;

class ImportedSubjects extends Model
{
    protected $table = 'imported_subject';
    protected $fillable = [
        'subjectName','subjectType','subjectCode','classId','isActive','sessionId','imported_subject_id','internal_subject_id'
    ];
}
