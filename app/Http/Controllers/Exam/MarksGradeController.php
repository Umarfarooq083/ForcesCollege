<?php

namespace App\Http\Controllers\Exam;
use App\Http\Controllers\Controller;
use App\Http\Requests\Exam\ExamGradeRequest;
use App\Models\Classes;
use App\Models\ExamGrade;
use App\Models\ExamLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MarksGradeController extends Controller
{
    public function index(Request $request): Response
    {
        $query = ExamGrade::where('tenant_id', tenant('id'))->with('classRel');
        if($request->filled('search'))
        {
            $search = $request->search;
            $query->where(function($q) use($search){
                $q->where('GradeName', 'like', "%{$search}%")
                ->orWhereHas('classRel', function($sub) use($search){
                    $sub->where('ClassName', 'like', "%{$search}%");
                });
            });
        }
        $grades =  $query->orderBy('id', 'desc')->paginate(25)->withQueryString();
        return Inertia::render('Exam/ExamGrade/List', [
            'grades' => $grades
        ]);
    }

    public function create(): Response
    {
        $classes = Classes::where('tenant_id', tenant('id'))->where('IsActive', 1)->get();
        return Inertia::render('Exam/ExamGrade/Create', [
            'classes' => $classes
        ]);
    }

    public function submit(ExamGradeRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $records = [];
        foreach ($validated['ClassId'] as $classId) {
            $records[] = [
                'GradeName'   => $validated['GradeName'],
                'ClassId'     => $classId,
                'PercentFrom' => $validated['PercentFrom'],
                'PercentUpt'  => $validated['PercentUpt'],
                'Description' => $validated['Description'] ?? null,
                'tenant_id'   => tenant('id'),
                'CreatedBy'   => auth()->id(),
                'created_at'  => now(),
                'updated_at'  => now(),
            ];
        }

        ExamGrade::insert($records);
        userActivityLogs('Exam Grade Created and User ID: '.auth()->user()->id.'', ExamLog::class);
        return redirect()->route('marksgrade.list')->with('success', 'Marks grade created successfully.');
    }

    public function edit(Request $request): Response
    {
        $grade = ExamGrade::where('tenant_id', tenant('id'))->where('id', $request->id)->first();
        $classes = Classes::where('tenant_id', tenant('id'))->where('IsActive', 1)->get();
        return Inertia::render('Exam/ExamGrade/Edit', [
            'grade' => $grade,
            'classes' => $classes
        ]);
    }

    public function update(ExamGradeRequest $request): RedirectResponse
    {
        $validated = $request->validated(); 
        ExamGrade::where('tenant_id', tenant('id'))->where('id', $request->id)->update([
            ...$validated,
            'ModifiedBy' => auth()->user()->id,
        ]);
        userActivityLogs('Exam Grade Updated and id is '.$request->id.' User ID: '.auth()->user()->id.'', ExamLog::class);
        return redirect()->route('marksgrade.list')->with('success', 'Marks grade updated successfully.');
    }

    public function delete(Request $request):RedirectResponse
    {
        $deleted = ExamGrade::where('tenant_id', tenant('id'))
        ->findOrFail($request->id)->delete();
        if ($deleted) {
            userActivityLogs('Exam Grade Deleted and id is '.$request->id.' User ID: '.auth()->user()->id.'', ExamLog::class);
        }
        return $this->redirectSuccess('Exam Grade deleted successfully!', 'marksgrade.list');
    }

}
