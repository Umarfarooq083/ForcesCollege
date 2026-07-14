<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\AttendanceRequest;
use App\Services\StaffAttendanceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AttendanceController extends Controller
{
    protected $staffAttendanceService;

    public function __construct(StaffAttendanceService $staffAttendanceService)
    {
        $this->staffAttendanceService = $staffAttendanceService;
    }

    public function staffAttendanceList(Request $request): Response
    {
        $data = $this->staffAttendanceService->staffAttendanceList($request);
        $data['officeStartTime'] = $this->staffAttendanceService->getOfficeStartTime();

        return Inertia::render('Staff/Attendance', $data);
    }

    public function submitStaffAttendance(AttendanceRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $this->staffAttendanceService->submitStaffAttendance($request);

        return $this->redirectSuccess('Staff attendance saved successfully.', 'staff.attendance.list');
    }
}
