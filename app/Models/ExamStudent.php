<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamStudent extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'exam_students';

    protected $fillable = [
        'tenant_id',
        'IsActive',
        'CreatedBy',
        'ModifiedBy',
        'SessionId',
        'ExamId',
        'ExamSubjectId',
        'StudentId',
        'deleted_at',
    ];

    public function Student()
    {
        return $this->belongsTo(Student::class, 'StudentId');
    }

    public function Subject()
    {
        return $this->belongsTo(ExamSubject::class, 'ExamSubjectId');
    }
}
