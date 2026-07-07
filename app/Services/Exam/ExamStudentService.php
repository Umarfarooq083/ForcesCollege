<?php

namespace App\Services\Exam;

use App\Models\Classes;
use App\Models\ExamStudent;
use App\Models\ExamSubject;
use App\Models\ExamType;
use App\Models\Student;
use Illuminate\Support\Collection;

class ExamStudentService
{

    public function index($request): array
    {
        $query = ExamStudent::where('tenant_id', tenant('id'))->with('Subject.ExamType', 'Subject.Class', 'Subject.Subject', 'Student');

        if($request->filled('search'))
        {
            $search = $request->search;
            $query->where(function($q) use($search) {
                $q->orWhereHas('Subject.ExamType', function($sub) use($search) {
                    $sub->where('ExamName', 'like', "%{$search}%");
                })
                ->orWhereHas('Student', function($sub1) use($search) {
                    $sub1->whereRaw("CONCAT(FirstName, ' ', LastName) LIKE ?", ["%{$search}%"]);
                })
                ->orWhereHas('Subject.Class', function($sub2) use($search) {
                    $sub2->where('ClassName', 'like', "%{$search}%");
                })
                ->orWhereHas('Subject', function($sub3) use($search) {
                    $sub3->where('Title', 'like', "%{$search}%");
                });
            });
        }

        $data['ExamStudents'] = $query->paginate(25)->withQueryString();
        return $data;
    }

    public function create(): array
    {
        $data['Exams'] = ExamType::where('IsActive', 1)->where('tenant_id', tenant('id'))->where('SessionId', fetchCurrentSession()->id)->get(['id', 'ExamName', 'IsActive']);
        $campusClassList = campusClassList();
        $data['Classes'] = Classes::whereIn('class_type_id', $campusClassList)->get();
        $data['Students'] = Student::where('tenant_id', tenant('id'))->where('IsDisable', 0)->get(['id', 'tenant_id', 'FirstName', 'LastName', 'ClassId', 'IsDisable']);
        return $data;
    }

    public function submit($request): void 
    {
        $currentSession = fetchCurrentSession();
        $insertArray = [];
        foreach ($request->StudentIds as $key => $studentId) {
            $insertArray[$key] = [
                'tenant_id'     => tenant('id'),
                'SessionId'     => $currentSession['id'],
                'ExamId'   => $request->ExamId,
                'ClassId'      => $request->ClassId,   
                'ExamSubjectId' => $request->ExamSubjectId,
                'StudentId'    => $studentId,
            ];
        }
        ExamStudent::insert($insertArray);
    }

    public function getSubjectsByClass($request): array
    {  
        $mappedSubjects = ExamSubject::with('Subject:id,SubjectName')
                        ->where('ClassId', $request->ClassId)
                        ->where('ExamId', $request->ExamId)
                        ->where('tenant_id', tenant('id'))
                        ->get();

        $Students = Student::where('tenant_id', tenant('id'))->where('IsActive', 1)->where('ClassId', $request->ClassId)
            ->get(['id', 'tenant_id', 'FirstName', 'LastName', 'ClassId', 'RollNumber', 'IsDisable']);
       
        return [
                'subjects' => $mappedSubjects,
                'Students' => $Students
            ];
    }
}