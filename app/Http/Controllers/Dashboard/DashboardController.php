<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\Dashboard\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\Inertia;

class DashboardController extends Controller
{
    protected $dashboardService;
    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function getDashboardData(): Response
    {
        $data['ClassesStudents'] = $this->dashboardService->getClassesStudents();
        $data['FeesData'] = $this->dashboardService->getChallanData();
        $data['TeacherAttendance'] = $this->dashboardService->teacherAttendance();
        $data['StudentsInquireis'] = $this->dashboardService->studentsInquiries();
        return Inertia::render('Dashboard', $data);
        
    }

    public function getFeesData(Request $request): JsonResponse
    {
        $year = null;
        $month = null;

        if ($request->has('month')) {
            [$year, $month] = explode('-', $request->month);
        }

        return response()->json(
            $this->dashboardService->getChallanData($year, $month)
        );
    }

    public function getStaffAttendanceFilterData(Request $request): JsonResponse
    {
        return response()->json(
            $this->dashboardService->teacherAttendance($request->date)
        );
    }

    public function getStaffAttendanceTableFilter(Request $request): JsonResponse
    {
        return response()->json(
            $this->dashboardService->attendanceTableFilter($request->date, $request->status)
        );
    }

    public function getStudentAttendanceFilterData(Request $request): JsonResponse
    {
        return response()->json(
            $this->dashboardService->getClassesStudents($request->date)
        );
    }

    public function getStudentInquiryFilterData(Request $request): JsonResponse
    {
        return response()->json(
            $this->dashboardService->studentsInquiries($request->year)
        );
    }
}
