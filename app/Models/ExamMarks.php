<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamMarks extends Model
{
    use SoftDeletes;

    protected $table = 'exam_marks';

    protected $fillable = [
        'tenant_id',
        'SchoolId',
        'IsActive',
        'IsDeleted',
        'CreatedBy',
        'CreatedDate',
        'ModifiedBy',
        'ModifiedDate',
        'SessionId',
        'ExamSubjectId',
        'ExamMarksGroupId',
        'imported_exam_marks_id',
    ];

    public function ExamSubject()
    {
        return $this->hasOne(ExamSubject::class, 'id', 'ExamSubjectId');
    }

    public function ExamMarksDetails()
    {
        return $this->hasMany(ExamMarksDetail::class, 'ExamMarksId', 'id');
    }
}
