<?php

namespace App\Http\Controllers\Staff;
use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\AssignClassTeacherRequest;
use App\Models\AssignClassTeacher;
use App\Services\ClassteacherAssignService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AssignClassTeacherController extends Controller
{
    protected $classteacherAssignService;

    public function __construct(ClassteacherAssignService $classteacherAssignService)
    {
        $this->classteacherAssignService = $classteacherAssignService;
    }

    public function index(): Response
    {
        $assignClassTeacher = AssignClassTeacher::select('id','tenant_id','ClassId','SectionId','StaffId')
        ->Tenant()
        ->with('ClassRel','SectionRel')
        ->with(['StaffRel' => function($q){
            $q->where('IsActive',1)->where('tenant_id',tenant('id'));
        }])
        ->orderBy('id','desc')
        ->paginate(25)->withQueryString();
        return Inertia::render('Staff/AssignClassTeacher/List',[
            'assignClassTeacher' =>$assignClassTeacher
        ]);
    }

    public function create(): Response
    {
        $data = $this->classteacherAssignService->create();
        return Inertia::render('Staff/AssignClassTeacher/Create', $data);
    }

    public function submit(AssignClassTeacherRequest $request): RedirectResponse
    {
        $this->classteacherAssignService->submit($request);
        return $this->redirectSuccess('Assignn teacher to class created successfully!', 'assign.class.teacher.list');
    }
   
    public function edit(Request $request): Response
    {
        $data = $this->classteacherAssignService->create();
        $assignClassTeacher = AssignClassTeacher::select('id','tenant_id','ClassId','SectionId','StaffId')
        ->where('id',$request->id)
        ->first();
        $data['assignClassTeacher'] = $assignClassTeacher;
        return Inertia::render('Staff/AssignClassTeacher/Edit', $data);
    }
   
    public function update(AssignClassTeacherRequest $request): RedirectResponse
    {   
        $this->classteacherAssignService->update($request);
        return $this->redirectSuccess('Assignn teacher to class updated successfully!', 'assign.class.teacher.list');
    }
}
