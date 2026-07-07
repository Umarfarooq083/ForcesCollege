<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamTerm extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'tenant_id', 'SchoolId', 'IsActive', 'CreatedBy', 'ModifiedBy', 'SessionId', 'ExamTermName','imported_exam_term_id'
    ];
}
