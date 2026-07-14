<?php

namespace App\Services;

use App\Models\Department;
use App\Models\HumanResourceLog;
use App\Models\Staff;
use App\Models\StaffAttendance;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class StaffAttendanceService
{
    public function getOfficeStartTime(): string
    {
        $officeStartTime = getSiteSettingValue('office_start_time');
        $parsedOfficeStartTime = $officeStartTime ? strtotime($officeStartTime) : false;

        return $parsedOfficeStartTime ? date('H:i', $parsedOfficeStartTime) : '09:00';
    }

    protected function calculateLateMinutes(string $officeStartTime): int
    {
        $officeStart = Carbon::createFromFormat('H:i', $officeStartTime);
        $attendanceTime = Carbon::now();

        if ($attendanceTime->greaterThan($officeStart)) {
            return (int) $officeStart->diffInMinutes($attendanceTime);
        }

        return 0;
    }

    public function staffAttendanceList($request): array
    {
        $data['departments'] = Department::get();
        $data['staffs'] = $this->getStaffList($request);

        $existingAttendance = [];
        if ($request->filled('date') && ! $data['staffs']->isEmpty()) {
            $ids = $data['staffs']->pluck('id')->toArray();
            $attendances = StaffAttendance::where('AttendanceDate', $request->date)
                ->where('tenant_id', tenant('id'))
                ->whereIn('StaffId', $ids)
                ->get(['StaffId', 'Attendance', 'start_time', 'end_time', 'late_minutes']);

            foreach ($attendances as $att) {
                $existingAttendance[$att->StaffId] = [
                    'attendance' => $att->Attendance,
                    'start_time' => $att->start_time ? date('H:i', strtotime($att->start_time)) : '',
                    'end_time' => $att->end_time ? date('H:i', strtotime($att->end_time)) : '',
                    'late_minutes' => $att->late_minutes,
                ];
            }
        }

        $data['existingAttendance'] = $existingAttendance;
        $data['filters'] = $request->only(['department_id', 'date', 'all_staff']);
        $data['AttendanceDate'] = $request->date;

        return $data;
    }

    public function getStaffList($request): Collection
    {
        $staffs_qry = Staff::select('id', 'tenant_id', 'DepartmentId', 'FirstName', 'LastName', 'IsActive')->where('tenant_id', tenant('id'))->where('IsActive', 1)->with('DepartmentRel');
        if ($request->has('all_staff') && $request->boolean('all_staff')) {
            return $staffs_qry->get();
        } elseif ($request->filled('department_id')) {
            return $staffs_qry->where('DepartmentId', $request->department_id)->get();
        } else {
            return collect();
        }
    }

    public function submitStaffAttendance($request): void
    {
        $attendance = $request->input('attendance');
        $departmentId = $request->input('department_id');
        $date = $request->input('date');
        $currentSession = fetchCurrentSession();
        foreach ($attendance as $staffId => $data) {
            $status = is_array($data) ? $data['status'] : $data;

            $isPresent = (string) $status === '1';
            $startTime = $isPresent && is_array($data) && isset($data['start_time']) ? $data['start_time'] : null;
            $endTime = $isPresent && is_array($data) && isset($data['end_time']) ? $data['end_time'] : null;

            $lateMinutes = 0;
            if ($isPresent && $startTime) {
                $lateMinutes = $this->calculateLateMinutes($startTime);
            }

            $existingAttendance = StaffAttendance::where('StaffId', $staffId)->where('tenant_id', tenant('id'))
                ->where('AttendanceDate', $date)
                ->first();

            $attendanceType = match ($status) {
                '0' => 'Absent',
                '1' => 'Present',
                '2' => 'Leave',
                default => 'Present'
            };

            if ($existingAttendance) {
                $updated = $existingAttendance->update([
                    'Attendance' => $attendanceType,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'late_minutes' => $lateMinutes,
                    'ModifiedBy' => auth()->id(),
                ]);

                if ($updated) {
                    userActivityLogs('Staff Attendance Updated and By User ID: '.auth()->user()->id.'', HumanResourceLog::class);
                }

            } else {
                $created = StaffAttendance::create([
                    'tenant_id' => tenant('id'),
                    'SessionId' => $currentSession->id,
                    'department_id' => (int) $departmentId,
                    'StaffId' => $staffId,
                    'AttendanceDate' => $date,
                    'Attendance' => $attendanceType,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'late_minutes' => $lateMinutes,
                    'IsActive' => true,
                    'CreatedBy' => auth()->user()->id,
                ]);

                if ($created) {
                    userActivityLogs('Staff Attendance Created and By User ID: '.auth()->user()->id.'', HumanResourceLog::class);
                }
            }
        }
    }
}
