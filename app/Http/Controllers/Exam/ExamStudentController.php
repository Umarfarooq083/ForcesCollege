<?php

namespace App\Http\Controllers\Exam;

use App\Http\Controllers\Controller;
use App\Http\Requests\Exam\ExamStudentRequest;
use App\Models\ExamLog;
use App\Models\ExamStudent;
use App\Services\Exam\ExamStudentService;
use Illuminate\Http\Request;
use Inertia\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class ExamStudentController extends Controller
{
    protected $examStudentService;
    public function __construct(ExamStudentService $examStudentService)
    {
        return $this->examStudentService = $examStudentService;
    }

    public function index(Request $request): Response
    {
        $data = $this->examStudentService->index($request);
        return inertia('Exam/ExamStudent/List', $data);
    }

    public function create(): Response
    {
        $data = $this->examStudentService->create();
        return inertia('Exam/ExamStudent/Create', $data);
    }

    public function submit(ExamStudentRequest $request): RedirectResponse
    {
        $this->examStudentService->submit($request);
        return $this->redirectSuccess('Exam student created successfully.', 'examstudent.index');
    }

    public function getSubjects(Request $request): JsonResponse
    {
        $subjects = $this->examStudentService->getSubjectsByClass($request);
        return response()->json($subjects);
    }

    public function delete(Request $request)
    {
        $deleted = ExamStudent::where('tenant_id',tenant('id'))->findorFail($request->id)->delete();   
        if($deleted){
            userActivityLogs('Fee Type Deleted id is '.$request->id.' and User ID: '.auth()->user()->id.'', ExamLog::class);
        }
        return $this->redirectSuccess('Exam Student deleted successfully!', 'examstudent.index');


    }
}
