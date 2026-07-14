<?php

namespace App\Services\Exam;

use App\Models\ExamMarks;
use App\Models\ExamMarksDetail;
use App\Models\ExamStudent;
use App\Models\ExamSubject;
use App\Models\ExamType;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class MarksService
{
    public function index($request): array
    {
        $query = ExamMarks::where('tenant_id', tenant('id'))
            ->with('ExamSubject.ExamType.examTerm', 'ExamSubject.Class', 'ExamSubject.Subject');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->orWhereHas('ExamSubject.ExamType', function ($sub) use ($search) {
                    $sub->where('ExamName', 'like', "%{$search}%");
                })
                    ->orWhereHas('ExamSubject.ExamType.examTerm', function ($sub) use ($search) {
                        $sub->where('ExamTermName', 'like', "%{$search}%");
                    })
                    ->orWhereHas('ExamSubject.Class', function ($sub) use ($search) {
                        $sub->where('ClassName', 'like', "%{$search}%");
                    })
                    ->orWhereHas('ExamSubject.Subject', function ($sub) use ($search) {
                        $sub->where('SubjectName', 'like', "%{$search}%");
                    });
            });
        }

        $data['ExamData'] = $query->orderBy('id', 'desc')->paginate(25)->withQueryString();

        return $data;
    }

    public function create(): array
    {
        // dd(fetchCurrentSession()->id);
        $data['Exams'] = ExamType::where('IsActive', 1)->where('tenant_id', tenant('id'))
            ->with('examStudents')
            ->where('SessionId', fetchCurrentSession()->id)
            // ->where('tenant_id', tenant('id'))
            ->get(['id', 'ExamName', 'IsActive']);

        return $data;
    }

    public function submit($validated, $request): void
    {
        $currentSession = fetchCurrentSession();

        $examMarks = [
            'tenant_id' => tenant('id'),
            'SchoolId' => null,
            'IsActive' => true,
            'IsDeleted' => false,
            'CreatedBy' => auth()->id(),
            'ModifiedBy' => null,
            'SessionId' => $currentSession['id'],
            'ExamSubjectId' => $validated['SubjectId'],
            'ExamMarksGroupId' => null,
        ];
        $exammarks = ExamMarks::Create($examMarks);

        $data = [];

        foreach ($validated['StudentsData'] as $student) {

            // Skip if Marks is null
            if (! array_key_exists('Marks', $student) || $student['Marks'] === null) {
                continue;
            }

            $data[] = [
                'ExamMarksId' => $exammarks->id,
                'StudentId' => $student['StudentId'],
                'ClassId' => $request['ClassId'],
                'tenant_id' => tenant('id') ?? null,
                'SchoolId' => auth()->user()->school_id ?? null,
                'SessionId' => $currentSession['id'] ?? null,
                'IsActive' => 1,
                'CreatedBy' => auth()->id(),
                'ModifiedBy' => auth()->id(),
                'Marks' => $student['Marks'] ?? null,
                'Remarks' => $student['Remarks'] ?? null,
            ];
        }

        DB::transaction(function () use ($data) {
            ExamMarksDetail::upsert(
                $data,
                ['ExamMarksId', 'StudentId'],
                ['tenant_id', 'SchoolId', 'SessionId', 'IsActive', 'CreatedBy', 'ModifiedBy', 'Marks', 'Remarks']
            );
        });
    }

    public function show($request): array
    {
        $data['ExamMarks'] = ExamMarks::where('tenant_id', tenant('id'))->where('id', $request['id'])
            ->with([
                'ExamSubject.Class',
                'ExamSubject.Subject',
                'ExamSubject.ExamType',
                'ExamMarksDetails' => function ($q) use ($request) {
                    $q->select('id', 'tenant_id', 'StudentId', 'ExamMarksId', 'Marks', 'Remarks')
                        ->whereHas('student', function ($sq) use ($request) {
                            $sq->where('ClassId', $request['ClassId']);
                        });
                },
                'ExamMarksDetails.student' => function ($q) use ($request) {
                    $q->where('ClassId', $request['ClassId'])
                        ->select('id', 'tenant_id', 'SectionId', 'ClassId', 'FirstName', 'LastName', 'RollNumber');
                },
            ])
            ->first();

        $data['Exams'] = ExamType::where('IsActive', 1)->where('tenant_id', tenant('id'))
            ->with('examStudents')
            ->get(['id', 'ExamName', 'IsActive']);

        return $data;
    }

    public function getMarksData($request): JsonResponse|ExamSubject|Collection|array
    {
        $query = ExamSubject::query()
            // ->where('tenant_id', tenant('id'))
            ->where('ExamId', $request->ExamId);

        if ($request->filled('ClassId')) {
            $query->where('ClassId', $request->ClassId);
        }

        if ($request->filled('SubjectId')) {
            $query->where('id', $request->SubjectId);
        }

        // Case 3: ExamId + ClassId + SubjectId → ek hi record
        if ($request->filled('ClassId') && $request->filled('SubjectId')) {

            $examSubject = $query->first();

            if (! $examSubject) {
                return response()->json([
                    'message' => 'Exam subject not found for this exam, class, and subject.',
                ], 404);
            }

            $exists = ExamMarks::where('ExamSubjectId', $examSubject->id)->exists();

            if ($exists) {
                return response()->json([
                    'message' => 'Marks already exist for this exam, class, and subject.',
                ], 409);
            }

            $Students = ExamStudent::where('tenant_id', tenant('id'))->where('ExamSubjectId', $request->SubjectId)->with('Student:id,tenant_id,FirstName,LastName,RollNumber,ClassId')->get()->pluck('Student');

            return [
                'subjects' => $examSubject,
                'Students' => $Students,
            ];

            return $examSubject;
        }

        if ($request->filled('ClassId')) {
            $examSubjects = $query->with('Subject')->get();
            $mappedSubjects = $examSubjects->map(function ($examSubject) {
                return [
                    'ExamSubject' => $examSubject,
                    'Subject' => $examSubject->Subject,
                ];
            })->unique(fn ($item) => $item['Subject']->id)->values();

            return [
                'subjects' => $mappedSubjects,
            ];
        }

        // Case 1: Sirf ExamId → Classes
        return $query->with('Class')
            ->get()
            ->pluck('Class')
            ->unique('id')
            ->values();
    }

    public function getEditData($classId, $examMarksId)
    {
        $tenantId = tenant('id');
        $currentSession = fetchCurrentSession();

        $examMarks = ExamMarks::where('tenant_id', $tenantId)
            ->where('id', $examMarksId)
            ->with([
                'ExamSubject.Class',
                'ExamSubject.Subject',
                'ExamSubject.ExamType',
            ])
            ->firstOrFail();

        $students = ExamStudent::where('tenant_id', $tenantId)
            ->with('student')
            ->where('ClassId', $classId)
            ->where('SessionId', $currentSession->id)
            ->where('ExamSubjectId', $examMarks->ExamSubjectId)
            ->get();

        $details = ExamMarksDetail::where('tenant_id', $tenantId)
            ->where('ExamMarksId', $examMarksId)
            ->get(['id', 'tenant_id', 'StudentId', 'ExamMarksId', 'Marks', 'Remarks'])
            ->keyBy('StudentId');

        $students = $students->map(function ($stu) use ($details) {
            $stu->marks = $details->get($stu->StudentId);

            return $stu;
        });

        $exams = ExamType::where('IsActive', 1)
            ->where('tenant_id', $tenantId)
            ->with('examStudents')
            ->get(['id', 'ExamName', 'IsActive']);

        return [
            'ExamMarks' => $examMarks,
            'Students' => $students,
            'Exams' => $exams,
        ];
    }

    public function update($validated): void
    {
        // dd($validated);
        // Get the exam marks record to update
        $examMarks = ExamMarks::where('tenant_id', tenant('id'))
            ->where('id', $validated['id'])
            ->firstOrFail();

        // Verify that the exam marks record belongs to the same exam, class, and subject
        $examSubject = ExamSubject::where('id', $validated['SubjectId'])
            ->where('ExamId', $validated['ExamId'])
            ->where('ClassId', $validated['ClassId'])
            ->firstOrFail();

        // Ensure the exam marks record matches the exam subject
        if ($examMarks->ExamSubjectId !== $examSubject->id) {
            throw new \Exception('Exam marks does not match the selected exam subject.');
        }

        // Update exam marks with modification details
        $examMarks->update([
            'ModifiedBy' => auth()->id(),
        ]);

        // Update existing exam marks details
        DB::transaction(function () use ($validated, $examMarks) {

            // $examMarksDetails = ExamMarksDetail::where('tenant_id', tenant('id'));

            // dd($examMarksDetails->where('ExamMarksId', $examMarks->id)->first());
            foreach ($validated['StudentsData'] as $student) {

                $conditions = [
                    'ExamMarksId' => $examMarks->id,
                    'StudentId' => $student['StudentId'],
                ];

                if (! array_key_exists('Marks', $student) || $student['Marks'] === null || $student['Marks'] === '') {
                    ExamMarksDetail::where('tenant_id', tenant('id'))->where($conditions)->delete();

                    continue;
                }

                // dd($examMarks, $student, ExamMarksDetail::where('tenant_id', tenant('id'))->where($conditions)->first());

                $updateData = [
                    'tenant_id' => tenant('id') ?? null,
                    'SchoolId' => auth()->user()->school_id ?? null,
                    'SessionId' => $examMarks->SessionId,
                    'IsActive' => 1,
                    'CreatedBy' => auth()->id(),
                    'ModifiedBy' => auth()->id(),
                    'Marks' => $student['Marks'] ?? null,
                    'Remarks' => $student['Remarks'] ?? null,
                ];

                $existing = ExamMarksDetail::where('tenant_id', tenant('id'))->withTrashed()->where($conditions)->first();
                // dd('dafasf', $existing);
                if ($existing) {
                    if ($existing->trashed()) {
                        $existing->restore();
                        $existing->update($updateData); // ✅ Update existing
                    } else {
                        ExamMarksDetail::where('tenant_id', tenant('id'))->updateOrCreate($conditions, $updateData);
                    }
                } else {
                    ExamMarksDetail::create(array_merge($conditions, $updateData));
                }
            }
        });
    }
}
