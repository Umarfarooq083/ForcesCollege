<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamMarksDetail extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'exam_marks_details';

    protected $fillable = [
        'tenant_id',
        'SchoolId',
        'IsActive',
        'CreatedBy',
        'ModifiedBy',
        'SessionId',
        'ExamMarksId',
        'ClassId',
        'StudentId',
        'Marks',
        'Remarks',
        'deleted_at',
        'imported_exam_detail_id'
    ];

    protected $casts = [
        'Marks' => 'decimal:2',
    ];

    public function exam(){
        return $this->hasOne(ExamType::class, 'id', 'ExamMarksId');
    }

    public function student(){
        return $this->hasOne(Student::class, 'id', 'StudentId');
    }

    public function examMarks(){
        return $this->hasOne(ExamMarks::class, 'id', 'ExamMarksId');
    }
}
