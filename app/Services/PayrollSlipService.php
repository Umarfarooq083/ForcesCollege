<?php

namespace App\Services;

use App\Models\FineDeduction;
use App\Models\GazettedLeave;
use App\Models\HumanResourceLog;
use App\Models\LateFine;
use App\Models\LeaveRequest;
use App\Models\MiscellaneousPayment;
use App\Models\PayrollSlip;
use App\Models\SalaryTax;
use App\Models\SecurityDeduction;
use App\Models\SecurityRefund;
use App\Models\Staff;
use App\Models\StaffAttendance;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PayrollSlipService
{
    protected CampusWeeklyHolidayService $campusWeeklyHolidayService;

    public function __construct(CampusWeeklyHolidayService $campusWeeklyHolidayService)
    {
        $this->campusWeeklyHolidayService = $campusWeeklyHolidayService;
    }

    public function getStaffList(): Collection
    {
        return Staff::select('id', 'FirstName', 'LastName', 'BasicSalary', 'TransportAllowance', 'ComputerAllowance', 'MobileAllowance', 'RecreationAllowance')
            ->where('tenant_id', tenant('id'))
            ->with('DesignationRel', 'DepartmentRel')
            ->where('IsActive', 1)
            ->get();
    }

    public function generatePayrollSlips(int $month, int $year, ?array $staffIds = null): array
    {
        $staffList = $staffIds
            ? Staff::whereIn('id', $staffIds)->where('IsActive', 1)->with('DesignationRel', 'DepartmentRel')->get()
            : $this->getStaffList();
        $results = [];
        foreach ($staffList as $staff) {
            $payrollSlip = $this->calculatePayrollForStaff($staff, $month, $year);
            $results[] = $payrollSlip;
        }

        return $results;
    }

    protected function getWeekendDaysForStaff(): array
    {
        return $this->campusWeeklyHolidayService->getWeekendDaysForTenant();
    }

    protected function calculatePayrollForStaff(Staff $staff, int $month, int $year): array
    {
        $gazettedLeaves = $this->getGazettedLeavesForMonth($month, $year);
        $weekendDays = $this->getWeekendDaysForStaff($staff);
        $workingDays = $this->calculateWorkingDays($month, $year, $gazettedLeaves, $weekendDays);
        $attendanceData = $this->calculateAttendance($staff->id, $month, $year, $gazettedLeaves, $weekendDays);
        $fineDeduction = $this->getFineDeduction($staff->id, $month, $year);
        $lateFineDeduction = $this->getLateFineDeduction($staff->id, $month, $year);
        $miscellaneousPayment = $this->getMiscellaneousPayment($staff->id, $month, $year);
        $salaryTax = $this->getSalaryTax($staff->id);
        $securityRefund = $this->getSecurityRefund($staff->id, $month, $year);
        $securityDeduction = $this->getSecurityDeduction($staff->id, $month, $year);

        $totalAllowances = $this->calculateTotalAllowances($staff);
        $grossSalary = $staff->BasicSalary + $totalAllowances;
        $dailySalary = $grossSalary / $workingDays;

        $absentDeduction = $dailySalary * $attendanceData['absent_days'];
        $gazettedLeaveDeduction = 0;

        $netSalary = $grossSalary
            - $absentDeduction
            - $fineDeduction
            - $lateFineDeduction
            - $salaryTax
            + $securityRefund
            - $securityDeduction
            + $miscellaneousPayment;

        return [
            'staff_id' => $staff->id,
            'staff_name' => $staff->FirstName.' '.$staff->LastName,
            'DesignationName' => $staff?->DesignationRel?->DesignationName,
            'DepartmentName' => $staff?->DepartmentRel?->DepartmentName,
            'payroll_month' => $month,
            'payroll_year' => $year,
            'basic_salary' => (float) $staff->BasicSalary,
            'transport_allowance' => (float) $staff->TransportAllowance,
            'computer_allowance' => (float) $staff->ComputerAllowance,
            'mobile_allowance' => (float) $staff->MobileAllowance,
            'recreation_allowance' => (float) $staff->RecreationAllowance,
            'total_allowances' => $totalAllowances,
            'gross_salary' => $grossSalary,
            'working_days' => $workingDays,
            'present_days' => $attendanceData['present_days'],
            'absent_days' => $attendanceData['absent_days'],
            'leave_days' => $attendanceData['leave_days'],
            'gazetted_leaves_count' => $attendanceData['gazetted_leaves_count'],
            'gazetted_leave_deduction' => 0,
            'total_absent_deduction' => round($absentDeduction, 2),
            'fine_deduction' => $fineDeduction,
            'late_fine_deduction' => $lateFineDeduction,
            'miscellaneous_payment' => $miscellaneousPayment,
            'salary_tax' => $salaryTax,
            'security_refund' => $securityRefund,
            'security_deduction' => $securityDeduction,
            'net_salary' => round($netSalary, 2),
            'applyed_leave_requests' => $attendanceData['applyed_leave_requests']->toArray(),

        ];
    }

    protected function calculateTotalAllowances(Staff $staff): float
    {
        $allowances = 0;
        $allowances += floatval($staff->TransportAllowance ?: 0);
        $allowances += floatval($staff->ComputerAllowance ?: 0);
        $allowances += floatval($staff->MobileAllowance ?: 0);
        $allowances += floatval($staff->RecreationAllowance ?: 0);

        return $allowances;
    }

    protected function getGazettedLeavesForMonth(int $month, int $year): Collection
    {
        return GazettedLeave::where('tenant_id', tenant('id'))
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->where('status', 1)
            ->pluck('date');
    }

    protected function calculateWorkingDays(int $month, int $year, Collection $gazettedLeaves, array $weekendDays = [0, 6]): int
    {
        $totalDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $workingDays = 0;

        $gazettedLeaveDays = $gazettedLeaves->map(fn ($d) => Carbon::parse($d)->format('Y-m-d'))->toArray();

        for ($d = 1; $d <= $totalDays; $d++) {
            $date = Carbon::create($year, $month, $d);
            $dayOfWeek = $date->dayOfWeek;

            if (in_array($dayOfWeek, $weekendDays)) {
                continue;
            }

            $dateString = $date->format('Y-m-d');
            if (in_array($dateString, $gazettedLeaveDays)) {
                continue;
            }

            $workingDays++;
        }

        return $workingDays;
    }

    protected function calculateAttendance(int $staffId, int $month, int $year, Collection $gazettedLeaves, array $weekendDays = [0, 6]): array
    {
        $totalDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $presentDays = 0;
        $absentDays = 0;
        $leaveDays = 0;
        $gazettedLeaveCount = 0;
        $gazettedLeaveDeduction = 0;

        $attendanceRecords = StaffAttendance::where('StaffId', $staffId)
            ->where('tenant_id', tenant('id'))
            ->whereMonth('AttendanceDate', $month)
            ->whereYear('AttendanceDate', $year)
            ->selectRaw('DATE(AttendanceDate) as date, Attendance')
            ->get()
            ->keyBy('date');

        $leaveRequests = LeaveRequest::where('staff_id', $staffId)
            ->where('tenant_id', tenant('id'))
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get(['date', 'status'])
            ->keyBy(function ($item) {
                return Carbon::parse($item->date)->format('Y-m-d');
            });

        $applyedleaveRequests = LeaveRequest::where('staff_id', $staffId)
            ->where('tenant_id', tenant('id'))
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->get();

        for ($d = 1; $d <= $totalDays; $d++) {
            $date = Carbon::create($year, $month, $d);
            $dayOfWeek = $date->dayOfWeek;
            $dateString = $date->format('Y-m-d');

            if (in_array($dayOfWeek, $weekendDays)) {
                continue;
            }

            $gazettedLeaveDaysArr = $gazettedLeaves->map(fn ($d) => Carbon::parse($d)->format('Y-m-d'))->toArray();
            if (in_array($dateString, $gazettedLeaveDaysArr)) {
                $gazettedLeaveCount++;

                continue;
            }

            $hasLeaveRequest = $leaveRequests->has($dateString);
            $leaveRequestRecord = $leaveRequests->get($dateString);
            $leaveStatus = $leaveRequestRecord->status ?? null;
            $leaveApproved = $hasLeaveRequest && $leaveStatus === 'approved';
            $leaveNotApproved = $hasLeaveRequest && $leaveStatus !== 'approved';

            if ($leaveApproved) {
                $leaveDays++;

                continue;
            }

            if ($leaveNotApproved) {
                $absentDays++;

                continue;
            }

            $attendance = $attendanceRecords->get($dateString);
            if ($attendance) {
                switch (strtolower($attendance->Attendance)) {
                    case 'present':
                        $presentDays++;
                        break;
                    case 'absent':
                        $absentDays++;
                        break;
                    case 'leave':
                        $leaveDays++;
                        break;
                }
            } else {
                $absentDays++;
            }
        }

        $gazettedLeaveDeduction = $gazettedLeaveCount;

        $absentDays = $this->checkSandwichLeaves($staffId, $month, $year, $absentDays, $gazettedLeaves, $weekendDays);

        return [
            'present_days' => $presentDays,
            'applyed_leave_requests' => $applyedleaveRequests,
            'absent_days' => $absentDays,
            'leave_days' => $leaveDays,
            'gazetted_leaves_count' => $gazettedLeaveCount,
            'gazetted_leave_deduction' => $gazettedLeaveDeduction,
        ];
    }

    protected function checkSandwichLeaves(int $staffId, int $month, int $year, int $absentDays, Collection $gazettedLeaves, array $weekendDays = [0, 6]): int
    {
        $absentDates = StaffAttendance::where('StaffId', $staffId)
            ->where('tenant_id', tenant('id'))
            ->whereMonth('AttendanceDate', $month)
            ->whereYear('AttendanceDate', $year)
            ->where('Attendance', 'Absent')
            ->pluck('AttendanceDate')
            ->map(fn ($date) => Carbon::parse($date)->day)
            ->toArray();
        $gazettedLeaveDays = $gazettedLeaves->map(fn ($gl) => Carbon::parse($gl)->day)->toArray();

        $allAttendanceDates = StaffAttendance::where('StaffId', $staffId)
            ->where('tenant_id', tenant('id'))
            ->whereMonth('AttendanceDate', $month)
            ->whereYear('AttendanceDate', $year)
            ->pluck('AttendanceDate')
            ->map(fn ($date) => Carbon::parse($date)->day)
            ->toArray();
        $sandwichDeduction = 0;

        foreach ($absentDates as $absentDay) {
            $date = Carbon::create($year, $month, $absentDay);
            $dayOfWeek = $date->dayOfWeek;

            $daysUntilNextWorkday = $this->getDaysUntilNextWorkday($date, $weekendDays);
            if ($daysUntilNextWorkday !== null) {
                $nextWorkday = $date->copy()->addDays($daysUntilNextWorkday);
                $nextWorkdayDay = $nextWorkday->day;

                if (! in_array($nextWorkday->dayOfWeek, $weekendDays) && $nextWorkday->dayOfWeek === 1) {
                    $nextWorkdayHasMissingAttendance = ! in_array($nextWorkdayDay, $allAttendanceDates);
                    if (! in_array($nextWorkdayDay, $gazettedLeaveDays) && ! in_array($nextWorkdayDay, $absentDates) && $nextWorkdayHasMissingAttendance) {
                        $sandwichDeduction++;
                    }
                }
            }
        }

        return $absentDays + $sandwichDeduction;
    }

    protected function getDaysUntilNextWorkday(Carbon $date, array $weekendDays): ?int
    {
        $currentDay = $date->dayOfWeek;
        $daysToCheck = [1, 2, 3];

        foreach ($daysToCheck as $daysAhead) {
            $nextDay = ($currentDay + $daysAhead) % 7;
            if (! in_array($nextDay, $weekendDays)) {
                return $daysAhead;
            }
        }

        return null;
    }

    protected function getFineDeduction(int $staffId, int $month, int $year): float
    {
        $fine = FineDeduction::where('staff_id', $staffId)
            ->where('apply_year', $year)
            ->where('applicable_month', $month)
            ->where('tenant_id', tenant('id'))
            ->whereNull('deleted_at')
            ->first();

        return $fine ? floatval($fine->amount) : 0;
    }

    protected function getLateFineDeduction(int $staffId, int $month, int $year): float
    {
        $fine = LateFine::where('staff_id', $staffId)
            ->where('apply_year', $year)
            ->where('applicable_month', $month)
            ->where('tenant_id', tenant('id'))
            ->whereNull('deleted_at')
            ->first();

        return $fine ? floatval($fine->amount) : 0;
    }

    protected function getMiscellaneousPayment(int $staffId, int $month, int $year): float
    {
        $payment = MiscellaneousPayment::where('staff_id', $staffId)
            ->where('apply_year', $year)
            ->where('applicable_month', $month)
            ->where('tenant_id', tenant('id'))
            ->whereNull('deleted_at')
            ->first();

        return $payment ? floatval($payment->amount) : 0;
    }

    protected function getSalaryTax(int $staffId): float
    {
        $tax = SalaryTax::where('staff_id', $staffId)
            ->where('tenant_id', tenant('id'))
            ->whereNull('deleted_at')
            ->first();

        return $tax ? floatval($tax->amount) : 0;
    }

    protected function getSecurityRefund(int $staffId, int $month, int $year): float
    {
        $refund = SecurityRefund::where('staff_id', $staffId)
            ->where('tenant_id', tenant('id'))
            ->where('applicable_month', $month)
            ->whereNull('deleted_at')
            ->first();

        // dd($refund,$month);
        return $refund ? floatval($refund->amount) : 0;
    }

    protected function getSecurityDeduction(int $staffId, int $month, int $year): float
    {
        $deduction = SecurityDeduction::where('staff_id', $staffId)
            ->where('tenant_id', tenant('id'))
            ->where('apply_year', $year)
            ->where(function ($query) use ($month) {
                $query->where('from_month', '<=', $month)
                    ->where('to_month', '>=', $month);
            })
            ->whereNull('deleted_at')
            ->first();

        return $deduction ? floatval($deduction->amount) : 0;
    }

    public function savePayrollSlips(array $payrollData): void
    {
        foreach ($payrollData as $data) {
            PayrollSlip::updateOrCreate(
                [
                    'tenant_id' => tenant('id'),
                    'staff_id' => $data['staff_id'],
                    'department_name' => $data['DepartmentName'],
                    'desigination_name' => $data['DesignationName'],
                    'payroll_month' => $data['payroll_month'],
                    'payroll_year' => $data['payroll_year'],
                ],
                array_merge($data, [
                    'CreatedBy' => auth()->id(),
                    'ModifiedBy' => auth()->id(),
                ])
            );

            userActivityLogs('Payroll Slip generated for staff ID: '.$data['staff_id'], HumanResourceLog::class);
        }
    }

    public function getPayrollSlipList($request): object
    {
        $query = PayrollSlip::with('staff')
            ->where('tenant_id', tenant('id'));

        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        if ($request->filled('month')) {
            $query->where('payroll_month', $request->month);
        }

        if ($request->filled('year')) {
            $query->where('payroll_year', $request->year);
        }

        return $query->orderBy('id', 'desc')
            ->paginate(25)
            ->withQueryString();
    }

    public function getPayrollSlipDetail(int $id): PayrollSlip
    {
        return PayrollSlip::with('staff')
            ->where('tenant_id', tenant('id'))
            ->findOrFail($id);
    }
}
