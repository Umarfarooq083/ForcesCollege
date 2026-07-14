<?php

namespace App\Http\Controllers\Section;

use App\Http\Controllers\Controller;
use App\Http\Requests\Section\SectionRequest;
use App\Models\Classes;
use App\Models\Section;
use App\Models\SectionType;
use App\Services\SectionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SectionController extends Controller
{
    protected $sectionService;

    public function __construct(SectionService $sectionService)
    {
        $this->sectionService = $sectionService;
    }

    public function index(Request $request): Response
    {
        $sections = $this->sectionService->index($request);
        $classesList = Classes::select('id', 'ClassName')->get();

        return Inertia::render('Section/List', [
            'sections' => $sections,
            'classesList' => $classesList,
        ]);
    }

    public function create(): Response
    {
        $classesList = Classes::select('id', 'ClassName')->where('tenant_id', tenant('id'))->where('IsActive', 1)->get();
        $sectionTypes = SectionType::select('id', 'name')->where('status', 1)->get();

        return Inertia::render('Section/Create', [
            'classesList' => $classesList,
            'sectionTypes' => $sectionTypes,
        ]);
    }

    public function submit(SectionRequest $request): RedirectResponse
    {

        $this->sectionService->submit($request);

        return $this->redirectSuccess('Section created successfully!', 'section.index');

        // dd(auth()->user()->id);
        // dd(auth()->user()->tenant_id);
        // dd([
        //     'all_data' => $request->all(),
        //     'tenant_id' => getDomainTenantId(),
        //     'selected_campus_id' => session('selected_campus_id'),
        // ]);
    }

    public function edit(SectionRequest $request): Response
    {
        $sectionData = Section::where('id', $request->id)->first();

        return Inertia::render('Section/Edit', ['sectionData' => $sectionData]);
    }

    public function update(SectionRequest $request): RedirectResponse
    {
        $this->sectionService->update($request->validated());

        return $this->redirectSuccess('Section updated successfully!', 'section.index');
    }
}
