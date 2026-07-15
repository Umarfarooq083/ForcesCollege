<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomeWork extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'home_works';

    protected $fillable = [
        'tenant_id',
        'classId',
        'class',
        'sectionId',
        'section',
        'subjectGroupId',
        'subjectGroup',
        'subjectId',
        'subject',
        'homeworkDate',
        'submissionDate',
        'attachDocumentPath',
        'description',
        'homeworkGroupId',
        'homeworkGroup',
        'session',
        'schoolId',
        'isActive',
        'createdBy',
        'modifiedBy',
        'sessionId',
        'imported_home_work_id',
    ];

    public function ClassRel()
    {
        return $this->hasOne(Classes::class, 'id', 'classId');
    }

    public function SectionRel()
    {
        return $this->hasOne(Section::class, 'id', 'sectionId');
    }

    public function SubjectRel()
    {
        return $this->hasOne(Subject::class, 'id', 'subjectId');
    }
}
