<?php

namespace App\Services\Exam;

use App\Models\Classes;
use App\Models\ExamMarks;
use App\Models\ExamStudent;
use App\Models\ExamSubject;
use App\Models\ExamType;
use App\Models\Subject;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ExamSubjectService
{
    public function index($request): array
    {
        $query = ExamSubject::where('tenant_id', tenant('id'))
            ->with('ExamType', 'Subject.classes');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('Title', 'like', "%{$search}%")
                    ->orWhereHas('ExamType', function ($sub) use ($search) {
                        $sub->where('ExamName', 'like', "%{$search}%");
                    })
                    ->orWhereHas('Subject', function ($sub) use ($search) {
                        $sub->where('SubjectName', 'like', "%{$search}%");
                    })
                    ->orWhereHas('Subject.classes', function ($sub) use ($search) {
                        $sub->where('ClassName', 'like', "%{$search}%");
                    });
            });
        }

        $data['ExamSubject'] = $query->orderBy('id', 'desc')->paginate(25)->withQueryString();

        return $data;
    }

    public function create(): array
    {
        $data['examTypes'] = ExamType::where('tenant_id', tenant('id'))->where('SessionId', fetchCurrentSession()->id)->get();
        $data['Classes'] = Classes::where('tenant_id', tenant('id'))->where('IsActive', 1)->get();
        $data['Subjects'] = Subject::where('tenant_id', tenant('id'))->get();

        return $data;
    }

    public function submit($request): void
    {
        $currentSession = fetchCurrentSession();
        $insertArray = [];

        foreach ($request->rows as $key => $row) {
            $exists = ExamSubject::where('tenant_id', tenant('id'))
                ->where('SessionId', $currentSession['id'])
                ->where('ExamId', $request->ExamId)
                ->where('ClassId', $request->ClassId)
                ->where('SubjectId', $row['SubjectId'])
                // ->where('Date', $row['Date'])
                ->exists();
            if ($exists) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "rows.$key.Date" => "An exam is already scheduled for this class on {$row['Date']}.",
                ]);
            }
            $insertArray[$key] = [
                'tenant_id' => tenant('id'),
                'SessionId' => $currentSession['id'],
                'Title' => $request->Title,
                'ExamId' => $request->ExamId,
                'ClassId' => $request->ClassId,
                'SubjectId' => $row['SubjectId'],
                'Date' => $row['Date'],
                'Time' => $row['Time'],
                'Duration' => $row['Duration'],
                'CreditHours' => $row['CreditHours'],
                'RoomNo' => $row['RoomNo'],
                'MarksMax' => $row['MarksMax'],
                'MarksMin' => $row['MarksMin'],
                'CreatedBy' => auth()->id(),
            ];
        }
        ExamSubject::insert($insertArray);
    }

    public function update($request): void
    {
        $currentSession = fetchCurrentSession();
        // $request->validate([
        //     'ClassId' => [
        //         'required',
        //         Rule::unique('exam_subjects')
        //             ->where(function ($query) use ($request, $currentSession) {
        //                 return $query->where('SessionId', $currentSession['id'])
        //                             ->where('Date', $request->Date)
        //                             ->where('tenant_id', tenant('id'));
        //             })
        //             ->ignore($request->id), // ignore the current record
        //     ],
        // ]);

        $request->validate([
            'ClassId' => [
                'required',
                Rule::unique('exam_subjects', 'ClassId')
                    ->where(function ($query) use ($request, $currentSession) {
                        return $query->where('SessionId', $currentSession['id'])
                            ->where('Date', $request->Date)
                            ->where('tenant_id', tenant('id'));
                    })
                    ->ignore($request->ClassId, 'ClassId'), // 👈 MAIN FIX
            ],
        ]);

        // update logic
        $examSubject = ExamSubject::findOrFail($request->id);
        $examSubject->update($request->all());
    }

    public function delete($request)
    {
        $exam_student = ExamStudent::where('ExamSubjectId', $request['id'])->where('tenant_id', tenant('id'))->first();
        $exam_marks = ExamMarks::where('ExamSubjectId', $request['id'])->where('tenant_id', tenant('id'))->first();

        // ✅ agar koi exist kare → validation error
        if ($exam_student || $exam_marks) {
            throw ValidationException::withMessages([
                'delete_error' => ['Please delete related Exam Students or Exam Marks first.'],
            ]);
        }
        $exam_subject = ExamSubject::findOrFail($request['id']);
        $exam_subject->delete();
    }
}
