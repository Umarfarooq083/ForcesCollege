<?php

namespace App\Services\Reports;

use App\Models\ContentFeedback;
use App\Models\ExamGrade;
use App\Models\ExamType;
use App\Models\GenerateFeeChallan;
use App\Models\Staff;
use App\Models\Student;
use App\Models\StudentInquiry;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Container\Attributes\Log;
use Illuminate\Support\Facades\DB;

class MaterReportsService
{
    private $tenant_data;
    public function __construct()
    {
        $this->tenant_data = Tenant::where('id', tenant('id'))->first();
    }
    public function studentAdmissionReport($request)
    {
        $query = Student::query()
            ->where('tenant_id', tenant('id'))
            ->with(['class', 'section'])
            ->when($request->ClassId, fn($q) => $q->where('ClassId', $request->ClassId))
            ->when($request->SectionId, fn($q) => $q->where('SectionId', $request->SectionId))
            ->when($request->gender, fn($q) => $q->where('Gender', $request->gender))
            ->when($request->rollno, fn($q) => $q->where('RollNumber', $request->rollno));

        if (!empty($request->start_date)) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $query->whereDate('AdmissionDate', '>=', $start);
        }

        if (!empty($request->end_date)) {
            $end = Carbon::parse($request->end_date)->endOfDay();
            $query->whereDate('AdmissionDate', '<=', $end);
        }

        $students = $query->orderBy('AdmissionDate', 'desc')
            ->get([
                'id',
                'AdmissionDate',
                'RollNumber',
                'FirstName',
                'LastName',
                'Gender',
                'MobileNumber',
                'ClassId',
                'SectionId'
            ]);

        $meta = [
            'campus' =>  $this->tenant_data->domain,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
        ];

        $data = [
            'students' => $students,
            'meta' => $meta,
        ];
        return $data;
    }

    public function studentAdmissionInquiryReport($request)
    {
        $query = StudentInquiry::query()
            ->where('tenant_id', tenant('id'))
            ->with('class', 'source', 'inqSession')
            ->when($request->ClassId, fn($q) => $q->where('ClassId', $request->ClassId))
            ->when($request->gender, fn($q) => $q->where('Gender', $request->gender));

       if (!empty($request['start_date']) && !empty($request['end_date'])) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end   = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('Date', [$start, $end]);
        }

        $students = $query->orderBy('Date', 'desc')
            ->get([
                'id',
                'Date',
                'Name',
                'LastName',
                'FatherName',
                'FatherPhoneNo',
                'Address',
                'NextFollowUpDate',
                'SourceId',
                'NumberOfChild',
                'SessionId',
                'Gender',
                'ClassId'
            ]);

        $meta = [
            'campus' =>  $this->tenant_data->domain,
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
        ];

        $data = [
            'students' => $students,
            'meta' => $meta,
        ];
        return $data;
    }

    public function studentUnPaidFee($request)
    {
        $created_date = sprintf('%04d-%02d-01', $request->year, $request->month);

        $query = GenerateFeeChallan::query()
            ->where('tenant_id', tenant('id'))
            ->where('IsActive', 1)
            ->where('Status', 'Unpaid')
            ->with('student.class', 'student.section')
            ->with(['partialPayments' => function ($q) {
                $q->select('id', 'IsActive', 'tenant_id', 'GenerateClassChallanId', 'ReceivedAmount', 'CollectDate', 'PaymentMode', 'SubmitDate')
                    ->where('IsActive', true)
                    ->where('tenant_id', tenant('id'));
            }])
            ->withSum(
                ['partialPayments as partial_paid_amount' => function ($q) {
                    $q->where('IsActive', true)
                        ->where('tenant_id', tenant('id'));
                }],
                'ReceivedAmount'
            )
            ->with('ChallanArrearsRel.arrear_challan_fine')
            ->with('challanArrearsSum')
            ->withSum('transection', 'BalanceFeeAfterDiscount')
        
            ->when($request->student_id, function ($q) use ($request) {
                $q->where('StudentId', $request->student_id);
            })

            ->when($request->month && $request->year, function ($q) use ($created_date) {
                $q->where('ChallanMonth', $created_date);
            })

            ->when($request->ClassId, function ($q) use ($request) {
                $q->whereHas('student', function ($subQuery) use ($request) {
                    $subQuery->where('ClassId', $request->ClassId);
                });
            })

            ->when($request->SectionId, function ($q) use ($request) {
                $q->whereHas('student', function ($subQuery) use ($request) {
                    $subQuery->where('SectionId', $request->SectionId);
                });
            });

        $GenerateFeeChallan = $query->orderBy('id', 'desc')
            ->get([
                'id',
                'tenant_id',
                'challan_no',
                'IsActive',
                'SessionId',
                'StudentId',
                'ChallanMonth',
                'SubmitDate',
                'IsPartialPayment',
                'Status',
                'FineAmount',
                'WaivedFineAmount',
                'transection_sum_balancefeeafterdiscount',
            ]);

        foreach ($GenerateFeeChallan as $challan) {
            $challan->total_arrear_fine = 0;
            foreach ($challan->ChallanArrearsRel as $arrearFine) {
                if($arrearFine->TransactionType === 'Fine'){
                    $fineAmount = $arrearFine['arrear_challan_fine']['FineAmount'];
                    $challan->total_arrear_fine += $fineAmount;
                }else{
                if( !empty($arrearFine['arrear_challan_fine']) && $arrearFine['arrear_challan_fine']['per_day_fine']) {
                    $expiryDate = Carbon::parse($arrearFine['arrear_challan_fine']['ExpiryDate']);
                    $dueDate    = Carbon::parse($arrearFine['arrear_challan_fine']['DueDate']);
                    $perDayFine = $arrearFine['arrear_challan_fine']['per_day_fine'];
                    $days = $dueDate->diffInDays($expiryDate); 
                    $challan->total_arrear_fine += $days * $perDayFine;
                } else if( !empty($arrearFine['arrear_challan_fine']) && $arrearFine['arrear_challan_fine']['FineAmount']) {
                    $challan->total_arrear_fine += $arrearFine['arrear_challan_fine']['FineAmount'];
                }
                } 
            }
        }

        $GenerateFeeChallan = $GenerateFeeChallan->map(function ($item) {
            $arrearsSum = $item->challanArrearsSum
                ->sum(fn($row) => (int) $row->arrear_challan_transaction_sum_balancefeeafterdiscount);

            $partialPaidSum = $item->challanArrearsSum
                ->flatMap(function ($row) {
                    return $row->challan_partial_payment ?? [];
                })
                ->sum(function ($payment) {
                    return (float) $payment['ReceivedAmount'];
                });

            $item->total_arrears_amount = $arrearsSum;
            $item->arrear_partial_sum  = $partialPaidSum;

            return $item;
        });

        $meta = [
            'campus' =>  $this->tenant_data->domain,
            'month' =>  $request->month,
            'year' =>  $request->year,
        ];

        $data = [
            'generated_challan' => $GenerateFeeChallan,
            'meta' => $meta,
        ];
        return $data;
    }

    public function studentSummaryReport($request)
    {

        //     $class_type_id = campusClassList();
        //     $classes = Classes::whereIn('class_type_id', $class_type_id)
        //     ->with([
        //         'sections' => function ($q) {
        //             $q->where('tenant_id', tenant('id'))
        //             ->withCount([
        //                 'students as male_students' => function ($q) {
        //                     $q->where('tenant_id', tenant('id'))->where('IsActive', 1)->where('IsDisable', 0)
        //                         ->where('gender', 'Male');
        //                 },
        //                 'students as female_students' => function ($q) {
        //                     $q->where('tenant_id', tenant('id'))->where('IsActive', 1)->where('IsDisable', 0)
        //                         ->where('gender', 'Female');
        //                 },
        //                 'students as total_students' => function ($q) {
        //                     $q->where('tenant_id', tenant('id'))->where('IsActive', 1)->where('IsDisable', 0);
        //                 },
        //             ]);
        //         }
        //     ])
        //     ->get();

        // dd($classes->toArray());



        // $report = Student::select(
        //     'students.ClassId',
        //     'students.SectionId',
        //     'classes.ClassName as class_name',
        //     'sectiones.SectionName  as section_name',
        //     DB::raw("SUM(CASE WHEN gender = 'Male' THEN 1 ELSE 0 END) as male_count"),
        //     DB::raw("SUM(CASE WHEN gender = 'Female' THEN 1 ELSE 0 END) as female_count"),
        //     DB::raw("COUNT(*) as total_students")
        // )
        //     ->join('classes', 'classes.id', '=', 'students.ClassId')
        //     ->join('sectiones', 'sectiones.id', '=', 'students.SectionId')
        //     ->where('students.tenant_id', tenant('id'))->where('students.IsActive', 1)->where('students.IsDisable', 0)
        //     ->orderBy('students.ClassId', 'asc')
        //     ->groupBy('ClassId', 'SectionId')
        //     ->get();

        $report = Student::select(
            'students.ClassId',
            'students.SectionId',
            'classes.ClassName as class_name',
            'sectiones.SectionName as section_name',
            DB::raw("SUM(CASE WHEN gender = 'Male' THEN 1 ELSE 0 END) as male_count"),
            DB::raw("SUM(CASE WHEN gender = 'Female' THEN 1 ELSE 0 END) as female_count"),
            DB::raw("COUNT(*) as total_students")
        )
            ->join('classes', 'classes.id', '=', 'students.ClassId')
            ->join('sectiones', 'sectiones.id', '=', 'students.SectionId')
            ->where('students.tenant_id', tenant('id'))
            ->where('students.IsActive', 1)
            ->orderBy('students.ClassId', 'asc')
            ->groupBy('students.ClassId', 'students.SectionId', 'classes.ClassName', 'sectiones.SectionName')
            ->get();


        $meta = [
            'campus' =>  $this->tenant_data->domain,
        ];

        $data = [
            'students_summary' => $report,
            'meta' => $meta,
        ];
        return $data;
    }

    public function contentFeedbackReport()
    {
        $ContentFeedback = ContentFeedback::get();

        $meta = [
            'campus' =>  $this->tenant_data->domain,
        ];
        $data = [
            'content_feedback' => $ContentFeedback,
            'meta' => $meta,
        ];
        return $data;
    }

    public function studentLedger($request)
    {
        $generateChallanData = GenerateFeeChallan::select(
            'id',
            'tenant_id',
            'challan_no',
            'IsActive',
            'SessionId',
            'StudentId',
            'ChallanMonth',
            'DueDate',
            'CollectDate',
            'ExpiryDate',
            'Status',
            'FineAmount',
            'WaivedFineAmount',
            'per_day_fine'
        )
            ->with('SessionRel')
            ->withSum(
                ['challanTransactions as total_amount' => function ($q) {
                    $q->where('tenant_id', tenant('id'));
                }],
                'BalanceFeeAfterDiscount'
            )
            ->with(['partialPayments' => function ($q) {
                $q->select('id', 'IsActive', 'tenant_id', 'GenerateClassChallanId', 'ReceivedAmount', 'CollectDate', 'PaymentMode', 'SubmitDate')
                    ->where('IsActive', true)
                    ->where('tenant_id', tenant('id'));
            }])
            ->withSum(
                ['partialPayments as partial_paid_amount' => function ($q) {
                    $q->where('IsActive', true)
                        ->where('tenant_id', tenant('id'));
                }],
                'ReceivedAmount'
            )
            ->with('ChallanArrearsRel.arrear_challan_fine')
            ->with('challanArrearsSum')
            ->where('tenant_id', tenant('id'))
            ->where('IsActive', 1)
            ->where('StudentId', $request->student_id)
            ->orderBy('ChallanMonth', 'asc')
            ->get();

        foreach ($generateChallanData as $challan) {
            $challan->total_arrear_fine = 0;

            foreach ($challan->ChallanArrearsRel as $arrearFine) {
                $arrearFineData = $arrearFine['arrear_challan_fine'] ?? null;

                if (empty($arrearFineData)) {
                    continue;
                }

                if ($arrearFine->TransactionType === 'Fine') {
                    $challan->total_arrear_fine += (float) ($arrearFineData['FineAmount'] ?? 0);
                    continue;
                }

                if (!empty($arrearFineData['per_day_fine'])) {
                    $expiryDate = Carbon::parse($arrearFineData['ExpiryDate']);
                    $dueDate = Carbon::parse($arrearFineData['DueDate']);
                    $days = $dueDate->diffInDays($expiryDate);

                    $challan->total_arrear_fine += $days * (float) $arrearFineData['per_day_fine'];
                } else {
                    $challan->total_arrear_fine += (float) ($arrearFineData['FineAmount'] ?? 0);
                }
            }
        }

        $generateChallanData = $generateChallanData->map(function ($item) {
            $arrearsSum = $item->challanArrearsSum
                ->sum(fn($row) => (float) ($row->arrear_challan_transaction_sum_balancefeeafterdiscount ?? 0));

            $partialPaidSum = $item->challanArrearsSum
                ->flatMap(function ($row) {
                    return $row->challan_partial_payment ?? [];
                })
                ->sum(function ($payment) {
                    return (float) ($payment['ReceivedAmount'] ?? 0);
                });

            $item->total_arrears_amount = $arrearsSum;
            $item->arrear_partial_sum = $partialPaidSum;

            return $item;
        });

        $previousBalance = 0;
        $previousFine = 0;

        foreach ($generateChallanData as $challan) {
            $baseAmount = (float) ($challan->total_amount ?? 0);
            $currentPaidAmount = (float) ($challan->partial_paid_amount ?? 0);

            $challan->amount_due = max(0, $baseAmount + $previousBalance + $previousFine);
            $challan->amount_paid = $currentPaidAmount;

            if ($challan->amount_paid == 0 && $challan->Status === 'Paid') {
                $challan->amount_paid = $challan->amount_due;
            }

            $challan->balance = max(0, $challan->amount_due - $challan->amount_paid);
            $challan->total_arrears_amount = $previousBalance;
            $challan->total_arrear_fine = $previousFine;

            $previousBalance = $challan->balance;
            $previousFine = 0;

            if ($previousBalance > 0) {
                if (!empty($challan->per_day_fine) && !empty($challan->DueDate) && !empty($challan->ExpiryDate)) {
                    $expiryDate = Carbon::parse($challan->ExpiryDate);
                    $dueDate = Carbon::parse($challan->DueDate);
                    $days = $dueDate->diffInDays($expiryDate);
                    $previousFine = max(0, ($days * (float) $challan->per_day_fine) - (float) ($challan->WaivedFineAmount ?? 0));
                } else {
                    $previousFine = max(0, (float) ($challan->FineAmount ?? 0) - (float) ($challan->WaivedFineAmount ?? 0));
                }
            }
        }

        $meta = [
            'campus' =>  $this->tenant_data->domain,
        ];
        $data = [
            'generateChallanData' => $generateChallanData,
            'meta' => $meta,
        ];
        return $data;
    }

    public function employeeReport()
    {
        $staffData = Staff::with('DesignationRel')->where('tenant_id', tenant('id'))->where('IsActive', 1)->get();
        $meta = [
            'campus' =>  $this->tenant_data->domain,
        ];
        $data = [
            'staffData' => $staffData,
            'meta' => $meta,
        ];
        return $data;
    }

    public function assesmentReportFetch($request)
    {
        $report = DB::table('exam_marks as EM')
            ->join('exam_marks_details as EMD', 'EM.Id', '=', 'EMD.ExamMarksId')
            ->join('exam_subjects as ES', 'ES.Id', '=', 'EM.ExamSubjectId')
            ->join('exam as ex', 'ES.ExamId', '=', 'ex.Id')
            ->join('subjects as Sub', 'Sub.Id', '=', 'ES.SubjectId')
            ->join('classes as CL', 'CL.Id', '=', 'Sub.ClassId')
            ->join('students as std', 'EMD.StudentId', '=', 'std.Id')
            ->join('lms_sessions as SES', 'EM.SessionId', '=', 'SES.Id')
            ->where('EM.IsActive', 1)
            ->where('EM.deleted_at', null)
            ->where('ES.ExamId', $request->exam_id)
            ->where('CL.Id', $request->ClassId)
            ->select(
                'std.id',
                'CL.ClassName',
                'std.RollNumber',
                // DB::raw("CONCAT(std.FirstName, ' ', std.LastName) AS StudentName"),
                'std.FirstName',
                'std.LastName',
                'ES.Title as ExamSubjectTitle',
                'ex.ExamName as ExamName',
                'Sub.SubjectName',
                'ES.MarksMax as TotalMarks',
                'EMD.Marks as ObtainMarks',
                'SES.name as SessionName'
            )
            ->orderBy('std.id')
            ->get();

        $result = collect($report);
        $subjectsList = $result->pluck('SubjectName')->unique()->values();
        $grades = ExamGrade::where('tenant_id', tenant('id'))->where('ClassId', $request->ClassId)->orderBy('PercentFrom', 'desc')->get();
        $grouped = $result->groupBy('id')->map(function ($records) use ($subjectsList, $grades) {
            $first = $records->first();
            $subjects = [];
            foreach ($subjectsList as $subject) {
                $subjects[$subject] = null;
            }

            foreach ($records as $rec) {
                $subjects[$rec->SubjectName] = floatval($rec->ObtainMarks);
            }
            $obtained = array_sum($subjects);
            $totalMax = $records->sum('TotalMarks');
            $percentage = round(($obtained / $totalMax) * 100, 2);
            $grade = $this->getDynamicGrade($percentage, $grades);

            return [
                'RollNumber' => $first->RollNumber,
                'StudentName' => $first->FirstName . ' ' . $first->LastName,
                'Subjects'   => $subjects,
                'TotalObtained' => $obtained,
                'TotalMax' => $totalMax,
                'Percentage' => $percentage,
                'Grade' => $grade
            ];
        });

        $data['students'] = $grouped;
        $data['subjects'] = $subjectsList;


        return $data;
        // dd($grouped->toArray());
        // $meta = [
        //     'campus' =>  $this->tenant_data->domain,
        // ];
        // $data = [
        //     'staffData' => $grouped,
        //     'meta' => $meta,
        // ];
        // return $data;
    }

    public function termWiseFetch($request)
    {
        $ExamTypeData = ExamType::select('id', 'tenant_id', 'ExamName')->where('tenant_id', tenant('id'))
            ->where('id', $request->exam_id)
            ->first();

        $result = DB::table('exam_marks_details as EMD')
            ->distinct()
            ->select(
                'EMD.StudentId',
                'STD.FirstName',
                'STD.LastName',
                'STD.RollNumber',
                'EX.ExamName',
                'ES.MarksMax',
                'EMD.Marks',
            )
            ->join('exam_marks as EM', function ($join) {
                $join->on('EM.id', '=', 'EMD.ExamMarksId')
                    ->where('EM.IsActive', 1)
                    ->where('EM.deleted_at', null);
            })
            ->join('exam_subjects as ES', function ($join) use ($request) {
                $join->on('EM.ExamSubjectId', '=', 'ES.id')
                    ->where('ES.ExamId', $request->exam_id);
            })
            ->join('subjects as SUB', function ($join) use ($request) {
                $join->on('SUB.id', '=', 'ES.SubjectId')
                    ->where('SUB.ClassId', $request->ClassId);
            })
            ->join('exam as EX', function ($join) {
                $join->on('EX.id', '=', 'ES.ExamId');
            })
            ->join('students as STD', function ($join) {
                $join->on('STD.id', '=', 'EMD.StudentId')
                    ->on('STD.ClassId', '=', 'SUB.ClassId');
            })
            ->get();
            
        $final = $result->groupBy('StudentId')->values()->map(function ($records, $index) use ($request) {
            $first = $records->first();
            $grades = ExamGrade::where('tenant_id', tenant('id'))->where('ClassId', $request->ClassId)->orderBy('PercentFrom', 'desc')->get();
            $totalMarks = $records->sum('MarksMax');
            $obtainedMarks = $records->sum('Marks');

            $percentage = $totalMarks > 0
                ? round(($obtainedMarks / $totalMarks) * 100, 2)
                : 0;
            $grade = $this->getDynamicGrade($percentage, $grades);
            return [
                'SrNo'           => $index + 1,
                'RollNumber'     => $first->RollNumber,
                'StudentName'    => $first->FirstName . ' ' . $first->LastName,
                'ObtMarks'       => $obtainedMarks,
                'TotalMarks'     => $totalMarks,
                'ObtainedMarks'  => $obtainedMarks,
                'Percentage'     => $percentage . '%',
                'Grade'          => $grade,
            ];
        });
        $data['exam'] = $ExamTypeData;
        $data['termwiseData'] = $final;
        $data['campus'] = $this->tenant_data->domain;
        return $data;
    }

    public function parentProfessionReport($request)
    {

        $query = Student::query();
        $query->where('tenant_id', tenant('id'))
            // ->where('IsActive',1)->where('IsDisable',0)
            ->with(['class', 'section'])
            ->when($request->profission, fn($q) => $q->where('FatherOccupation', $request->profission));
        $students = $query->orderBy('id', 'asc')
            ->get([
                'id',
                'tenant_id',
                'RollNumber',
                'FatherName',
                'FatherPhone',
                'MobileNumber',
                'FirstName',
                'LastName',
                'FatherOccupation',
                'ClassId',
                'SectionId'
            ]);

        $meta = [
            'campus' =>  $this->tenant_data->domain,
        ];
        $data = [
            'students' => $students,
            'meta' => $meta,
        ];
        return $data;
    }

    private function getDynamicGrade($percentage, $grades)
    {
        foreach ($grades as $g) {
            if ($percentage >= $g->PercentFrom && $percentage <= $g->PercentUpt) {
                return $g->GradeName;
            }
        }
        return 'N/A';
    }

    public function studentAttendanceFetch($request)
    {
        $month = $request->month;
        $year  = $request->year;
        $classId = $request->ClassId;
        $sectionId = $request->SectionId;

        $filePath = resource_path('views/reports/StudentAttendanceReport.html');
        $html = file_get_contents($filePath);

        $datesWithDays = $this->getDatesWithDayNames($month, $year);

        $tdHeader = "<td style='Color:#fff; background-color:#385623;'>Sr.</td><td style='Color:#fff; background-color:#385623;'>Reg</td><td style='Color:#fff; background-color:#385623;'>Name</td>";
        $tdNameHeader = "<td colspan='3'></td>";
        $srNo = 1;
        $isSundayAdded = false;

        $students = Student::select('id', 'tenant_id', 'RollNumber', 'ClassId', 'SectionId', 'FirstName', 'LastName')->with([
            'studentAttendance' => function ($q) use ($month, $year) {
                $q->whereMonth('AttendanceDate', $month)
                    ->whereYear('AttendanceDate', $year)
                    ->where('IsActive', 1);
            },
        ])
            ->where('tenant_id', tenant('id'))
            ->where('ClassId', $classId)
            ->where('SectionId', $sectionId)
            ->get();

        $filteredStudents = $students->filter(function ($student) {
            return $student->studentAttendance->count() > 0;
        });
        $studentHtml = '';

        if ($filteredStudents->count() > 0) {

            foreach ($filteredStudents as $student) {

                $totalPresent = 0;
                $totalAbsent = 0;
                $totalLeave = 0;
                $totalMissing = 0;

                $row = "<tr>
                <td style='Color:#385623; font-size:14px; font-weight:bold;'>{$srNo}</td>
                <td style='Color:#385623; font-size:14px; font-weight:bold;'>{$student->RollNumber}</td>
                <td style='Color:#385623; font-size:14px; font-weight:bold;'>{$student->FirstName} {$student->LastName}</td>";

                if ($srNo > 1) {
                    $isSundayAdded = true;
                }

                foreach ($datesWithDays as $dateItem) {

                    if ($dateItem['dayName'] == 'sunday' && $isSundayAdded) {
                        continue;
                    } elseif ($dateItem['dayName'] == 'sunday' && $isSundayAdded == false) {
                        $row .= "<td rowspan='1000' style='vertical-align:middle;'>
                    <div  style='font-size:18px; color: #385623; font-weight:bold; writing-mode: vertical-rl;display:flex;justify-content:center;align-items:center;width:100%;'>
                        Weekly Holiday
                    </div>
                 </td>";
                    } else {
                        $dateString = sprintf("%04d-%02d-%02d", $year, $month, $dateItem['day']);
                        $attendance = $student->studentAttendance
                            ->first(function ($att) use ($dateString) {
                                return $att['AttendanceDate']->format('Y-m-d') === $dateString;
                            });

                        if (!$attendance) {
                            $row .= "<td style='Color:#385623; font-size:14px; font-weight:bold;'>M</td>";
                            $totalMissing++;
                        } else {
                            switch (strtolower($attendance->AttendanceType)) {

                                case 'absent':
                                    $row .= "<td style='Color:#385623; font-size:14px; font-weight:bold;'>A</td>";
                                    $totalAbsent++;
                                    break;

                                case 'late':
                                case 'present':
                                    $row .= "<td style='Color:#385623; font-size:14px; font-weight:bold;'>P</td>";
                                    $totalPresent++;
                                    break;

                                case 'leave':
                                    $row .= "<td style='Color:#385623; font-size:14px; font-weight:bold;'>L</td>";
                                    $totalLeave++;
                                    break;
                            }
                        }
                    }
                }

                $row .= "<td style='Color:#385623; font-size:14px; font-weight:bold;'>{$totalPresent}</td>
                     <td style='Color:#385623; font-size:14px; font-weight:bold;'>{$totalAbsent}</td>
                     <td style='Color:#385623; font-size:14px; font-weight:bold;'>{$totalLeave}</td>
                     <td style='Color:#385623; font-size:14px; font-weight:bold;'>{$totalMissing}</td>";

                $studentHtml .= $row . "</tr>";
                $srNo++;
            }

            foreach ($datesWithDays as $item) {
                $tdHeader .= "<td style='Color:#fff; font-size:14px; font-weight:bold;'>{$item['day']}</td>";

                if ($item['dayName'] == 'sunday') {
                    $tdNameHeader .= "<td rowspan='1000' style='Color:#385623; font-size:14px; font-weight:bold;'>{$item['dayName']}</td>";
                } else {
                    $tdNameHeader .= "<td style='Color:#fff; font-size:14px; font-weight:bold;'>{$item['dayName']}</td>";
                }
            }

            $tdHeader .= "<td style='Color:#fff; font-size:14px; font-weight:bold;'>T.P</td><td style='Color:#fff; font-size:14px; font-weight:bold;'>T.A</td><td style='Color:#fff; font-size:14px; font-weight:bold;'>T.L</td><td style='Color:#fff; font-size:14px; font-weight:bold;'>T.M</td>";
            $tdNameHeader .= "<td colspan='4'></td>";

            $html = str_replace("{{thHeader}}", $tdHeader, $html);
            $html = str_replace("{{trBody}}", $studentHtml, $html);
            $html = str_replace("{{noRecordFound}}", "", $html);
        } else {
            $html = str_replace("{{thHeader}}", "", $html);
            $html = str_replace("{{trBody}}", "", $html);
            $html = str_replace("{{noRecordFound}}", "<h2>No Record Found</h2>", $html);
        }

        if ($students->count() > 0) {
            $html = str_replace("{{ClassName}}", $students->first()->class->ClassName, $html);
            $html = str_replace("{{SectionName}}", strtoupper($students->first()->section->SectionName), $html);
        }
        $monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        $html = str_replace("{{MonthYear}}", $monthNames[$month - 1] . "/" . $year, $html);
        $host = $this->tenant_data->domain;
        $html = str_replace("{{Campus}}", strtoupper($host), $html);
        return $html;
    }

    public function staffAttendanceReport($request)
    {
        $month = $request->month;
        $year  = $request->year;

        $filePath = resource_path('views/reports/StaffAttendanceReport.html');
        $html = file_get_contents($filePath);
        $datesWithDays = $this->getDatesWithDayNames($month, $year);


        $tdHeader = "<td style='Color:#fff; background-color:#385623;'>Sr.</td>
                 <td style='Color:#fff; background-color:#385623;'>Staff ID</td>
                 <td style='Color:#fff; background-color:#385623;'>Name</td>";

        $tdNameHeader = "<td colspan='3'></td>";
        $srNo = 1;
        $isSundayAdded = false;

        $staff = Staff::select('id', 'tenant_id', 'StaffCode', 'FirstName', 'LastName')
            ->with([
                'attendance' => function ($q) use ($month, $year) {
                    $q->whereMonth('AttendanceDate', $month)
                        ->whereYear('AttendanceDate', $year)
                        ->where('IsActive', 1);
                }
            ])
            ->where('tenant_id', tenant('id'));

        $staff = $staff->get();

        // dd($staff);

        // Only staff having attendance
        $filteredStaff = $staff->filter(fn($s) => $s->attendance->count() > 0);

        $staffHtml = '';

        if ($filteredStaff->count() > 0) {

            foreach ($filteredStaff as $s) {

                $totalPresent = 0;
                $totalAbsent = 0;
                $totalLeave = 0;
                $totalMissing = 0;

                $row = "<tr>
                <td style='Color:#385623; font-size:14px; font-weight:bold;'>{$srNo}</td>
                <td style='Color:#385623; font-size:14px; font-weight:bold;'>{$s->StaffCode}</td>
                <td style='Color:#385623; font-size:14px; font-weight:bold;'>{$s->FirstName} {$s->LastName}</td>";

                if ($srNo > 1) {
                    $isSundayAdded = true;
                }

                foreach ($datesWithDays as $dateItem) {

                    if ($dateItem['dayName'] === 'sunday' && $isSundayAdded) {
                        continue;
                    } elseif ($dateItem['dayName'] === 'sunday' && !$isSundayAdded) {
                        $row .= "<td rowspan='1000' style='vertical-align:middle; '>
                        <div style=' font-size:18px; color:#385623;font-weight:bold; writing-mode: vertical-rl; display:flex; justify-content:center; align-items:center; width:100%;'>
                            Weekly Holiday
                        </div>
                    </td>";
                    } else {

                        $dateString = sprintf("%04d-%02d-%02d", $year, $month, $dateItem['day']);

                        // Find attendance for this date
                        $attendance = $s->attendance
                            ->first(
                                function ($att) use ($dateString) {
                                    return $att['AttendanceDate']->format('Y-m-d') == $dateString;
                                }
                            );

                        if (!$attendance) {
                            $row .= "<td style='Color:#385623; font-size:14px; font-weight:bold;'>M</td>";
                            $totalMissing++;
                        } else {

                            switch (strtolower($attendance->Attendance)) {
                                case 'absent':
                                    $row .= "<td style='Color:#385623; font-size:14px; font-weight:bold;'>A</td>";
                                    $totalAbsent++;
                                    break;

                                case 'late':
                                case 'present':
                                    $row .= "<td style='Color:#385623; font-size:14px; font-weight:bold;'>P</td>";
                                    $totalPresent++;
                                    break;

                                case 'leave':
                                    $row .= "<td style='Color:#385623; font-size:14px; font-weight:bold;'>L</td>";
                                    $totalLeave++;
                                    break;
                            }
                        }
                    }
                }

                $row .= "<td style='Color:#385623; font-size:14px; font-weight:bold;'>{$totalPresent}</td>
                     <td style='Color:#385623; font-size:14px; font-weight:bold;'>{$totalAbsent}</td>
                     <td style='Color:#385623; font-size:14px; font-weight:bold;'>{$totalLeave}</td>
                     <td style='Color:#385623; font-size:14px; font-weight:bold;'>{$totalMissing}</td>";

                $staffHtml .= $row . "</tr>";
                $srNo++;
            }

            foreach ($datesWithDays as $item) {
                $tdHeader .= "<td style='Color:#fff; font-size:14px; font-weight:bold; background-color:#385623;'>{$item['day']}</td>";

                if ($item['dayName'] == 'sunday') {
                    $tdNameHeader .= "<td rowspan='1000' style='Color:#385623; font-size:14px; font-weight:bold;'>{$item['dayName']}</td>";
                } else {
                    $tdNameHeader .= "<td style='Color:#fff; font-size:14px; font-weight:bold;'>{$item['dayName']}</td>";
                }
            }

            $tdHeader .= "<td style='Color:#fff; background-color:#385623;'>T.P</td><td style='Color:#fff; background-color:#385623;'>T.A</td><td style='Color:#fff; background-color:#385623;'>T.L</td><td style='Color:#fff; background-color:#385623;'>T.M</td>";
            $tdNameHeader .= "<td colspan='4'></td>";

            $html = str_replace("{{thHeader}}", $tdHeader, $html);
            $html = str_replace("{{trBody}}", $staffHtml, $html);
            $html = str_replace("{{noRecordFound}}", "", $html);
        } else {
            $html = str_replace("{{thHeader}}", "", $html);
            $html = str_replace("{{trBody}}", "", $html);
            $html = str_replace("{{noRecordFound}}", "<h2>No Record Found</h2>", $html);
        }

        $monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];
        $html = str_replace("{{MonthYear}}", $monthNames[$month - 1] . "/" . $year, $html);

        $host = $this->tenant_data->domain;
        $html = str_replace("{{Campus}}", strtoupper($host), $html);
        return $html;
    }

    private function getDatesWithDayNames($month, $year)
    {
        // dummy values if invalid
        if ($month < 1 || $month > 12) {
            $month = 1;
        }
        if ($year < 2000) {
            $year = 2024;
        }

        $days = [];
        $totalDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        for ($d = 1; $d <= $totalDays; $d++) {

            try {
                $date = \Carbon\Carbon::create($year, $month, $d);
            } catch (\Exception $e) {
                continue;
            }

            $days[] = [
                'day' => $d,
                'dayName' => strtolower($date->format('l'))
            ];
        }

        return $days;
    }

    public function feeSummaryHeadWiseReport($request)
    {
        $month    = $request->month;
        $year     = $request->year;
        $tenantId = tenant('id');

        $innerQuery = DB::table('students as STD')
            ->join('classes as CLS', 'STD.ClassId', '=', 'CLS.id')
            ->join('sectiones as SEC', 'SEC.id', '=', 'STD.SectionId')
            ->join('generate_fee_challan as GCC', 'STD.id', '=', 'GCC.StudentId')
            ->join('challan_transactions as CT', 'GCC.id', '=', 'CT.generate_challan_id')
            ->leftJoin('fees_type as FT', 'CT.FKey', '=', 'FT.id')
            ->join('lms_sessions as SES', 'GCC.SessionId', '=', 'SES.id')
            ->where('STD.IsActive', 1)
            ->where('STD.tenant_id', $tenantId)
            ->where('GCC.tenant_id', $tenantId)
            ->where('GCC.IsActive', 1)
            ->whereNull('GCC.deleted_at')
            ->whereMonth('GCC.ChallanMonth', $month)
            ->whereYear('GCC.ChallanMonth', $year)
            ->select([
                'CLS.id as ClassId',
                'CLS.ClassName as Class',
                'FT.FeeName as FeeType',
                DB::raw('UPPER(SEC.SectionName) as Section'),
                DB::raw('IFNULL(CT.BalanceFeeAfterDiscount, 0) as FeeAmount'),
                DB::raw("(SELECT IFNULL(SUM(
                    CASE WHEN ACD.TransactionType = 'Arrears' THEN
                        CASE WHEN GCC2.IsPartialPayment = 1 THEN
                            CT2.BalanceFeeAfterDiscount - IFNULL(PP.received_amount_sum, 0)
                        ELSE
                            CT2.BalanceFeeAfterDiscount
                        END
                    ELSE 0 END
                ), 0)
            FROM arrears_challan_details ACD
            LEFT JOIN generate_fee_challan GCC2 ON ACD.FKeyId = GCC2.id
            LEFT JOIN challan_transactions CT2 ON GCC2.id = CT2.generate_challan_id
            LEFT JOIN (
                SELECT GenerateClassChallanId, SUM(ReceivedAmount) AS received_amount_sum
                FROM challan_partial_payments
                WHERE IsActive = 1
                GROUP BY GenerateClassChallanId
            ) PP ON GCC2.id = PP.GenerateClassChallanId
            WHERE ACD.GenerateFeeChallanId = GCC.id
        ) as ArrearAmount"),
                'GCC.Status as Status'
            ]);

        $results = DB::table(DB::raw("({$innerQuery->toSql()}) as MQ"))
            ->mergeBindings($innerQuery)
            ->select([
                'MQ.ClassId',
                'MQ.Class',
                'MQ.Section',
                'MQ.FeeType',
                'MQ.Status',
                DB::raw('SUM(MQ.FeeAmount) as FeeAmount'),
                DB::raw('SUM(MQ.ArrearAmount) as ArrearAmount'),
            ])
            ->groupBy('MQ.ClassId', 'MQ.Class', 'MQ.Section', 'MQ.FeeType', 'MQ.Status')
            ->orderBy('MQ.ClassId')
            ->get();

        $summary = $results
            ->groupBy(fn($item) => strtolower(trim($item->FeeType ?? 'unknown')))
            ->map(function ($itemsByFeeType) {
                return $itemsByFeeType
                    ->groupBy(fn($item) => strtolower(trim($item->Status)))
                    ->map(function ($items) {
                        return [
                            'total_fee_amount'    => $items->sum(fn($i) => (float) $i->FeeAmount),
                            'total_arrear_amount' => $items->sum(fn($i) => (float) $i->ArrearAmount),
                            'total_records'       => $items->count(),
                        ];
                    });
            });

        $response  = $results->toArray();
        $finalData = [];
        $feeColumns = [];

        function normalizeFeeTypeDynamic($feeType)
        {
            $feeType = trim($feeType ?? 'Unknown');
            $feeType = preg_replace('/\s+/', ' ', $feeType);
            return ucwords(strtolower($feeType));
        }

        foreach ($response as $item) {

            $key     = $item->ClassId . '_' . strtolower(trim($item->Section));
            $feeType = normalizeFeeTypeDynamic($item->FeeType);

            if (!in_array($feeType, $feeColumns)) {
                $feeColumns[] = $feeType;
            }

            if (!isset($finalData[$key])) {
                $finalData[$key] = [
                    'Class Name'    => $item->Class,
                    'Section'       => $item->Section,
                    'Arrear Amount' => 0,
                ];
            }

            foreach ($feeColumns as $col) {
                if (!isset($finalData[$key][$col])) {
                    $finalData[$key][$col] = 0;
                }
            }

            $finalData[$key][$feeType] += (float) $item->FeeAmount;
            $finalData[$key]['Arrear Amount'] += (float) $item->ArrearAmount;
        }

        $finalData = array_values($finalData);

        // Totals row
        $totals = ['Class Name' => 'Total', 'Section' => ''];
        foreach ($feeColumns as $col) {
            $totals[$col] = 0;
        }
        $totals['Arrear Amount'] = 0;

        foreach ($finalData as $row) {
            foreach ($feeColumns as $col) {
                $totals[$col] += $row[$col] ?? 0;
            }
            $totals['Arrear Amount'] += $row['Arrear Amount'] ?? 0;
        }

        $finalData[] = $totals;

        $meta = [
            'campus' => $this->tenant_data->domain,
        ];

        return [
            'result'  => $finalData,
            'meta'    => $meta,
            'summary' => $summary,
        ];
    }

    // 03-26-2026 ( Comment Date ) yah code umar farooq ny comment kia hai es
    // main head sirf monthly wala e a raha tha 
    // public function feeSummaryHeadWiseReport($request)
    // {
    //     $month = $request->month;
    //     $year  = $request->year;
    //     $tenantId = tenant('id');

    //     $innerQuery = DB::table('students as STD')
    //         ->join('classes as CLS', 'STD.ClassId', '=', 'CLS.id')
    //         ->join('sectiones as SEC', 'STD.SectionId', '=', 'SEC.id')
    //         ->join('generate_fee_challan as GFC', 'STD.id', '=', 'GFC.StudentId')
    //         ->join('challan_transactions as CT', 'GFC.id', '=', 'CT.generate_challan_id')
    //         ->join('lms_sessions as SES', 'GFC.SessionId', '=', 'SES.id')
    //         ->where('STD.IsActive', 1)
    //         ->where('STD.tenant_id', $tenantId)
    //         ->where('GFC.tenant_id', $tenantId)
    //         ->where('GFC.IsActive', 1)
    //         ->whereNull('GFC.deleted_at')
    //         ->whereMonth('GFC.ChallanMonth', $month)
    //         ->whereYear('GFC.ChallanMonth', $year)
    //         ->select([
    //             'CLS.id as ClassId',
    //             'CLS.ClassName as Class',
    //             DB::raw('UPPER(SEC.SectionName) as Section'),

    //             DB::raw("(SELECT FT.FeeName 
    //                 FROM campus_fees_masters CFM
    //                 JOIN fees_type FT ON CFM.FeesTypeNId = FT.id
    //                 LIMIT 1) as FeeType"),
    //             DB::raw('IFNULL(CT.BalanceFeeAfterDiscount,0) as BalanceFeeAfterDiscount'),

    //             DB::raw("(SELECT IFNULL(SUM(
    //                     CASE WHEN ACD.TransactionType='Arrears' THEN 
    //                         CASE WHEN GFC2.IsPartialPayment=1 
    //                             THEN CT2.BalanceFeeAfterDiscount - IFNULL(PP.received_amount_sum,0)
    //                         ELSE CT2.BalanceFeeAfterDiscount 
    //                         END
    //                     ELSE 0 END),0)
    //                 FROM arrears_challan_details ACD
    //                 LEFT JOIN generate_fee_challan GFC2 ON ACD.FKeyId=GFC2.id
    //                 LEFT JOIN challan_transactions CT2 ON GFC2.id=CT2.generate_challan_id
    //                 LEFT JOIN (
    //                     SELECT GenerateClassChallanId, SUM(ReceivedAmount) AS received_amount_sum
    //                     FROM challan_partial_payments
    //                     WHERE IsActive=1
    //                     GROUP BY GenerateClassChallanId
    //                 ) PP ON GFC2.id=PP.GenerateClassChallanId
    //                 WHERE ACD.GenerateFeeChallanId=GFC.id
    //         ) as ArrearAmount"),

    //             'GFC.status as Status'
    //         ]);

    //     $results = DB::table(DB::raw("({$innerQuery->toSql()}) as MQ"))
    //         ->mergeBindings($innerQuery)
    //         ->select([
    //             'MQ.ClassId',
    //             'MQ.Class',
    //             'MQ.Section',
    //             'MQ.FeeType',
    //             'MQ.Status',
    //             DB::raw('SUM(MQ.BalanceFeeAfterDiscount) as BalanceFeeAfterDiscount'),
    //             DB::raw('SUM(MQ.ArrearAmount) as ArrearAmount'),
    //         ])
    //         ->groupBy('MQ.ClassId', 'MQ.Class', 'MQ.Section', 'MQ.FeeType', 'MQ.Status')
    //         ->orderBy('MQ.ClassId')
    //         ->get();

    //     $summary = $results
    //         ->groupBy(fn($item) => strtolower(trim($item->FeeType)))
    //         ->map(function ($itemsByFeeType) {
    //             return $itemsByFeeType
    //                 ->groupBy(fn($item) => strtolower(trim($item->Status)))
    //                 ->map(function ($items) {
    //                     return [
    //                         'total_fee_amount' => $items->sum(fn($i) => (float) $i->BalanceFeeAfterDiscount),
    //                         'total_arrear_amount' => $items->sum(fn($i) => (float) $i->ArrearAmount),
    //                         'total_records' => $items->count(),
    //                     ];
    //                 });
    //         });

    //     $response = $results->toArray();
    //     $finalData = [];
    //     $feeColumns = [];

    //     function normalizeFeeTypeDynamic($feeType)
    //     {
    //         $feeType = trim($feeType);
    //         $feeType = preg_replace('/\s+/', ' ', $feeType);
    //         return ucwords(strtolower($feeType));
    //     }

    //     foreach ($response as $item) {

    //         $key = $item->ClassId;
    //         $feeType = normalizeFeeTypeDynamic($item->FeeType);

    //         if (!in_array($feeType, $feeColumns)) {
    //             $feeColumns[] = $feeType;
    //         }

    //         if (!isset($finalData[$key])) {
    //             $finalData[$key] = [
    //                 'Class Name' => $item->Class,
    //                 'Section' => $item->Section,
    //                 'Arrear Amount' => 0
    //             ];
    //         }

    //         foreach ($feeColumns as $col) {
    //             if (!isset($finalData[$key][$col])) {
    //                 $finalData[$key][$col] = 0;
    //             }
    //         }
    //         $finalData[$key][$feeType] += (float) $item->BalanceFeeAfterDiscount;
    //         $finalData[$key]['Arrear Amount'] += (float) $item->ArrearAmount;
    //     }

    //     $finalData = array_values($finalData);
    //     $totals = ['Class Name' => 'Total', 'Section' => ''];

    //     foreach ($feeColumns as $col) {
    //         $totals[$col] = 0;
    //     }

    //     $totals['Arrear Amount'] = 0;

    //     foreach ($finalData as $row) {
    //         foreach ($feeColumns as $col) {
    //             $totals[$col] += $row[$col] ?? 0;
    //         }
    //         $totals['Arrear Amount'] += $row['Arrear Amount'] ?? 0;
    //     }

    //     $finalData[] = $totals;

    //     $meta = [
    //         'campus' => $this->tenant_data->domain,
    //     ];

    //     return [
    //         'result' => $finalData,
    //         'meta' => $meta,
    //         'summary' => $summary,
    //     ];
    // }

    public function studentInformationFetch($request)
    {
        $isActive = ((string)$request->isActive === 'false') ? 0 : 1;
        $query = Student::query();
        if ($isActive == 1) {
            $query->where('IsActive', $isActive);
        }else{
            $query->where('IsActive', 0);
        }

        $query->where('tenant_id', tenant('id'))
            ->with(['class', 'section'])
            ->when($request->ClassId, fn($q) => $q->where('ClassId', $request->ClassId))
            ->when($request->SectionId, fn($q) => $q->where('SectionId', $request->SectionId));

        $students = $query->orderBy('AdmissionDate', 'desc')
            ->get([
                'id',
                'AdmissionDate',
                'RollNumber',
                'FirstName',
                'LastName',
                'Gender',
                'FatherName',
                'MobileNumber',
                'ClassId',
                'SectionId'
            ]);
        $meta = [
            'campus' =>  $this->tenant_data->domain,
        ];

        $data = [
            'students' => $students,
            'meta' => $meta,
        ];
        return $data;
    }

    public function feeCollectionReportFetch($request)
    {
        $month = $request->month;
        $startDate = $month . '-01';
        $endDate = \Carbon\Carbon::parse($startDate)->endOfMonth()->toDateString();

        $GenerateFeeChallan = GenerateFeeChallan::select(
            'id',
            'tenant_id',
            'challan_no',
            'IsActive',
            'SessionId',
            'StudentId',
            'ChallanMonth',
            'SubmitDate',
            'IsPartialPayment',
            'Status',
            'FineAmount',
            'WaivedFineAmount'
        )
            ->with('StudentRel.class', 'StudentRel.section')
            ->with(['partialPayments' => function ($q) {
                $q->select('id', 'IsActive', 'tenant_id', 'GenerateClassChallanId', 'ReceivedAmount', 'CollectDate', 'PaymentMode', 'SubmitDate')
                    ->where('IsActive', true)
                    ->where('tenant_id', tenant('id'));
            }])
            ->withSum('transection', 'BalanceFeeAfterDiscount')
            ->whereBetween('ChallanMonth', [$startDate, $endDate])
            ->where('tenant_id', tenant('id'))
            ->where('IsActive', 1)
            ->get();


        // $GenerateFeeChallan = $GenerateFeeChallan->map(function ($item) {
        //     // transection sum + fine ko replace ya naya key bana den
        //     $item['transection_sum_balancefeeafterdiscount'] += $item['FineAmount'];
        //     return $item;
        // });

        $grouped = $GenerateFeeChallan
                ->groupBy(function ($item) {
                    return $item->StudentRel->class->ClassName ?? 'Unknown';
                })->map(function ($classGroup) {
                    return $classGroup->groupBy(function ($item) {
                        return $item->StudentRel->section->SectionName ?? 'Unknown Section';
                    });
                });
        // dd($grouped->toArray());
        $meta = [
            'campus' =>  $this->tenant_data->domain,
        ];

        $data = [
            'GenerateFeeChallan' => $grouped,
            'meta' => $meta,
        ];
        return $data;
    }
    
    public function feeCollectionReportSummary($request)
    {
        $month = $request->month;
        $startDate = $month . '-01';
        $endDate = \Carbon\Carbon::parse($startDate)->endOfMonth()->toDateString();
        $GenerateFeeChallan = GenerateFeeChallan::select(
            'id',
            'tenant_id',
            'challan_no',
            'IsActive',
            'SessionId',
            'StudentId',
            'ChallanMonth',
            'SubmitDate',
            'IsPartialPayment',
            'Status',
            'FineAmount',
            'ClassId',
            'SectionId',
            'WaivedFineAmount'
        )
            // ->with('StudentRel.class', 'StudentRel.section')
            ->with('ClassRel', 'SectionRel')
            ->with(['partialPayments' => function ($q) {
                $q->select('id', 'IsActive', 'tenant_id', 'GenerateClassChallanId', 'ReceivedAmount', 'CollectDate', 'PaymentMode', 'SubmitDate')
                    ->where('IsActive', true)
                    ->where('tenant_id', tenant('id'));
            }])
            ->withSum(
                ['partialPayments as partial_paid_amount' => function ($q) {
                    $q->where('IsActive', true)
                        ->where('tenant_id', tenant('id'));
                }],
                'ReceivedAmount'
            )
            ->with('ChallanArrearsRel.arrear_challan_fine')
            ->with('challanArrearsSum')
            ->withSum('transection', 'BalanceFeeAfterDiscount')
            ->whereBetween('ChallanMonth', [$startDate, $endDate])
            ->where('tenant_id', tenant('id'))
            ->where('IsActive', 1)
            // ->where('challan_no', 350)
            // ->whereIn('id', [106039])
            ->get();

        // dd($GenerateFeeChallan->toArray());

        foreach ($GenerateFeeChallan as $challan) {
            $challan->total_arrear_fine = 0;
            foreach ($challan->ChallanArrearsRel as $arrearFine) {
                if($arrearFine->TransactionType === 'Fine'){
                    $fineAmount = $arrearFine['arrear_challan_fine']['FineAmount'];
                    $challan->total_arrear_fine += $fineAmount;
                }else{



                if( !empty($arrearFine['arrear_challan_fine']) && $arrearFine['arrear_challan_fine']['per_day_fine']) {
                    $expiryDate = Carbon::parse($arrearFine['arrear_challan_fine']['ExpiryDate']);
                    $dueDate    = Carbon::parse($arrearFine['arrear_challan_fine']['DueDate']);
                    $perDayFine = $arrearFine['arrear_challan_fine']['per_day_fine'];
                    $days = $dueDate->diffInDays($expiryDate); 
                    $challan->total_arrear_fine += $days * $perDayFine;
                } else if( !empty($arrearFine['arrear_challan_fine']) && $arrearFine['arrear_challan_fine']['FineAmount']) {
                    $challan->total_arrear_fine += $arrearFine['arrear_challan_fine']['FineAmount'];
                }


                    // if($arrearFine['arrear_challan_fine']['per_day_fine']){
                    //     $expiryDate = Carbon::parse($arrearFine['arrear_challan_fine']['ExpiryDate']);
                    //     $dueDate    = Carbon::parse($arrearFine['arrear_challan_fine']['DueDate']);
                    //     $perDayFine = $arrearFine['arrear_challan_fine']['per_day_fine'];
                    //     $days = $dueDate->diffInDays($expiryDate); 
                    //     $challan->total_arrear_fine += $days * $perDayFine;
                    // }else{
                    //     $challan->total_arrear_fine += $arrearFine['arrear_challan_fine']['FineAmount'];
                    // }
                } 
            }
        }


        $GenerateFeeChallan = $GenerateFeeChallan->map(function ($item) {
            $arrearsSum = $item->challanArrearsSum
                ->sum(fn($row) => (int) $row->arrear_challan_transaction_sum_balancefeeafterdiscount);
            // dd($item->toArray());
            $partialPaidSum = $item->challanArrearsSum
                ->flatMap(function ($row) {
                    return $row->challan_partial_payment ?? [];
                })
                ->sum(function ($payment) {
                    return (float) $payment['ReceivedAmount'];
                });
            // $arrearsWiveOffAmount = $item->ChallanArrearsRel->sum(fn($row) => (int) $row->arrear_challan_fine['WaivedFineAmount']);

            $arrearsWiveOffAmount = $item->ChallanArrearsRel
            ->filter(function ($row) {
                return $row->arrear_challan_fine['Status'] !== 'Paid';
            })
            ->sum(function ($row) {
                return (int) ($row->arrear_challan_fine['WaivedFineAmount'] ?? 0);
            });



            $item->total_arrears_amount = $arrearsSum;
            $item->arrear_partial_sum  = $partialPaidSum;
            $item->total_waive_off_amount = $arrearsWiveOffAmount + $item->WaivedFineAmount;


            return $item;
        });

        // dd($GenerateFeeChallan->toArray());
        // $grouped = $GenerateFeeChallan->groupBy(function ($item) {

        //     return $item->StudentRel->class->ClassName ?? 'Unknown';
        // });

        $grouped = $GenerateFeeChallan
            ->groupBy(function ($item) {
                return $item->ClassRel->ClassName ?? 'Unknown Class';
            })
            ->map(function ($classGroup) {
                return $classGroup->groupBy(function ($item) {
                    return $item->SectionRel->SectionName ?? 'Unknown Section';
                });
            });

        // dd($grouped->toArray());


        $meta = [
            'campus' =>  $this->tenant_data->domain,
        ];

        $data = [
            'GenerateFeeChallan' => $grouped,
            'meta' => $meta,
        ];
        return $data;
    }

    public function dailyFeeCollection($request)
    {
        $month = $request->month;
        $GenerateFeeChallan = GenerateFeeChallan::select(
            'id',
            'tenant_id',
            'challan_no',
            'IsActive',
            'SessionId',
            'StudentId',
            'ChallanMonth',
            'SubmitDate',
            'IsPartialPayment',
            'Status',
            'FineAmount',
            'WaivedFineAmount'
        )
            ->with('StudentRel.class', 'StudentRel.section')
            ->with(['partialPayments' => function ($q) {
                $q->select('id', 'IsActive', 'tenant_id', 'GenerateClassChallanId', 'ReceivedAmount', 'CollectDate', 'PaymentMode', 'SubmitDate')
                    ->where('IsActive', true)
                    ->where('tenant_id', tenant('id'));
            }])
            ->withSum(
                ['partialPayments as partial_paid_amount' => function ($q) {
                    $q->where('IsActive', true)
                        ->where('tenant_id', tenant('id'));
                }],
                'ReceivedAmount'
            )
            ->with('ChallanArrearsRel.arrear_challan_fine')
            ->with('challanArrearsSum')
            ->withSum('transection', 'BalanceFeeAfterDiscount')
            ->where('SubmitDate', $month)
            ->where(function ($q) {
                $q->where(function ($q) {
                    $q->where('Status', 'Paid')
                    ->where('IsPartialPayment', 0);
                })->orWhere(function ($q) {
                    $q->where('Status', 'Unpaid')
                    ->where('IsPartialPayment', 1);
                });
            })
            // ->where('Status', 'Paid')
            ->where('tenant_id', tenant('id'))
            ->where('IsActive', 1)
            ->get();
        // dd($GenerateFeeChallan->toArray());
        foreach ($GenerateFeeChallan as $challan) {
            $challan->total_arrear_fine = 0;
            foreach ($challan->ChallanArrearsRel as $arrearFine) {
                if ($arrearFine->TransactionType === 'Fine') {
                    $fineAmount = $arrearFine['arrear_challan_fine']['FineAmount'];
                    $challan->total_arrear_fine += $fineAmount;
                } else {
                    if (!empty($arrearFine['arrear_challan_fine']) && $arrearFine['arrear_challan_fine']['per_day_fine']) {
                        $expiryDate = Carbon::parse($arrearFine['arrear_challan_fine']['ExpiryDate']);
                        $dueDate = Carbon::parse($arrearFine['arrear_challan_fine']['DueDate']);
                        $perDayFine = $arrearFine['arrear_challan_fine']['per_day_fine'];
                        $days = $dueDate->diffInDays($expiryDate);
                        $challan->total_arrear_fine += $days * $perDayFine;
                    } else if (!empty($arrearFine['arrear_challan_fine']) && $arrearFine['arrear_challan_fine']['FineAmount']) {
                        $challan->total_arrear_fine += $arrearFine['arrear_challan_fine']['FineAmount'];
                    }
                }
            }
        }

        $GenerateFeeChallan = $GenerateFeeChallan->map(function ($item) {
            $arrearsSum = $item->challanArrearsSum
                ->sum(fn($row) => (int) $row->arrear_challan_transaction_sum_balancefeeafterdiscount);

            $partialPaidSum = $item->challanArrearsSum
                ->flatMap(function ($row) {
                    return $row->challan_partial_payment ?? [];
                })
                ->sum(function ($payment) {
                    return (float) $payment['ReceivedAmount'];
                });

            $item->total_arrears_amount = $arrearsSum;
            $item->arrear_partial_sum = $partialPaidSum;

            return $item;
        });

        $grouped = $GenerateFeeChallan
            ->groupBy(function ($item) {
                return $item->StudentRel->class->ClassName ?? 'Unknown Class';
            })
            ->map(function ($classGroup) {
                return $classGroup->groupBy(function ($item) {
                    return $item->StudentRel->section->SectionName ?? 'Unknown Section';
                });
            });

        $meta = [
            'campus' =>  $this->tenant_data->domain,
        ];

        $data = [
            'GenerateFeeChallan' => $grouped,
            'meta' => $meta,
        ];
        return $data;
    }

    public function studentFeebalance($request)
    {

        $tenant_id = tenant('id');
        if (session('switched_tenant_id')) {
            $tenant_id = session('switched_tenant_id');
        }
        $result = DB::table('students as st')
            ->select([
                'c.ClassName',
                's.SectionName',
                'st.FirstName as StudentName',
                'st.LastName as StudentLastName',
                'st.FatherName',
                'st.FatherPhone',
                'st.MotherPhone',
                'student_unpaid.TotalUnpaidPerStudent'
            ])
            ->joinSub(
                DB::table('generate_fee_challan as gfc')
                    ->select(
                        'gfc.StudentId',
                        DB::raw('SUM(ct.BalanceFeeAfterDiscount) as TotalUnpaidPerStudent')
                    )
                    ->joinSub(
                        DB::table('generate_fee_challan')
                            ->select(
                                'StudentId',
                                DB::raw('MAX(ChallanMonth) as latest_unpaid_month')
                            )
                            ->where('Status', 'Unpaid')
                            ->where('deleted_at', null)
                            ->groupBy('StudentId'),
                        'latest',
                        function ($join) {
                            $join->on('latest.StudentId', '=', 'gfc.StudentId')
                                ->on('latest.latest_unpaid_month', '=', 'gfc.ChallanMonth');
                        }
                    )
                    ->join('challan_transactions as ct', 'ct.generate_challan_id', '=', 'gfc.id')
                    ->where('gfc.Status', 'Unpaid')
                    ->where('gfc.tenant_id', $tenant_id)
                    ->groupBy('gfc.StudentId'),
                'student_unpaid',
                'student_unpaid.StudentId',
                '=',
                'st.id'
            )

            ->join('classes as c', 'c.id', '=', 'st.ClassId')
            ->join('sectiones as s', 's.id', '=', 'st.SectionId')

            ->when($request->ClassId, function ($q) use ($request) {
                $q->where('st.ClassId', $request->ClassId);
            })
            ->when($request->SectionId, function ($q) use ($request) {
                $q->where('st.SectionId', $request->SectionId);
            })

            ->orderBy('c.ClassName', 'DESC')
            ->get();

        $meta = [
            'campus' =>  $this->tenant_data->domain,
            'date' =>  Carbon::now()->format('d-m-Y'),
        ];

        $data = [
            'studentBalance' => $result,
            'meta' => $meta,
        ];
        return $data;
    }


    public function schoolFeeLedgerReport($validated)
    {
        $fromDate = Carbon::parse($validated['start_date']);
        $toDate = Carbon::parse($validated['end_date']);

        $monthList = [];
        $pointer = $fromDate->copy()->startOfMonth();
        $endMonth = $toDate->copy()->startOfMonth();
        while ($pointer->lessThanOrEqualTo($endMonth)) {
            $monthList[] = $pointer->format('Y-m');
            $pointer->addMonth();
        }

        $students = Student::query()
            ->where('tenant_id', tenant('id'))
            ->where('ClassId', $validated['ClassId'])
            ->when(!empty($validated['SectionId']), fn($q) => $q->where('SectionId', $validated['SectionId']))
            ->with(['class', 'section'])
            ->get(['id', 'RollNumber', 'FirstName', 'LastName', 'ClassId', 'SectionId']);

        $studentIds = $students->pluck('id')->toArray();
        $challans = GenerateFeeChallan::query()
            ->where('tenant_id', tenant('id'))
            ->where('IsActive', 1)
            ->whereIn('StudentId', $studentIds)
            ->whereDate('ChallanMonth', '>=', $fromDate->toDateString())
            ->whereDate('ChallanMonth', '<=', $toDate->toDateString())
            ->with(['partialPayments', 'StudentRel.class', 'StudentRel.section'])
            ->withSum('transection', 'BalanceFeeAfterDiscount')
            ->get([
                'id',
                'StudentId',
                'ChallanMonth',
                'Status',
                'FineAmount',
                'WaivedFineAmount',
                'IsPartialPayment'
            ]);

        $studentData = [];
        foreach ($students as $student) {
            $studentData[$student->id] = [  
                'sr_no' => null,
                'roll_number' => $student->RollNumber,
                'student_name' => trim($student->FirstName . ' ' . $student->LastName),
                'class' => optional($student->class)->ClassName ?? null,
                'section' => optional($student->section)->SectionName ?? null,
                'monthly' => array_fill_keys($monthList, ['paid' => 0.0, 'waived' => 0.0, 'pending' => 0.0, 'receivable' => 0.0]),
                'total_received' => 0.0,
                'total_waived_fine_amount' => 0.0,
                'total_pending' => 0.0,
                'total_receivable' => 0.0,
            ];
        }

        foreach ($challans as $challan) {
            if (!isset($studentData[$challan->StudentId])) {
                continue;
            }

            $monthKey = Carbon::parse($challan->ChallanMonth)->format('Y-m');
            if (!in_array($monthKey, $monthList)) {
                continue;
            }

            $total_fee = round((float) ($challan->transection_sum_balancefeeafterdiscount ?? 0), 2);
            $waived = round((float) ($challan->WaivedFineAmount ?? 0), 2);
            $partial_sum = round((float) $challan->partialPayments->sum('ReceivedAmount'), 2);
            $paid = $partial_sum;
            if (strtolower((string) $challan->Status) === 'paid' && $paid == 0.0) {
                // $paid = max($total_fee - $waived, 0);
                $paid = max($total_fee, 0);
            }
            $remaining = max($total_fee - $paid, 0);
            // $remaining = max($total_fee - $waived - $paid, 0);

            $studentData[$challan->StudentId]['monthly'][$monthKey]['paid'] += round($paid, 2);
            $studentData[$challan->StudentId]['monthly'][$monthKey]['pending'] += round($remaining, 2);
            $studentData[$challan->StudentId]['monthly'][$monthKey]['receivable'] += round($total_fee, 2);
            $studentData[$challan->StudentId]['monthly'][$monthKey]['waived'] += round($waived, 2);

            $studentData[$challan->StudentId]['total_received'] += round($paid, 2);
            $studentData[$challan->StudentId]['total_waived_fine_amount'] += round($waived, 2);
            $studentData[$challan->StudentId]['total_pending'] += round($remaining, 2);
            $studentData[$challan->StudentId]['total_receivable'] += round($total_fee, 2);
        }

        $rows = [];
        $index = 1;
        foreach ($studentData as $studentId => $value) {
            $value['sr_no'] = $index;
            $row = [
                'sr_no' => $index,
                'roll_number' => $value['roll_number'],
                'student_name' => $value['student_name'],
                'class' => $value['class'],
                'section' => $value['section'],
            ];

            foreach ($monthList as $monthKey) {
                $m = $value['monthly'][$monthKey] ?? ['paid' => 0.0, 'pending' => 0.0, 'receivable' => 0.0, 'waived' => 0.0];
                $row[$monthKey] = [
                    'paid' => $m['paid'],
                    'pending' => $m['pending'],
                    'receivable' => $m['receivable'],
                    'waived' => $m['waived'],
                    'label' => ($m['receivable'] == 0 && $m['waived'] == 0 && $m['paid'] == 0 && $m['pending'] == 0)
                        ? null
                        : sprintf(
                            'T %s, W %s, P %s / U %s',
                            number_format($m['receivable'], 0, '.', ''),
                            number_format($m['waived'], 0, '.', ''),
                            number_format($m['paid'], 0, '.', ''),
                            number_format($m['pending'], 0, '.', '')
                        ),
                ];
            }

            $row['total_received'] = 'P ' . number_format((float) $value['total_received'], 2, '.', '');
            $row['total_waived_fine_amount'] = 'W ' . number_format((float) $value['total_waived_fine_amount'], 2, '.', '');
            $row['total_pending'] = 'U ' . number_format((float) $value['total_pending'], 2, '.', '');
            $row['total_receivable'] = 'T ' . number_format((float) $value['total_receivable'], 2, '.', '');

            $rows[] = $row;
            $index++;
        }

        // dd($rows);
        
        $meta = [
            'campus' => $this->tenant_data->domain,
            'from_date' => $validated['start_date'],
            'to_date' => $validated['end_date'],
            'class_id' => $validated['ClassId'],
            'section_id' => $validated['SectionId'] ?? null,
        ];
        
        
        return [
            'meta' => $meta,
            'months' => $monthList,
            'data' => $rows,
        ];
    }

    public function studentWithdrawReportFetch($request)
    {
        $query = Student::query()
            ->where('tenant_id', tenant('id'))
            ->where('withdraw_status','Approved')
            ->with(['class', 'section'])
            ->when($request->ClassId, fn($q) => $q->where('ClassId', $request->ClassId))
            ->when($request->SectionId, fn($q) => $q->where('SectionId', $request->SectionId))
            ->orderBy('id', 'desc')
            ->get([
                'id',
                'AdmissionDate',
                'RollNumber',
                'FirstName',
                'LastName',
                'Gender',
                'MobileNumber',
                'ClassId',
                'SectionId',
                'withdraw_status',
                'withdraw_date',
                'last_challan_no',
                'withdraw_reason',
                'last_challan_amount',
            ]);

        $meta = [
            'campus' => $this->tenant_data->domain,
            'class_name' => $query->first()?->class->ClassName ?? '',
            'section_name' => $query->first()?->section->SectionName ?? '',
        ];
        $data = [
            'students' => $query,
            'meta' => $meta,
        ];
        return $data;
    }
}
