<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentAttendance extends Model
{
    use SoftDeletes;
    protected $table = 'student_attendances';
    protected $fillable = [
        'SchoolId',
        'tenant_id',
        'IsActive',
        'CreatedBy',
        'ModifiedBy',
        'ClassId',
        'SectionId',
        'SessionId',
        'StudentId',
        'AttendanceType',
        'AttendanceDate',
        'Note',
        'IsFromMachine',
        'imported_student_attendance_id'
    ];

    protected $casts = [
        'IsActive'       => 'boolean',
        'IsDeleted'      => 'boolean',
        'IsFromMachine'  => 'boolean',
        'CreatedDate'    => 'datetime',
        'ModifiedDate'   => 'datetime',
        'AttendanceDate' => 'date',
    ];

}
