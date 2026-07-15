<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamGrade extends Model
{
    use SoftDeletes;

    protected $table = 'marks_grade';

    protected $fillable = [
        'tenant_id',
        'SchoolId',
        'IsActive',
        'CreatedBy',
        'ModifiedBy',
        'ClassId',
        'GradeName',
        'PercentFrom',
        'PercentUpt',
        'Description',
        'MarksGradeGroupId',
        'imported_marks_grade_id',
    ];

    public function classRel()
    {
        return $this->belongsTo(Classes::class, 'ClassId', 'id');
    }
}
