<?php

namespace App\Http\Controllers\Exam;

use App\Http\Controllers\Controller;
use App\Http\Requests\Exam\ExamTypeRequest;
use App\Models\ExamLog;
use App\Models\ExamTerm;
use App\Models\ExamType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Inertia\Inertia;
use Inertia\Response;

class ExamTypeController extends Controller
{
    public function index(Request $request): Response
    {
        $current_session = fetchCurrentSession();
        if (! $current_session) {

            $emptyCollection = collect([]);

            $examTypes = new LengthAwarePaginator(
                $emptyCollection,
                0,
                25,
                request()->get('page', 1),
                [
                    'path' => request()->url(),
                    'query' => request()->query(),
                ]
            );

            return Inertia::render('Exam/ExamType/List', [
                'examTypes' => $examTypes,
            ]);
        }

        $query = ExamType::with('examTerm', 'SessionRel')->where('SessionId', $current_session->id)->orderBy('id', 'desc')->where('tenant_id', tenant('id'));
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('ExamName', 'like', "%{$search}%")
                    ->orWhereHas('examTerm', function ($sub) use ($search) {
                        $sub->where('ExamTermName', 'like', '%'.$search.'%');
                    });
            });
        }
        $examTypes = $query->paginate(25)->withQueryString();

        return Inertia::render('Exam/ExamType/List', [
            'examTypes' => $examTypes,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Exam/ExamType/Create', [
            'examTerms' => ExamTerm::get(),
            'currentSession' => fetchCurrentSession(),
        ]);
    }

    public function submit(ExamTypeRequest $request): RedirectResponse
    {
        ExamType::create([
            ...$request->validated(),
            'CreatedBy' => auth()->user()->id,
            'tenant_id' => tenant('id'),
        ]);
        userActivityLogs('Exam Type Created and User ID: '.auth()->user()->id.'', ExamLog::class);

        return $this->redirectSuccess('Exam created successfully.', 'examtype.index');
    }

    public function edit(Request $request): Response
    {
        $examType = ExamType::where('tenant_id', tenant('id'))->with('examTerm', 'SessionRel')->findOrFail($request->id);

        return Inertia::render('Exam/ExamType/Edit', [
            'examType' => $examType,
            'examTerms' => ExamTerm::all(),
            'currentSession' => fetchCurrentSession(),
        ]);
    }

    public function update(ExamTypeRequest $request): RedirectResponse
    {
        $examType = ExamType::where('tenant_id', tenant('id'))->findOrFail($request->id);
        $examType->update($request->validated());
        userActivityLogs('Exam Type Updated and id '.$request->id.' User ID: '.auth()->user()->id.'', ExamLog::class);

        return $this->redirectSuccess('Exam updated and id is '.$examType->id.' successfully.', 'examtype.index');
    }
}
