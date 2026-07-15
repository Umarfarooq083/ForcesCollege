<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\StudentRequest;
use App\Models\Classes;
use App\Models\DisableReason;
use App\Models\GuardianRelation;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentInquiry;
use App\Services\StudentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class StudentController extends Controller
{
    protected $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    public function index(Request $request): Response
    {
        $students = $this->studentService->index($request);
        $disableReasons = DisableReason::select('id', 'DisableReasonName')->get();

        return Inertia::render('Student/List', [
            'students' => $students,
            'disable_reasons' => $disableReasons,
        ]);
    }

    public function disablelist(): Response
    {
        $disablestudents = $this->studentService->disablelist();

        return Inertia::render('Student/DisableList', [
            'disablestudents' => $disablestudents,
        ]);
    }

    public function create(Request $request): Response
    {
        $inquiry = StudentInquiry::with('guardian', 'guardianRelation', 'class.sections', 'inqSession')->findOrFail($request->id);
        $guardianInfo = $inquiry?->guardian;
        $classes = $inquiry?->class;
        $campusSession = $inquiry?->inqSession;
        $guardianRelation = $inquiry?->guardianRelation;

        return Inertia::render('Student/Create', [
            'classesList' => $classes,
            'guardianInfo' => $guardianInfo,
            'guardianrelation' => $guardianRelation,
            'campusSession' => $campusSession,
            'inquiry' => $inquiry,
        ]);
    }

    // StudentRequest
    public function submit(StudentRequest $request): RedirectResponse
    {
        $this->studentService->submit($request);

        return $this->redirectSuccess('Admission inquiry created successfully!', 'student.index');
    }

    public function toggleStatus(Request $request, int $id): RedirectResponse
    {

        $request->validate([
            'FromDate' => ['nullable', 'date', 'before_or_equal:ToDate'],
            'Reason' => ['required', 'max:255'],
        ]);

        $this->studentService->toggleStatus($request, $id);

        return $this->redirectSuccess('Student status updated successfully!', 'student.index');
    }

    public function withdrawSubmit(Request $request, int $id): RedirectResponse
    {

        $request->validate([
            'FromDate' => ['required', 'date'],
            'Reason' => ['required', 'max:255'],
        ]);

        $this->studentService->withdrawSubmit($request, $id);

        return $this->redirectSuccess('Student status updated successfully!', 'student.index');
    }

    public function edit(Request $request): Response
    {
        $classList = $this->campusClasses();
        $sections = Section::where('tenant_id', tenant('id'))->get();
        $student = $this->studentService->detail($request);
        $guardianrelation = GuardianRelation::get();

        return Inertia::render('Student/Edit', [
            'student' => $student,
            'sections' => $sections,
            'guardianrelation' => $guardianrelation,
            'classList' => $classList,
        ]);
    }

    public function detail(Request $request): Response
    {
        $student = $this->studentService->detail($request);
        $guardianrelation = GuardianRelation::find($student->GuardianRelation);
        $disableReasons = DisableReason::select('id', 'DisableReasonName')->get();

        return Inertia::render('Student/Detail', [
            'student' => $student,
            'guardianrelation' => ($guardianrelation) ? $guardianrelation->relationName : '',
        ]);
    }

    public function withdraw(Request $request): Response
    {
        $data = $this->studentService->withdraw($request);
        $disableReasons = DisableReason::select('id', 'DisableReasonName')->get();

        return Inertia::render('Student/Withdraw', [
            'student' => $data['student'],
            'lastChallan' => $data['lastChallan'],
            'disable_reasons' => $disableReasons,
        ]);
    }

    public function withdrawList(): Response
    {
        $withdrawingStudents = $this->studentService->getWithdrawList();

        return Inertia::render('Student/WithdrawList', [
            'withdrawingStudents' => $withdrawingStudents,
        ]);
    }

    public function approveWithdraw(Request $request, int $id): RedirectResponse
    {
        $this->studentService->approveWithdraw($request, $id);

        return $this->redirectSuccess('Withdraw request approved successfully!', 'student.withdrawlist');
    }

    public function rejectWithdraw(Request $request, int $id): RedirectResponse
    {
        $this->studentService->rejectWithdraw($request, $id);

        return $this->redirectSuccess('Withdraw request rejected successfully!', 'student.withdrawlist');
    }

    public function readmissionList(): Response
    {
        $readmissionStudents = $this->studentService->getReAdmissionList();

        return Inertia::render('Student/ReAdmissionList', [
            'readmissionStudents' => $readmissionStudents,
        ]);
    }

    public function readmission(Request $request): Response
    {
        $data = $this->studentService->readmission($request);

        return Inertia::render('Student/ReAdmission', [
            'student' => $data['student'],
        ]);
    }

    public function readmissionSubmit(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'readmitted_date' => ['required', 'date'],
        ]);

        $this->studentService->submitReAdmission($request, $id);

        return $this->redirectSuccess('Student readmitted successfully!', 'student.readmissionlist');
    }

    public function update(StudentRequest $request): RedirectResponse
    {
        $this->studentService->update($request);

        return $this->redirectSuccess('Student updated successfully.', 'student.index');
    }

    public function card(Request $request): Response
    {

        $student = Student::where('tenant_id', tenant('id'))->where('id', $request->id)
                            // ->where('IsDisable', 0)
            ->with('class:id,tenant_id,ClassName', 'section:id,tenant_id,SectionName')->first();

        return Inertia::render('Student/Card', ['student' => $student]);
    }

    public function cardPdf(Request $request)
    {
        $student = Student::with(['class', 'section'])->findOrFail($request->id);
        $pdf = Pdf::loadView('reports.print-student-card', compact('student'))
            ->setPaper('a4', 'portrait')
            ->setOption([
                'defaultFont' => 'Arial',
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'dpi' => 96,
            ]);

        return $pdf->download($student->FirstName.'_'.$student->LastName.'_Card.pdf');
    }

    public function campusClasses(): Collection|Classes
    {
        return Classes::select('id', 'tenant_id', 'ClassName')
            ->where('IsActive', 1)
            ->where('tenant_id', tenant('id'))
            ->get();
    }
}
