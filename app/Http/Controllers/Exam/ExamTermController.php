<?php

namespace App\Http\Controllers\Exam;
use App\Http\Controllers\Controller;
use App\Models\ExamLog;
use App\Models\ExamTerm;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExamTermController extends Controller
{
    public function index(Request $request): Response
    {
        $query = ExamTerm::query();
        if($request->filled('search'))
        {
            $query->where('ExamTermName', 'like', '%'. $request->search . '%');
        }
        $examTerms = $query->orderBy('id','desc')->paginate(25)->withQueryString();
        return Inertia::render('Exam/ExamTerm/List', [
            'examTerms' => $examTerms
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Exam/ExamTerm/Create');
    }

    public function submit(Request $request): RedirectResponse
    {
       ExamTerm::create(array_merge( $request->all(),
            [ 'CreatedBy' => auth()->id(), ]
        ));
        userActivityLogs('Exam Term Created and User ID: '.auth()->user()->id.'', ExamLog::class);
        return $this->redirectSuccess('Exam term created successfully.', 'examterm.index');
    }

    public function edit(Request $request): Response
    {
        $examTerm = ExamTerm::findOrFail($request->id);
        return Inertia::render('Exam/ExamTerm/Edit', [
            'examTerm' => $examTerm
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $examTerm = ExamTerm::findOrFail($request->id);
        $examTerm->update(array_merge(
            $request->all(),
            [ 'ModifiedBy' => auth()->id(), ]
        ));
        userActivityLogs('Exam Term Updated and id is '.$request->id.' and User ID: '.auth()->user()->id.'', ExamLog::class);
        return $this->redirectSuccess('Exam term updated successfully.', 'examterm.index');
    }
}
