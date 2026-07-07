<?php

namespace App\Http\Controllers\Reports;

use App\Exports\SchoolFeeLedgerExport;
use App\Exports\StudentReportExport;
use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\ExamSubject;
use App\Models\ExamTerm;
use App\Models\ExamType;
use App\Models\GuardianInfo;
use App\Models\LmsSession;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentInquiry;
use App\Models\Subject;
use App\Models\Tenant;
use App\Services\Reports\MaterReportsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;



class MasterReportController extends Controller
{

    private $tenant_data;
    protected $MaterReportsService;
    public function __construct(MaterReportsService $MaterReportsService)
    {
        $this->MaterReportsService = $MaterReportsService;
        $this->tenant_data = Tenant::where('id', tenant('id'))->first();
    }

    public function reportList()
    {
        $campusClassList = campusClassList();
        $classes = Classes::whereIn('class_type_id', $campusClassList)->orderBy('ClassOrder', 'asc')->get();
        $sections = Section::where('tenant_id', tenant('id'))->get();
        $student = Student::where('tenant_id', tenant('id'))->where('IsDisable', 0)->where('IsActive', 1)->get();
        $examTermData = ExamTerm::where('IsActive', 1)->get();
        $lmsSessionData = LmsSession::get();
        $data['classes'] = $classes;
        $data['sections'] = $sections;
        $data['student'] = $student;
        $data['examTermData'] = $examTermData;
        $data['lmsSessionData'] = $lmsSessionData;
        return Inertia::render('Reports/MasterReportList', $data);
    }

    public function studentDetailReport(Request $request)
    {
        return Excel::download(
            new StudentReportExport($request->ClassId, $request->SectionId, $request->gender, $request->rollno),
            'student-report.xlsx'
        );
    }

    public function studentAdmissionReport(Request $request)
    {
        $data = $this->MaterReportsService->studentAdmissionReport($request);
        $pdf = PDF::loadView('reports.student_admission', $data)
            ->setPaper('a4', 'landscape');
        return $pdf->download('student-admission.pdf');
    }

    public function studentAdmissionInquiryReport(Request $request)
    {
        $request->validate([
            'ClassId' => 'required',
        ]);

        ini_set('memory_limit', '512M');
        $data = $this->MaterReportsService->studentAdmissionInquiryReport($request);
        $pdf = PDF::loadView('reports.student_admission_inquiry', $data)
            ->setPaper('a4', 'landscape');
        return $pdf->download('student-admission-inquiry.pdf');
    }

    public function studentUnPaidFee(Request $request)
    {
        ini_set('memory_limit', '512M');
        $data = $this->MaterReportsService->studentUnPaidFee($request);
        $pdf = PDF::loadView('reports.student_unpaid_fee', $data)
            ->setPaper('a4', 'landscape');
        return $pdf->download('student-unpaid-fee.pdf');
    }

    public function studentSummaryReport(Request $request)
    {
        ini_set('memory_limit', '512M');
        $data = $this->MaterReportsService->studentSummaryReport($request);
        $pdf = PDF::loadView('reports.student_summary_report', $data)
            ->setPaper('a4', 'portrait');
        return $pdf->download('student-summary-report.pdf');
    }


    public function contentFeedbackReport(Request $request)
    {
        ini_set('memory_limit', '512M');
        $data = $this->MaterReportsService->contentFeedbackReport();
        $pdf = PDF::loadView('reports.content_feedback_report', $data)
            ->setPaper('a4', 'portrait');
        return $pdf->download('student-summary-report.pdf');
    }

    public function studentLedger(Request $request)
    {
        $request->validate([
            'student_id' => 'required',
        ]);

        ini_set('memory_limit', '512M');
        $data = $this->MaterReportsService->studentLedger($request);
        $pdf = PDF::loadView('reports.student_ledger_report', $data)
            ->setPaper('a4', 'landscape');
        return $pdf->download('student-ledger-report.pdf');
    }

    public function employeeReport(Request $request)
    {
        ini_set('memory_limit', '512M');
        $data = $this->MaterReportsService->employeeReport($request);
        $pdf = PDF::loadView('reports.employee_report', $data)
            ->setPaper('a4', 'landscape');
        return $pdf->download('employee-report.pdf');
    }

    public function assesmentReportExam(Request $request)
    {
        $tenant_id = tenant('id');
        if (session('switched_tenant_id')) {
            $tenant_id = session('switched_tenant_id');
        }
        $ExamTypeData = ExamType::where('tenant_id', $tenant_id)
            ->where('ExamTermId', $request->exam_term_id)
            ->where('SessionId', $request->session_id)
            ->get();
        return $ExamTypeData->toArray();
    }

    public function assesmentExamClass(Request $request)
    {
        $tenant_id = tenant('id');
        if (session('switched_tenant_id')) {
            $tenant_id = session('switched_tenant_id');
        }
        // $classData = [];
        $ExamSubjectData = ExamSubject::where('tenant_id', $tenant_id)
            ->where('ExamId', $request->exam_id)
            ->where('SessionId', $request->session_id)
            ->pluck('SubjectId')
            ->unique()
            ->values();
        if (count($ExamSubjectData) > 0) {
            $SubjectData = Subject::where('tenant_id', $tenant_id)
                ->whereIn('id', $ExamSubjectData)
                ->where('IsActive', 1)
                ->pluck('ClassId')
                ->unique()
                ->values();

            if (count($SubjectData) > 0) {
                $classData = Classes::whereIn('id', $SubjectData)
                    ->where('IsActive', 1)
                    ->get();
            }
        }
        return $classData;
    }

    public function assesmentReportFetch(Request $request)
    {
        $request->validate([
            'ClassId' => 'required',
        ]);
        ini_set('memory_limit', '512M');
        $data = $this->MaterReportsService->assesmentReportFetch($request);
        $pdf = PDF::loadView('reports.assesment_wise_report', $data)
            ->setPaper('a4', 'landscape');
        return $pdf->download('assesment-wise-report.pdf');
    }

    public function termWiseFetch(Request $request)
    {

        ini_set('memory_limit', '512M');
        $data = $this->MaterReportsService->termWiseFetch($request);
        $pdf = PDF::loadView('reports.term_wise_report', $data)
            ->setPaper('a4', 'landscape');
        return $pdf->download('term-wise-report.pdf');
    }

    public function parentProfessionReport(Request $request)
    {

        ini_set('memory_limit', '512M');
        $data = $this->MaterReportsService->parentProfessionReport($request);
        $pdf = PDF::loadView('reports.parent_profission_report', $data)
            ->setPaper('a4', 'landscape');
        return $pdf->download('parent-profission-report.pdf');
    }

    public function studentAttendanceFetch(Request $request)
    {
        ini_set('memory_limit', '512M');
        $html = $this->MaterReportsService->studentAttendanceFetch($request);
        return response()->json([
            'success' => true,
            'studentAtt' => $html
        ]);
    }

    public function staffAttendanceReport(Request $request)
    {
        ini_set('memory_limit', '512M');
        $html = $this->MaterReportsService->staffAttendanceReport($request);
        return response()->json([
            'success' => true,
            'staffAtt' => $html
        ]);
    }

    public function feeSummaryHeadWiseReport(Request $request)
    {
        ini_set('memory_limit', '512M');
        $data = $this->MaterReportsService->feeSummaryHeadWiseReport($request);
        $pdf = PDF::loadView('reports.fee_summary_head_wise_report', $data)
            ->setPaper('a4', 'landscape');
        return $pdf->download('fee-summary-head-wise-report.pdf');
    }

    public function studentInformation()
    {
        $campusClassList = campusClassList();
        $classes = Classes::whereIn('class_type_id', $campusClassList)->get();
        $sections = Section::where('tenant_id', tenant('id'))->get();
        return Inertia::render('Reports/StudentInformationReport', [
            'classes' => $classes,
            'sections' => $sections,
        ]);
    }

    public function studentInformationFetch(Request $request)
    {
        $data = $this->MaterReportsService->studentInformationFetch($request);
        $pdf = PDF::loadView('reports.student_information_report', $data)
            ->setPaper('a4', 'landscape');
        return $pdf->download('student-information-report.pdf');
    }

    public function feeCollectionReport(Request $request)
    {
        return Inertia::render('Reports/FeeCollectionReport');
    }
    public function feeCollectionReportSummary(Request $request)
    {
        return Inertia::render('Reports/FeeCollectionReportSummary');
    }

    public function dailyFeeCollection(Request $request)
    {
        return Inertia::render('Reports/DailyFeeCollection');
    }

    public function feeCollectionReportFetch(Request $request)
    {
        $data = $this->MaterReportsService->feeCollectionReportFetch($request);
        return view('reports.student_fee_collection', $data);
    }


    public function studentFeebalanceFetch()
    {
        $campusClassList = campusClassList();
        $classes = Classes::whereIn('class_type_id', $campusClassList)->get();
        $sections = Section::where('tenant_id', tenant('id'))->get();
        $data['classes'] = $classes;
        $data['sections'] = $sections;
        return Inertia::render('Reports/FeeBalanceReport', $data);
    }

    public function feeCollectionSummaryFetch(Request $request)
    {
        $data = $this->MaterReportsService->feeCollectionReportSummary($request);
        return view('reports.student_fee_collection_summary', $data);
    }

    public function dailyFeeCollectionFetch(Request $request)
    {
        $data = $this->MaterReportsService->dailyFeeCollection($request);
        return view('reports.daily_fee_collection', $data);
    }

    public function studentFeebalance(Request $request)
    {
        ini_set('memory_limit', '512M');
        $data = $this->MaterReportsService->studentFeebalance($request);
        $pdf = Pdf::loadView('reports.student_belance', $data)
            ->setPaper('a4', 'landscape');
        return $pdf->download('student-belance.pdf');
    }


    public function studentSiblingReport(Request $request)
    {
        return Inertia::render('Reports/SiblingReport');
    }

    public function FetchAllSiblingStudents()
    {
        $tenantId = session('switched_tenant_id') ?: tenant('id');

        // $guardiansWithMultipleStudents = GuardianInfo::where('tenant_id', $tenantId)
        //     ->with(['studentInquiries' => function ($query) use ($tenantId) {
        //         $query->where('tenant_id', $tenantId)
        //             ->with(['class']);
        //     }])
        //     ->withCount(['studentInquiries as inquiry_count'])
        //     ->get()
        //     ->map(function ($guardian) use ($tenantId) {
        //         $students = Student::where('tenant_id', $tenantId)
        //             ->where('FatherCnic', $guardian->cnic)
        //             ->where('IsActive', 1)                
        //             ->with(['class', 'section'])
        //             ->get();

        //         $allStudents = collect();

        //         foreach ($guardian->studentInquiries as $inquiry) {
        //             $allStudents->push([
        //                 'id' => $inquiry->id,
        //                 'FirstName' => $inquiry->Name,
        //                 'LastName' => $inquiry->LastName,
        //                 'FatherName' => $inquiry->FatherName,
        //                 'Gender' => $inquiry->Gender,
        //                 'RollNumber' => $inquiry->RollNumber ?? null,
        //                 'EnquiryRollNumber' => $inquiry->RollNumber ?? null,
        //                 'class' => $inquiry->class ? [
        //                     'ClassName' => $inquiry->class->ClassName
        //                 ] : null,
        //                 'section' => null,
        //             ]);
        //         }

        //         foreach ($students as $student) {
        //             $allStudents->push([
        //                 'id' => $student->id,
        //                 'FirstName' => $student->FirstName,
        //                 'LastName' => $student->LastName,
        //                 'FatherName' => $student->FatherName,
        //                 'Gender' => $student->Gender,
        //                 'RollNumber' => $student->RollNumber,
        //                 'EnquiryRollNumber' => null,
        //                 'class' => $student->class ? [
        //                     'ClassName' => $student->class->ClassName
        //                 ] : null,
        //                 'section' => $student->section ? [
        //                     'SectionName' => $student->section->SectionName
        //                 ] : null,
        //             ]);
        //         }

        //         $guardian->all_students = $allStudents->values();
        //         $guardian->total_student_count = $allStudents->count();

        //         return $guardian;
        //     })
        //     ->filter(function ($guardian) {
        //         return $guardian->total_student_count > 1;
        //     })
        //     ->values()
        //     ->map(function ($guardian) {
        //         return [
        //             'guardian' => [
        //                 'name' => $guardian->name,
        //                 'cnic' => $guardian->cnic,
        //             ],
        //             'students' => $guardian->all_students,
        //         ];
        //     });


            $siblingCnics = Student::select('FatherCnic')
                ->where('tenant_id', $tenantId)
                ->whereNotNull('FatherCnic')
                ->where('FatherCnic', '!=', '')
                ->where('IsActive', 1)
                ->groupBy('FatherCnic')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('FatherCnic');

            $siblings = Student::select(
                'id',
                'tenant_id',
                'RollNumber',
                'IsActive',
                'FirstName',
                'LastName',
                'Gender',
                'DateOfBirth',
                'FatherCnic',
                'MotherName',
                'FatherName',   
                'ClassId',
                'SectionId'
            )
             ->with([
                'class:id,ClassName',
                'section:id,SectionName'
            ])
            ->whereIn('FatherCnic', $siblingCnics)
            ->where('tenant_id', $tenantId)
            ->where('IsActive', 1)
            ->get()
            ->groupBy('FatherCnic')
            ->map(function ($students, $cnic) {

                $firstStudent = $students->first();

                return [
                    'guardian' => [
                        'name' => $firstStudent->FatherName,
                        'cnic' => $cnic,
                    ],
                    'students' => $students->values()->toArray(),
                ];
            })
            ->values();

        // dd($siblings->toArray());

        return response()->json([
            'siblings' => $siblings
        ]);
    }

    public function FetchAllSiblingStudentsPdf()
    {
        $tenantId = session('switched_tenant_id') ?: tenant('id');
        $siblingCnics = Student::select('FatherCnic')
                ->where('tenant_id', $tenantId)
                ->whereNotNull('FatherCnic')
                ->where('FatherCnic', '!=', '')
                ->where('IsActive', 1)
                ->groupBy('FatherCnic')
                ->havingRaw('COUNT(*) > 1')
                ->pluck('FatherCnic');

            $siblings = Student::select(
                'id',
                'tenant_id',
                'RollNumber',
                'IsActive',
                'FirstName',
                'LastName',
                'Gender',
                'DateOfBirth',
                'FatherCnic',
                'MotherName',
                'FatherName',   
                'ClassId',
                'SectionId'
            )
             ->with([
                'class:id,ClassName',
                'section:id,SectionName'
            ])
            ->whereIn('FatherCnic', $siblingCnics)
            ->where('tenant_id', $tenantId)
            ->where('IsActive', 1)
            ->get()
            ->groupBy('FatherCnic')
            ->map(function ($students, $cnic) {

                $firstStudent = $students->first();

                return [
                    'guardian' => [
                        'name' => $firstStudent->FatherName,
                        'cnic' => $cnic,
                    ],
                    'students' => $students->values()->toArray(),
                ];
            })
            ->values();

        $data['siblings'] = $siblings;
        $data['meta'] = [
            'campus' => $this->tenant_data->domain,
        ];

        ini_set('memory_limit', '512M');
        $pdf = Pdf::loadView('reports.student_sibling_report_all', $data)
            ->setPaper('a4', 'portrait');
        return $pdf->stream('student-sibling-report-all.pdf');
    }

    public function schoolFeeLedgerReport(Request $request)
    {
        $validated = $request->validate([
            'ClassId'   => 'required|integer',
            'SectionId' => 'nullable|integer',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:from_date',
        ]);

        $data = $this->MaterReportsService->schoolFeeLedgerReport($validated);
        return Excel::download(
            new SchoolFeeLedgerExport($data['months'], $data['data']),
            'school-fee-ledger-report.xlsx'
        );
    }

    public function studentWithdrawReport(): Response
    {
        $campusClassList = campusClassList();
        $classes = Classes::whereIn('class_type_id', $campusClassList)->orderBy('ClassOrder', 'asc')->get();
        $sections = Section::where('tenant_id', tenant('id'))->get();
        return Inertia::render('Reports/StudentWithdrawReport', [
            'classes' => $classes,
            'sections' => $sections,
        ]);
    }

    public function studentWithdrawReportFetch(Request $request): Response
    {
        $request->validate([
            'ClassId' => 'required|integer',
        ]);

        ini_set('memory_limit', '512M');
        $data = $this->MaterReportsService->studentWithdrawReportFetch($request);
        $pdf = PDF::loadView('reports.student_withdraw_report', $data)
            ->setPaper('a4', 'landscape');
        return $pdf->download('student-withdraw-report.pdf');
    }
}
