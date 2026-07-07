<?php

namespace App\Services\Reports;

use App\Models\ExamGrade;
use App\Models\ExamStudent;
use App\Models\ExamSubject;
use App\Models\ExamTerm;
use App\Models\ExamType;
use App\Models\LmsSession;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ResultCardService
{
    public function resultSheet(): array
    {
        $data['Session'] = fetchCurrentSession();
        $data['ExamTerms'] = ExamTerm::where('IsActive', 1)->get();
        $data['Exams'] = ExamType::where('IsActive', 1)->where('SessionId', fetchCurrentSession()?->id)->where('tenant_id', tenant('id'))->get(['id', 'ExamName', 'IsActive']);
        $data['Students'] = Student::where('tenant_id', tenant('id'))->where('IsDisable', 0)->get(['id', 'tenant_id', 'FirstName', 'LastName', 'ClassId', 'RollNumber']);
        return $data;
    }

    public function resultCard($request, $allterms): array
    {
        $tenantId = tenant('id');
        $classId = $request['classid'];
        $examId = $request['examid'];
        $studentId = $request['studentid'];
        $sessionId = $request['session'];
        $session_id = is_array($sessionId) ? ($sessionId['id'] ?? $sessionId) : $sessionId;
        $examTermId = $request['examtermid'];

        $data['ExamStudents'] = ExamStudent::where('tenant_id', $tenantId)
            ->where('ClassId', $classId)
            ->where('ExamId', $examId)
            ->where('StudentId', $studentId)
            ->with([
                'Subject.ExamType',
                'Subject.Subject',
                'Subject.Class.assignedTeacher' => function ($q) use ($tenantId) {
                    $q->where('tenant_id', $tenantId)
                    ->with(['StaffRel' => fn($q) => $q->where('tenant_id', $tenantId)]);
                },
                'Student.ExamMarksDetails.examMarks.ExamSubject.Subject'
            ])
            ->first();

        $allterms = filter_var($allterms, FILTER_VALIDATE_BOOLEAN);
        $baseExamQuery = ExamType::where('tenant_id', $tenantId);
        $exam_final_term = ExamTerm::where('id', $examTermId)->where('ExamTermName', 'LIKE', '%final%')->first();
        
        if ($allterms && $exam_final_term) {
            // Get all exam IDs for the term
            $allexamtermsIds = (clone $baseExamQuery)
                ->where('ExamTermId', $examTermId)
                ->where('SessionId', $sessionId['id'])
                ->pluck('id');

            // dd($allexamtermsIds->toArray());

            $examTermsids_from_exam_table = (clone $baseExamQuery)
                ->where('SessionId', $sessionId['id'])
                ->pluck('ExamTermId')
                ->unique();
            
            // dd($examTermsids_from_exam_table);

            $examTermsids_without_final = ExamTerm::whereIn('id', $examTermsids_from_exam_table)
                ->where('ExamTermName', 'NOT LIKE', '%final%')
                ->pluck('id');


            $examids_without_final_term = (clone $baseExamQuery)
                ->where('SessionId', $session_id)
                ->whereIn('ExamTermId', $examTermsids_without_final)
                ->pluck('id');

            
            // dd($examids_without_final_term->toArray(), $examTermsids_without_final->toArray());
            // dump($examids_without_final_term, $classId, $tenantId, $sessionId['id']);
            // Get exam subjects without final term with marks calculation
            $exam_subjects_without_final = ExamSubject::whereIn('ExamId', $examids_without_final_term)
                            ->where('tenant_id', $tenantId)
                            ->where('SessionId', $sessionId['id'])
                            ->where('ClassId', $classId)
                            ->with(['Class', 'Subject', 'ExamType'])
                            ->with(['exammarks' => function ($q) use ($studentId) {
                                $q->with(['examMarksDetails' => function ($query) use ($studentId) {
                                    $query->where('StudentId', $studentId);
                                }]);
                            }])
                            ->get()
                            ->map(function ($examSubject) use ($studentId) {
                                $totalMarks = 0;
                                $hasMarks = false; // ✅ Track karo ke marks mila ya nahi

                                if ($examSubject->exammarks) {

                                    if ($examSubject->exammarks instanceof \Illuminate\Support\Collection) {
                                        foreach ($examSubject->exammarks as $examMark) {
                                            if ($examMark->examMarksDetails && $examMark->examMarksDetails->isNotEmpty()) {
                                    foreach ($examMark->examMarksDetails as $detail) {
                                        if ($detail->StudentId == $studentId) {
                                            $totalMarks += (float)$detail->Marks;
                                            $hasMarks = true; // ✅ Mark mila
                                        }
                                    }
                                }
                            }
                        }

                        else if ($examSubject->exammarks instanceof \Illuminate\Database\Eloquent\Model) {
                            if ($examSubject->exammarks->examMarksDetails && $examSubject->exammarks->examMarksDetails->isNotEmpty()) {
                                foreach ($examSubject->exammarks->examMarksDetails as $detail) {
                                    if ($detail->StudentId == $studentId) {
                                        $totalMarks += (float)$detail->Marks;
                                        $hasMarks = true; // ✅ Mark mila
                                    }
                                }
                            }
                        }
                    }

                    $examSubject->total_marks = $totalMarks;
                    $examSubject->has_marks = $hasMarks; // ✅ Flag set karo

                    return $examSubject;
                })
                ->filter(function ($examSubject) {
                    return $examSubject->has_marks === true; // ✅ Sirf woh rakhao jinka marks hai
                })
                ->values(); // ✅ Array keys reset karo
            
            // dd($studentId, $exam_subjects_without_final->toArray());
            $sum_of_total_marks = $exam_subjects_without_final->sum('MarksMax') ;
            $sum_of_obtained_marks = $exam_subjects_without_final->sum('total_marks');

            // Get exam IDs excluding final term
            $examsWithoutFinalIds = (clone $baseExamQuery)
                ->where('ExamTermId', $examTermId)
                ->where('SessionId', $session_id)
                ->whereDoesntHave('examTerm', fn($q) => 
                    $q->whereRaw('LOWER(ExamName) LIKE ?', ['%final%'])
                )
                ->pluck('id');
        // dd($examTermId,$allexamtermsIds);
            // dd($examsWithoutFinalIds->toArray(), $examTermsids_without_final->toArray());
            // Fetch ExamSubjects for all terms and without finals
            $allTermSubjects = ExamSubject::whereIn('ExamId', $allexamtermsIds)
                // ->with(['Class', 'Subject', 'ExamType', 'exammarks.ExamMarksDetails'])


                 ->whereHas('exammarks', function ($q) use ($studentId) {
                    $q->whereHas('examMarksDetails', function ($query) use ($studentId) {
                        $query->where('StudentId', $studentId);
                    });
                })
                ->with(['Class', 'Subject', 'ExamType'])
                ->with(['exammarks' => function ($q) use ($studentId) {
                    $q->with(['examMarksDetails' => function ($query) use ($studentId) {
                        $query->where('StudentId', $studentId);
                    }]);
                }])





                ->where('tenant_id', $tenantId)
                ->where('SessionId', $sessionId['id'])
                ->where('ClassId', $classId)
                ->get();
            
                // yanha tak thak hai 
            // dd($allTermSubjects->toArray());
           
            $withoutFinalSubjects = ExamSubject::whereIn('ExamId', $examTermsids_without_final)
                ->with(['Class', 'Subject', 'ExamType', 'exammarks.ExamMarksDetails'])
                ->where('tenant_id', $tenantId)
                ->where('SessionId', $sessionId['id'])
                ->where('ClassId', $classId)
                ->get();
            //  dd($withoutFinalSubjects->toArray());
            $data['ExamSubjects'] = $allTermSubjects;
            $data['ExamsWithoutFinals'] = $withoutFinalSubjects;

            $data['total_marks_sum'] = $sum_of_total_marks;
            $data['obtained_marks_sum'] = $sum_of_obtained_marks;
            $data['term_wise_percentage'] = $sum_of_total_marks > 0
                ? round(($sum_of_obtained_marks / $sum_of_total_marks) * 100, 2)
                : 0;

            // $exam = (clone $baseExamQuery)
            //     ->where('ExamTermId', $examTermId)
            //     ->where('SessionId', $session_id)
            //     ->with('ExamSubjectRel')
            //     ->with('ExamSubjectRel.exammarks')
            //     ->get();

       

            $exam = (clone $baseExamQuery)
            ->where('ExamTermId', $examTermId)
            ->where('SessionId', $session_id)

            ->whereHas('ExamSubjectRel', function ($q) use ($classId) {
                $q->where('tenant_id', tenant('id'))
                ->where('ClassId', $classId)
                ->whereHas('exammarks');
            })

            ->with([
                'ExamSubjectRel' => function ($q) use ($classId) {
                    $q->where('tenant_id', tenant('id'))
                    ->where('ClassId', $classId)
                    ->whereHas('exammarks')
                    ->with('exammarks');
                }
            ])
            ->get();


            // dd($exam->toArray());
            // dd($data['ExamsWithoutFinals']->toArray());
        } else {
            $exam = (clone $baseExamQuery)
                ->where('id', $examId)
                ->get();

            $data['total_marks_sum'] = 0;
            $data['obtained_marks_sum'] = 0;
            $data['term_wise_percentage'] = 0;

            $data['ExamSubjects'] = ExamSubject::whereIn('ExamId', $exam->pluck('id'))
                ->with(['Class', 'Subject', 'ExamType', 'exammarks.ExamMarksDetails'])
                ->where('ClassId', $classId)
                ->get();
        }
        // Group subjects and build result rows
        $subjectGroups = $data['ExamSubjects']->groupBy('SubjectId');
        // dd($subjectGroups->toArray());
        $grandTotals = ['total_marks' => 0, 'obtained_marks' => 0];

        // dd($data);
        // Initialize checkpoint totals
        $checkpointTotals = $exam->mapWithKeys(fn($e) => [
            $e->id => ['total' => 0, 'obtained' => 0]
        ])->toArray();

        $final = $subjectGroups->map(function ($examSubjects) use ($exam, $studentId, &$grandTotals, &$checkpointTotals) {
            $firstSubject = $examSubjects->first();
            $row = ['Subject' => $firstSubject->Subject->SubjectName ?? 'N/A'];
            // dd($exam->toArray(), $examSubjects->toArray());
            $subjectTotalMarks = 0;
            $subjectObtainedMarks = 0;
            // dd($exam);
            foreach ($exam as $examType) {
                $examSubject = $examSubjects->firstWhere('ExamId', $examType->id);

                if ($examSubject) {
                    $obtainedMarks = 0;
                    
                    // SAFE ACCESS PATTERN - Check each level
                    if ($examSubject->exammarks) {
                        // Handle collection
                        if ($examSubject->exammarks instanceof \Illuminate\Support\Collection) {
                            foreach ($examSubject->exammarks as $em) {
                                if ($em->examMarksDetails && $em->examMarksDetails instanceof \Illuminate\Support\Collection) {
                                    $studentMark = $em->examMarksDetails->firstWhere('StudentId', $studentId);
                                    if ($studentMark) {
                                        $obtainedMarks += (float)$studentMark->Marks;
                                    }
                                }
                            }
                        } 
                        // Handle single model
                        else if ($examSubject->exammarks instanceof \Illuminate\Database\Eloquent\Model) {
                            if ($examSubject->exammarks->examMarksDetails && 
                                $examSubject->exammarks->examMarksDetails instanceof \Illuminate\Support\Collection) {
                                $studentMark = $examSubject->exammarks->examMarksDetails->firstWhere('StudentId', $studentId);
                                if ($studentMark) {
                                    $obtainedMarks += (float)$studentMark->Marks;
                                }
                            }
                        }
                    }

                    $row[$examType->ExamName . ' Total'] = $examSubject->MarksMax;
                    $row[$examType->ExamName . ' Obt'] = $obtainedMarks;

                    $subjectTotalMarks += $examSubject->MarksMax;
                    $subjectObtainedMarks += $obtainedMarks;

                    $checkpointTotals[$examType->id]['total'] += $examSubject->MarksMax;
                    $checkpointTotals[$examType->id]['obtained'] += $obtainedMarks;
                } else {
                    $row[$examType->ExamName . ' Total'] = 0;
                    $row[$examType->ExamName . ' Obt'] = 0;
                }
            }

            $percentage = $subjectTotalMarks > 0
                ? round(($subjectObtainedMarks / $subjectTotalMarks) * 100, 2)
                : 0;

            $row['Total Marks'] = $subjectTotalMarks;
            $row['Obtained Marks'] = $subjectObtainedMarks;
            $row['Percentage'] = $percentage . '%';
            $row['Grade'] = $this->getGrade($percentage, request('classid'));

            $grandTotals['total_marks'] += $subjectTotalMarks;
            $grandTotals['obtained_marks'] += $subjectObtainedMarks;

            return $row;
        })->values()->toArray();

        // dd($exam->toArray());
        // Grand total row
        $grandPercentage = $grandTotals['total_marks'] > 0
            ? round(($grandTotals['obtained_marks'] / $grandTotals['total_marks']) * 100, 2)
            : 0;

        $totalRow = collect($exam)->mapWithKeys(fn($e) => [
            $e->ExamName . ' Total' => $checkpointTotals[$e->id]['total'],
            $e->ExamName . ' Obt' => $checkpointTotals[$e->id]['obtained'], 
        ])->prepend('Total', 'Subject')
        ->put('Total Marks', $grandTotals['total_marks'])
        ->put('Obtained Marks', $grandTotals['obtained_marks'])
        ->put('Percentage', $grandPercentage . '%')
        ->put('Grade', $this->getGrade($grandPercentage, request('classid')))
        ->toArray();

        $final[] = $totalRow;

        $data['ResultData'] = $final;
        $data['ExamTypes'] = $exam;
        $data['AllTerms'] = $allterms;
        $data['Session'] = $sessionId;
        $data['isFinalTerm'] = $exam_final_term ? true : false;
        $data['Grads'] = ExamGrade::where('tenant_id', $tenantId)
            ->where('ClassId', (int)$classId)
            ->get();

        return $data;
    }

    private function getGrade($percentage, $class_id)
    {
        $grads = ExamGrade::where('tenant_id', tenant('id'))
            ->where('ClassId', (int)$class_id)
            ->get();
        
        $roundedPercentage = (int)round($percentage);
        foreach($grads as $grad){
            if($roundedPercentage >= $grad->PercentFrom && $roundedPercentage <= $grad->PercentUpt)
            {
                return $grad->GradeName;
            }
        }
        return 'N/A';
    }

    public function getExamStudents($request) : Collection 
    {
        $studentIds = ExamStudent::where('ExamId', $request['ExamId'])->where('ClassId', $request['ClassId'])->where('tenant_id', tenant('id'))->pluck('StudentId');
        return Student::whereIn('id', $studentIds)->where('tenant_id', tenant('id'))->get(['id', 'tenant_id','FirstName', 'LastName', 'ClassId', 'SectionId', 'SessionId']);
    }


}