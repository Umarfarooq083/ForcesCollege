<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\ClassTimeTableRequest;
use App\Models\Classes;
use App\Services\ClassTimeTableService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ClassTimeTableController extends Controller
{

    protected $timetablesService;

    public function __construct(ClassTimeTableService $timetablesService)
    {
        $this->timetablesService = $timetablesService;
    }

    public function index(Request $request): Response
    {
        $data['timetables'] = $this->timetablesService->index($request);
        $data['staffList'] = $this->timetablesService->getStaffList();
        return Inertia::render('Staff/ClassTimeTable/List', $data);
    }

    public function create(): Response
    {
        $staffList = $this->timetablesService->getStaffList();
        $classesList = Classes::select('id', 'tenant_id', 'ClassName')->where('tenant_id', tenant('id'))->where('IsActive', 1)->get();
        return Inertia::render('Staff/ClassTimeTable/Create', [
            'staffList' => $staffList,
            'classesList' => $classesList,
        ]);
    }

    public function getSectionsAndSubjects(int $classId)
    {
        return response()->json(
            $this->timetablesService->getSectionsAndSubjects($classId)
        );
    }
    
    public function submit(ClassTimeTableRequest $request)
    {
        // dd($request->validated());
        try {
            $this->timetablesService->createTimetableGroup($request);
            return $this->redirectSuccess('Timetable created successfully!', 'classtimetable.index');
        } 
        catch (\Throwable $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    
}