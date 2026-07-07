<?php

namespace App\Services\Dashboard;

use App\Models\Classes;
use App\Models\GenerateFeeChallan;
use App\Models\Staff;
use App\Models\StaffAttendance;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentInquiry;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class DashboardService
{

    public function getClassesStudents($filter_date = null)
    {
        $tenantId = session('switched_tenant_id') ?? tenant('id');
        $date = $filter_date ?? Carbon::today()->toDateString();
        $classTypeIds = campusClassList();

        // Get classes with total students
        $classes = Classes::whereIn('class_type_id', $classTypeIds)
            ->withCount([
                'students as students_count' => function ($q) use ($tenantId) {
                    $q->where('tenant_id', $tenantId)
                        ->where('IsActive', 1);
                }
            ])
            ->get(['id', 'ClassName']);

        // Get attendance summary in ONE query
        $attendance = StudentAttendance::where('tenant_id', $tenantId)
            ->whereDate('AttendanceDate', $date)
            ->whereIn('ClassId', $classes->pluck('id'))
            ->selectRaw("
            ClassId,
            SUM(AttendanceType = 'Present') as present,
            SUM(AttendanceType IN ('Absent','Leave')) as absent_leave
        ")
            ->groupBy('ClassId')
            ->get()
            ->keyBy('ClassId');

        $result = [];
        $totalPresent = 0;
        $totalAbsentLeave = 0;

        foreach ($classes as $class) {
            $present = $attendance[$class->id]->present ?? 0;
            $absentLeave = $attendance[$class->id]->absent_leave ?? 0;

            $totalPresent += $present;
            $totalAbsentLeave += $absentLeave;

            $result[] = [
                'class' => $class->ClassName,
                'total' => $class->students_count,
                'present' => $present,
                'absent_leave' => $absentLeave,
            ];
        }

        // Total row
        $result[] = [
            'class' => 'Total',
            'total' => $classes->sum('students_count'),
            'present' => $totalPresent,
            'absent_leave' => $totalAbsentLeave,
        ];

        return $result;
    }

    public function getChallanData($year = null, $month = null): array
    {
        $tenant_id = tenant('id');
        if (session('switched_tenant_id')) {
            $tenant_id = session('switched_tenant_id');
        }

        $year = $year ?? Carbon::now()->year;
        $month = $month ?? Carbon::now()->month;

        $query = GenerateFeeChallan::where('tenant_id', $tenant_id)
            ->whereYear('ChallanMonth', $year)
            ->whereMonth('ChallanMonth', $month);

        return [
            'unpaid_fee' => (clone $query)->where('Status', 'Unpaid')->where('IsPartialPayment', 0)->count(),
            'fully_paid' => (clone $query)->where('Status', 'Paid')->where('IsPartialPayment', 0)->count(),
            'partial_paid' => (clone $query)->where('Status', 'Unpaid')->where('IsPartialPayment', 1)->count(),
        ];
    }

    public function teacherAttendance($filter_date = null): array
    {
        $tenant_id = tenant('id');
        if (session('switched_tenant_id')) {
            $tenant_id = session('switched_tenant_id');
        }

        $date = $filter_date ?? Carbon::today()->toDateString();
        $query = StaffAttendance::select('id', 'tenant_id', 'IsActive', 'StaffId', 'Attendance', 'AttendanceDate')
            ->where('tenant_id', $tenant_id)
            ->whereDate('AttendanceDate', $date);

        return [
            'total' => Staff::where('IsActive', 1)->where('tenant_id', $tenant_id)->count(),
            'present' => (clone $query)->where('Attendance', 'Present')->count(),
            'absent' => (clone $query)->where('Attendance', 'Absent')->count(),
            'leave' => (clone $query)->where('Attendance', 'Leave')->count(),
            'teachers_attendance' => (clone $query)->where('IsActive', 1)->with('staff:id,tenant_id,DepartmentId,DesignationId,FirstName,LastName', 'staff.DesignationRel', 'staff.DepartmentRel')->get(),
        ];
    }

    public function attendanceTableFilter($filter_date = null, $status_filter = null): array
    {
        $tenant_id = tenant('id');
        if (session('switched_tenant_id')) {
            $tenant_id = session('switched_tenant_id');
        }
        $date = $filter_date ?? Carbon::today()->toDateString();
        $query = StaffAttendance::select('id', 'tenant_id', 'IsActive', 'StaffId', 'Attendance', 'AttendanceDate')
            ->where('tenant_id', $tenant_id)
            ->whereDate('AttendanceDate', $date);

        if (!is_null($status_filter) && $status_filter !== '') {
            $query->where('Attendance', $status_filter);
        }

        return [
            'teachers_attendance' => (clone $query)->where('IsActive', 1)->with('staff:id,tenant_id,DepartmentId,DesignationId,FirstName,LastName', 'staff.DepartmentRel', 'staff.DesignationRel')->get(),
        ];
    }

    public function studentsInquiries($filter_year = null): array
    {
        $tenant_id = tenant('id');
        if (session('switched_tenant_id')) {
            $tenant_id = session('switched_tenant_id');
        }
        $year = $filter_year ?? now()->year;
        $students = Student::where('tenant_id', $tenant_id)
            ->whereYear('CreatedDate', $year)
            ->selectRaw('MONTH(CreatedDate) as month, COUNT(*) as total')
            ->groupBy('month')
            ->where('IsDisable', 0)
            ->where('IsActive', 1)
            ->pluck('total', 'month')
            ->toArray();
        $inquiries = StudentInquiry::where('tenant_id', $tenant_id)
            ->whereYear('created_at', $year)
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();
        $months = range(1, 12);
        $studentsMonthly = array_map(fn($m) => $students[$m] ?? 0, $months);
        $inquiriesMonthly = array_map(fn($m) => $inquiries[$m] ?? 0, $months);

        return [
            'year'      => $year,
            'students'   => $studentsMonthly,
            'inquiries'  => $inquiriesMonthly,
        ];
    }
}
