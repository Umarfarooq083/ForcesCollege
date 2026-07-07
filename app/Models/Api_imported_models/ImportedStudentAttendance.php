<?php

namespace App\Models\Api_imported_models;

use Illuminate\Database\Eloquent\Model;

class ImportedStudentAttendance extends Model
{
    protected $table = 'imported_student_attendance';
    protected $fillable = [
        'studentId','attendanceType','attendanceDate','imported_attendance_id',
        'internal_attendance_id','sessionId','note','isFromMachine','isActive',
        'createdBy','createdDate'
    ];
}
