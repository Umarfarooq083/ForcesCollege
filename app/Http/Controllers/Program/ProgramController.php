<?php

namespace App\Http\Controllers\Program;

use App\Http\Controllers\Controller;
use App\Http\Requests\Program\ProgramRequest;
use App\Models\Program;
use App\Services\ProgramService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProgramController extends Controller
{
    protected $programService;

    public function __construct(ProgramService $programService)
    {
        $this->programService = $programService;
    }

    public function index(Request $request): Response
    {
        $programs = Program::select('id', 'name', 'type', 'duration')
            ->where('tenant_id', tenant('id'));

        if ($request->filled('search')) {
            $programs->where('name', 'like', '%'.$request->search.'%');
        }

        $programs = $programs->orderBy('id', 'desc')->paginate(25)->withQueryString();

        return Inertia::render('Program/List', [
            'programs' => $programs,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Program/Create');
    }

    public function submit(ProgramRequest $request): RedirectResponse
    {
        $this->programService->submit($request);

        return $this->redirectSuccess('Program created successfully!', 'program.index');
    }

    public function edit(Request $request): Response
    {
        $programData = Program::where('id', $request->id)->firstOrFail();

        return Inertia::render('Program/Edit', [
            'programData' => $programData,
        ]);
    }

    public function update(ProgramRequest $request): RedirectResponse
    {
        $this->programService->update($request);

        return $this->redirectSuccess('Program updated successfully!', 'program.index');
    }

    public function delete(Request $request): RedirectResponse
    {
        $this->programService->delete($request);

        return $this->redirectSuccess('Program deleted successfully!', 'program.index');
    }
}
