<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamSubject extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id', 'SchoolId', 'IsActive', 'CreatedBy', 'ModifiedBy', 'SessionId',
        'Title', 'ExamId', 'SubjectId', 'Date', 'Time', 'Duration', 'CreditHours',
        'RoomNo', 'MarksMax', 'MarksMin', 'ExamSubjectGroupId', 'imported_exam_subject_id',
    ];

    protected $casts = [
        'Time' => 'date:H:i A',
    ];

    public function ExamType()
    {
        return $this->belongsTo(ExamType::class, 'ExamId');
    }

    public function Class()
    {
        return $this->belongsTo(Classes::class, 'ClassId');
    }

    public function Subject()
    {
        return $this->belongsTo(Subject::class, 'SubjectId');
    }

    public function exammarks()
    {
        return $this->belongsTo(ExamMarks::class, 'id', 'ExamSubjectId');
    }

    public function student()
    {
        return $this->belongsTo(ExamStudent::class, 'id', 'ExamSubjectId');
    }
}
