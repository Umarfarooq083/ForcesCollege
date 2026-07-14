<?php

namespace App\Http\Controllers\Exam;

use App\Http\Controllers\Controller;
use App\Http\Requests\Exam\MarksRequest;
use App\Models\ExamLog;
use App\Models\ExamMarks;
use App\Services\Exam\MarksService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;

class MarksController extends Controller
{
    protected $marksService;

    public function __construct(MarksService $marksService)
    {
        return $this->marksService = $marksService;
    }

    public function index(Request $request): Response
    {
        $data = $this->marksService->index($request);

        return inertia('Exam/ExamMarks/List', $data);
    }

    public function create(): Response
    {
        $data = $this->marksService->create();

        return inertia('Exam/ExamMarks/Create', $data);
    }

    public function submit(MarksRequest $request): RedirectResponse
    {
        $this->marksService->submit($request->validated(), $request);
        userActivityLogs('Exam Marks Created and User ID: '.auth()->user()->id.'', ExamLog::class);

        return $this->redirectSuccess('Exam Marks submitted successfully.', 'marks.index');
    }

    public function show(Request $request): Response
    {
        $data = $this->marksService->show($request->all());

        return inertia('Exam/ExamMarks/Show', $data);
    }

    public function edit(Request $request): Response
    {
        $data = $this->marksService->getEditData($request->input('ClassId'), $request->input('id'));

        return inertia('Exam/ExamMarks/Edit', $data);
    }

    public function update(MarksRequest $request): RedirectResponse
    {
        $this->marksService->update($request->validated());
        userActivityLogs('Exam Marks Updated and id is '.$request->id.' User ID: '.auth()->user()->id.'', ExamLog::class);

        return redirect()->route('marks.index');
    }

    public function getMarksData(MarksRequest $request): JsonResponse
    {
        $data = $this->marksService->getMarksData($request);

        return response()->json($data);
    }

    public function delete(Request $request): RedirectResponse
    {
        $examMarks = ExamMarks::where('tenant_id', tenant('id'))
            ->findOrFail($request->id);
        $examMarks->ExamMarksDetails()->delete();
        $deleted = $examMarks->delete();

        if ($deleted) {
            userActivityLogs('Exam Marks Deleted and id is '.$request->id.' and User ID: '.auth()->user()->id.'', ExamLog::class);
        }

        return $this->redirectSuccess('Exam Marks deleted successfully!', 'marks.index');
    }
}
