<?php

namespace App\Http\Controllers\Program;

use App\Http\Controllers\Controller;
use App\Http\Requests\Program\ProgramLevelRequest;
use App\Models\Program;
use App\Models\ProgramLevel;
use App\Services\ProgramLevelService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProgramLevelController extends Controller
{
    protected $programLevelService;

    public function __construct(ProgramLevelService $programLevelService)
    {
        $this->programLevelService = $programLevelService;
    }

    public function index(Request $request): Response
    {
        $programLevels = ProgramLevel::select('id', 'programm_id', 'title', 'status')
            ->where('tenant_id', tenant('id'));

        if ($request->filled('search')) {
            $search = $request->search;
            $programLevels->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        $programLevels = $programLevels->orderBy('id', 'desc')->paginate(25)->withQueryString();

        $programs = Program::where('tenant_id', tenant('id'))->get(['id', 'name']);

        return Inertia::render('ProgramLevel/List', [
            'programLevels' => $programLevels,
            'programs' => $programs,
        ]);
    }

    public function create(): Response
    {
        $programs = Program::where('tenant_id', tenant('id'))->get(['id', 'name']);

        return Inertia::render('ProgramLevel/Create', [
            'programs' => $programs,
        ]);
    }

    public function submit(ProgramLevelRequest $request): RedirectResponse
    {
        $this->programLevelService->submit($request);

        return $this->redirectSuccess('Program level created successfully!', 'programlevel.index');
    }

    public function edit(Request $request): Response
    {
        $programLevelData = ProgramLevel::where('id', $request->id)
            ->where('tenant_id', tenant('id'))
            ->firstOrFail();

        $programs = Program::where('tenant_id', tenant('id'))->get(['id', 'name']);

        return Inertia::render('ProgramLevel/Edit', [
            'programLevelData' => $programLevelData,
            'programs' => $programs,
        ]);
    }

    public function update(ProgramLevelRequest $request): RedirectResponse
    {
        $this->programLevelService->update($request);

        return $this->redirectSuccess('Program level updated successfully!', 'programlevel.index');
    }

    public function delete(Request $request): RedirectResponse
    {
        $this->programLevelService->delete($request);

        return $this->redirectSuccess('Program level deleted successfully!', 'programlevel.index');
    }
}
