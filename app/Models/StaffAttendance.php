<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffAttendance extends Model
{
    use SoftDeletes;
    protected $table = 'staff_attendances';

    protected $fillable = [
        'tenant_id',
        'SchoolId',
        'IsActive',
        'CreatedBy',
        'ModifiedBy',
        'SessionId',
        'StaffId',
        'department_id',
        'Attendance',
        'start_time',
        'end_time',
        'late_minutes',
        'Note',
        'IsHolidayToday',
        'AttendanceDate',
        'StaffAttendanceGroupId',
    ];

    protected $casts = [
        // 'AttendanceDate' => 'date',
         'AttendanceDate' => 'date:Y-m-d',
    ];

    public function staff()
    {
        return $this->hasOne(Staff::class, 'id', 'StaffId');
    }

}
