<?php

namespace App\Http\Controllers\Exam;

use App\Http\Controllers\Controller;
use App\Http\Requests\Exam\ExamSubjectRequest;
use App\Models\ExamLog;
use App\Models\ExamSubject;
use App\Services\Exam\ExamSubjectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ExamSubjectController extends Controller
{
    private $examSubjectService;

    public function __construct(ExamSubjectService $examSubjectService)
    {
        $this->examSubjectService = $examSubjectService;
    }

    public function index(Request $request): Response
    {
        $data = $this->examSubjectService->index($request);
        return Inertia::render('Exam/ExamSubject/List', $data);
    }

    public function create(): Response
    {
        $data = $this->examSubjectService->create();
        return Inertia::render('Exam/ExamSubject/Create', $data);
    }

    public function submit(ExamSubjectRequest $request): RedirectResponse
    {
        $this->examSubjectService->submit($request);
        userActivityLogs('Exam Subject Created and User ID: '.auth()->user()->id.'', ExamLog::class);
        return $this->redirectSuccess('Exam subject created successfully .', 'examsubject.index');
    }

    public function edit(Request $request): Response
    {
        $data = $this->examSubjectService->create();
        $data['examSubject'] = ExamSubject::where('tenant_id', tenant('id'))
            ->where('id', $request->id)
            ->first();
        return Inertia::render('Exam/ExamSubject/Edit', $data);
    }

    public function update(ExamSubjectRequest $request): RedirectResponse
    {
        $this->examSubjectService->update($request);
        userActivityLogs('Exam Subject Updated and id is '.$request->id.' User ID: '.auth()->user()->id.'', ExamLog::class);
        return $this->redirectSuccess('Exam subject updated successfully .', 'examsubject.index');
    }

    public function delete(ExamSubjectRequest $request)
    {
        try {
            $this->examSubjectService->delete($request->validated());
            userActivityLogs('Exam Subject deleted and id is '.$request->id.' by '. auth()->user()->id, ExamLog::class);
            return $this->redirectSuccess('Exam subject deleted successfully.', 'examsubject.index');
        } catch (ValidationException $e) {
            return $this->redirectError('Please first of all delete related Exam Students and Exam Marks.', 'examsubject.index');
        }
    }
}
