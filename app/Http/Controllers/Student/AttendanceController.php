<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;

use App\Http\Requests\Student\AttendanceRequest;
use App\Services\StudentAttendanceService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Inertia\Inertia;

class AttendanceController extends Controller
{
    protected $attendanceService;

    public function __construct(StudentAttendanceService $attendanceService)
    {
        $this->attendanceService = $attendanceService;
    }

    public function attendanceList(Request $request): Response
    {
        $data = $this->attendanceService->attendanceList($request);
        return Inertia::render('Attendance/Create', $data);
    }

    public function submitAttendance(AttendanceRequest $request): RedirectResponse
    {  
        $this->attendanceService->submitStudentAttendance($request);
        return $this->redirectSuccess('Attendance saved successfully.', 'attendance.create');
    }  

}
