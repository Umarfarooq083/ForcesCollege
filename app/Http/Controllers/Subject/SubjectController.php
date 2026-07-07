<?php

namespace App\Http\Controllers\Subject;

use App\Http\Controllers\Controller;
use App\Http\Requests\Subject\SubjectRequest;
use App\Models\Classes;
use App\Models\Subject;
use App\Services\SubjectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SubjectController extends Controller
{
    protected $subjectService;

    public function __construct(SubjectService $subjectService)
    {
        $this->subjectService  = $subjectService;
    }

    public function index(Request $request): Response
    {
        $subject = Subject::select('id', 'SubjectName', 'ClassId')->where('tenant_id',tenant('id'))->with('classes');
        if($request->filled('search')){
            $subject->where(function($q) use($request) {
                $q->where('SubjectName', 'like', '%'. $request->search .'%')
                ->orWhereHas('classes', function($sub) use($request) {
                    $sub->where('ClassName', 'like', "%{$request->search}%");
                });
            });
        }
        $subject = $subject->orderBy('id','desc')->paginate(25)->withQueryString();
        return Inertia::render('Subject/List', [
            'subject' => $subject,
        ]);
    }

    public function create(): Response
    {
        $classesList = $this->classList();
        return Inertia::render('Subject/Create', [
            'classesList' => $classesList,
        ]);
    }

    public function submit(SubjectRequest $request): RedirectResponse
    {
        $this->subjectService->submit($request);
        return $this->redirectSuccess('Subject created successfully!', 'subject.index');
    }

    public function edit(Request $request): Response
    {
        $classesList = $this->classList();
        $subjectData = Subject::where('id',$request->id)->first();
        return Inertia::render('Subject/Edit', [
            'subjectData' => $subjectData,
            'classesList' => $classesList,
        ]);
    }

    public function update(SubjectRequest $request): RedirectResponse
    {  
        $this->subjectService->update($request);
        return $this->redirectSuccess('Subject created successfully!', 'subject.index');
    }

    private function classList()
    {
        return Classes::select('id', 'ClassName')->get();
    }
}
