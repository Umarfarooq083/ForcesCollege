<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamType extends Model
{
    use SoftDeletes;

    protected $table = 'exam';

    protected $casts = [
        'IsPublishResult' => 'boolean',
    ];

    protected $fillable = [
        'tenant_id',
        'SchoolId',
        'IsActive',
        'CreatedBy',
        'ModifiedBy',
        'ExamName',
        'SessionId',
        'ExamTermId',
        'ResultDeclarationDate',
        'IsPublishResult',
        'Description',
        'imported_exam_id',
    ];

    public function examTerm()
    {
        return $this->belongsTo(ExamTerm::class, 'ExamTermId');
    }

    public function examStudents()
    {
        return $this->belongsTo(ExamStudent::class, 'id', 'ExamId');
    }

    public function SessionRel()
    {
        return $this->belongsTo(LmsSession::class, 'SessionId');
    }

    public function ExamSubjectRel()
    {
        return $this->belongsTo(ExamSubject::class, 'id', 'ExamId')->select('id', 'tenant_id', 'IsActive', 'SessionId', 'ExamId', 'SubjectId');
    }
}
