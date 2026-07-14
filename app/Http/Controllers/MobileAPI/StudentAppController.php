<?php

namespace App\Http\Controllers\MobileAPI;

use App\Http\Controllers\Controller;
use App\Models\ClassTimeTable;
use App\Models\ExamGrade;
use App\Models\ExamMarks;
use App\Models\ExamMarksDetail;
use App\Models\ExamSubject;
use App\Models\ExamType;
use App\Models\FcmToken;
use App\Models\GenerateFeeChallan;
use App\Models\HomeWork;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StudentAppController extends Controller
{
    public function Login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user' => 'required',
            'password' => 'required',
            'campus' => 'required',
            'fcm_token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $tenant = MobileTenantVerifaction($request);
        if (! $tenant) {
            return $this->apiErrorResponse('Student or Campus Not Found', 404);
        }

        $student = Student::with('class', 'section')
            ->where('tenant_id', $tenant->id)
            ->where('RollNumber', $request->user)
            ->first();

        if ($student && Hash::check($request->password, $student->Password)) {
            $FcmToken = FcmToken::where('user_id', $student->id)->first();
            if ($FcmToken) {
                $FcmToken->update(['fcm_token' => $request->fcm_token]);
            } else {
                $FcmTokenCreate = new FcmToken;
                $FcmTokenCreate->user_id = $student->id;
                $FcmTokenCreate->fcm_token = $request->fcm_token;
                $FcmTokenCreate->save();
            }

            return $this->apiSuccessResponse('Student retrieved successfully', $student);
        } else {
            return $this->apiErrorResponse('Student or Campus Not Found', 404);
        }
    }

    public function Campuses()
    {
        $tenants = Tenant::select('id', 'domain', 'data')->get();

        return $this->apiSuccessResponse('Campuses retrieved successfully', $tenants);
    }

    public function homeWork(Request $request)
    {
        $tenant = MobileTenantVerifaction($request);
        if (! $tenant) {
            return $this->apiErrorResponse('Student or Campus Not Found', 404);
        }
        $currentSession = APIfetchCurrentSession($tenant);
        if (! $currentSession) {
            return $this->apiErrorResponse('Current Session Not Found', 404);
        }

        $homeWorks = HomeWork::with('SubjectRel')->where('sessionId', $currentSession->id)->where('classId', $request->classId)->where('sectionId', $request->sectionId)->where('tenant_id', $tenant->id)->get();

        return $this->apiSuccessResponse('HomeWorks retrieved successfully', $homeWorks);
    }

    public function ClassTimeTable(Request $request)
    {
        // return $request->all();
        $tenant = MobileTenantVerifaction($request);
        if (! $tenant) {
            return $this->apiErrorResponse('Student or Campus Not Found', 404);
        }
        $currentSession = APIfetchCurrentSession($tenant);
        if (! $currentSession) {
            return $this->apiErrorResponse('Current Session Not Found', 404);
        }

        $date = Carbon::parse($request->Date)->format('Y-m-d');
        $classTimeTables = ClassTimeTable::where('SessionId', $currentSession->id)
            ->with('subject')
            ->where('ClassId', $request->classId)
            ->where('SectionId', $request->sectionId)
            ->whereDate('date', $date)
            ->where('tenant_id', $tenant->id)
            ->get();

        return $this->apiSuccessResponse('Class Time Table retrieved successfully', $classTimeTables);
    }

    public function studentAttendance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'studentId' => 'required|integer|exists:students,id',
            'campus' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $tenant = MobileTenantVerifaction($request);
        if (! $tenant) {
            return $this->apiErrorResponse('Student or Campus Not Found', 404);
        }

        $currentSession = APIfetchCurrentSession($tenant);
        if (! $currentSession) {
            return $this->apiErrorResponse('Current Session Not Found', 404);
        }

        $startDate = \Carbon\Carbon::parse($request->start_date ?? now())->toDateString();
        $endDate = \Carbon\Carbon::parse($request->end_date ?? $startDate)->toDateString();

        // $studentAttendance = StudentAttendance::where('tenant_id', $tenant->id)
        //     ->where('SessionId', $currentSession->id)
        //     ->where('StudentId', $request->studentId)
        //     ->whereBetween('AttendanceDate', [$startDate, $endDate])
        //     ->get();
        $studentAttendance = StudentAttendance::where('tenant_id', $tenant->id)
            ->where('StudentId', $request->studentId)
            ->whereDate('AttendanceDate', '>=', $startDate)
            ->whereDate('AttendanceDate', '<=', $endDate)
            ->get();

        return $this->apiSuccessResponse(
            'Student Attendance retrieved successfully',
            $studentAttendance
        );
    }

    public function getStudentFee(Request $request)
    {

        $tenant = MobileTenantVerifaction($request);
        if (! $tenant) {
            return $this->apiErrorResponse('Student or Campus Not Found', 404);
        }
        $currentSession = APIfetchCurrentSession($tenant);
        if (! $currentSession) {
            return $this->apiErrorResponse('Current Session Not Found', 404);
        }
        $student = Student::select('id', 'tenant_id', 'IsActive', 'RollNumber', 'FirstName', 'LastName')->where('tenant_id', $tenant->id)->where('IsActive', 1)->where('id', $request->studentId)->firstOrFail();

        $generateFeeChallan = GenerateFeeChallan::withSum('transection', 'BalanceFeeAfterDiscount')
            ->with('challanArrearsSumMobile')
            ->with('partialPayments')
            ->where('tenant_id', $tenant->id)
            ->where('IsActive', 1)
            ->where('SessionId', $currentSession->id)
            ->where('StudentId', $request->studentId)
            ->orderBy('id', 'desc')
            ->get();

        $generateFeeChallan->map(function ($item) {
            return $item->amount_total = $item->challanArrearsSumMobile->sum('arrear_challan_transaction_sum_feeamount')
                + $item->challanArrearsSumMobile->sum('arrear_challan_fine.FineAmount') + $item->transection_sum_balancefeeafterdiscount;
        });

        $data['studentData'] = $student;
        $data['generateFeeChallan'] = $generateFeeChallan;

        return $this->apiSuccessResponse('Student Fee retrieved successfully', $data);
    }

    public function getStudentExams(Request $request)
    {
        $tenant = MobileTenantVerifaction($request);
        if (! $tenant) {
            return $this->apiErrorResponse('Student or Campus Not Found', 404);
        }
        $currentSession = APIfetchCurrentSession($tenant);
        if (! $currentSession) {
            return $this->apiErrorResponse('Current Session Not Found', 404);
        }

        $examMarksDetail = ExamMarksDetail::where('tenant_id', $tenant->id)
            ->where('IsActive', 1)
            ->where('StudentId', $request->studentId)
            ->pluck('ExamMarksId');
        if (count($examMarksDetail) <= 0) {
            return $this->apiErrorResponse('No Exam Not Found', 404);
        }

        $ExamMarksSubjectid = ExamMarks::whereIn('id', $examMarksDetail->toArray())
            ->where('tenant_id', $tenant->id)
            ->where('IsActive', 1)
            ->where('SessionId', $currentSession->id)
            ->pluck('ExamSubjectId');
        if (count($ExamMarksSubjectid) <= 0) {
            return $this->apiErrorResponse('No Exam Not Found', 404);
        }

        $subjectsExamId = ExamSubject::whereIn('id', $ExamMarksSubjectid)
            ->where('tenant_id', $tenant->id)
            ->where('IsActive', 1)
            ->where('SessionId', $currentSession->id)
            ->distinct()->pluck('ExamId');

        if (count($subjectsExamId) <= 0) {
            return $this->apiErrorResponse('No Exam Not Found', 404);
        }

        $examType = ExamType::select('id', 'tenant_id', 'IsActive', 'ExamName', 'SessionId', 'ResultDeclarationDate')
            ->whereIn('id', $subjectsExamId->toArray())
            ->where('tenant_id', $tenant->id)
            ->where('IsActive', 1)
            ->where('SessionId', $currentSession->id)
            ->orderBy('id', 'desc')
            ->get()
            ->map(function ($exam) use ($request) {
                $exam->student_id = $request->studentId;

                return $exam;
            });

        return $this->apiSuccessResponse('Student Exams retrieved successfully', $examType);
    }

    public function getStudentExamsDetail(Request $request)
    {
        $tenant = MobileTenantVerifaction($request);
        if (! $tenant) {
            return $this->apiErrorResponse('Student or Campus Not Found', 404);
        }
        $currentSession = APIfetchCurrentSession($tenant);
        if (! $currentSession) {
            return $this->apiErrorResponse('Current Session Not Found', 404);
        }

        $student = Student::select('id', 'tenant_id', 'ClassId')->where('tenant_id', $tenant->id)->where('id', $request->studentId)->first();

        $examGrade = ExamGrade::select('id', 'IsActive', 'tenant_id', 'GradeName', 'ClassId', 'SessionId', 'PercentFrom', 'PercentUpt')
            ->where('ClassId', $student->ClassId)
            ->where('SessionId', $currentSession->id)
            ->where('IsActive', 1)
            ->where('tenant_id', $tenant->id)
            ->get();

        $examDetail = ExamMarksDetail::select('id', 'tenant_id', 'IsActive', 'ExamMarksId', 'StudentId', 'Marks')
            ->where('StudentId', $request->studentId)
            ->where('tenant_id', $tenant->id)
            ->whereHas('examMarks', function ($query) use ($request, $currentSession) {
                $query->where('SessionId', $currentSession->id)
                    ->whereHas('ExamSubject', function ($subjectQuery) use ($request, $currentSession) {
                        $subjectQuery->where('ExamId', $request->exam_id)
                            ->where('SessionId', $currentSession->id);
                    });
            })
            ->with([
                'examMarks' => function ($query) use ($request, $currentSession) {
                    $query->select('id', 'IsActive', 'SessionId', 'ExamSubjectId')->where('SessionId', $currentSession->id)
                        ->with([
                            'ExamSubject' => function ($subjectQuery) use ($request, $currentSession) {
                                $subjectQuery->select('id', 'tenant_id', 'SessionId', 'Title', 'ExamId', 'SubjectId', 'MarksMax')->where('ExamId', $request->exam_id)
                                    ->where('SessionId', $currentSession->id)->with('Subject:id,SubjectName');
                            },
                        ]);
                },
            ])
            ->get();
        $data['examDetail'] = $examDetail;
        $data['examGrade'] = $examGrade;

        return $this->apiSuccessResponse('Student exam detail retrieved successfully', $data);
    }

    public function resetPassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'campus' => 'required',
            'studentId' => 'required',
            'oldPassword' => 'required',
            'newPassword' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $tenant = MobileTenantVerifaction($request);
        if (! $tenant) {
            return $this->apiErrorResponse('Student or Campus Not Found', 404);
        }
        $currentSession = APIfetchCurrentSession($tenant);
        if (! $currentSession) {
            return $this->apiErrorResponse('Current Session Not Found', 404);
        }

        $student = Student::where('id', $request->studentId)->first();
        if ($student) {
            if (! Hash::check($request->oldPassword, $student->Password)) {
                return $this->apiErrorResponse('Old password is incorrect', 404);
            }
            $student->update([
                'Password' => Hash::make($request->newPassword),
            ]);
        }

        return $this->apiSuccessResponse('Password updated successfully');
    }
}
