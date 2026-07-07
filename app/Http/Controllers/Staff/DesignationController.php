<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\Staff\DesignationRequest;
use App\Services\DesignationService;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Designation;

class DesignationController extends Controller
{

    protected $designationService;
    public function __construct(DesignationService $designationService)
    {
        $this->designationService = $designationService;
    }

    public function index(): Response
    {
        $designations = $this->designationService->index();
        return Inertia::render('Staff/Designation/List', [
            'designations' => $designations,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Staff/Designation/Create');
    }

    public function submit(DesignationRequest $request): RedirectResponse
    {
        $this->designationService->submit($request);
        return $this->redirectSuccess('Designation created successfully!', 'designation.index');
    }

    public function edit(Request $request): Response
    {
        $designations = Designation::where('id',$request->id)->first();
         return Inertia::render('Staff/Designation/Edit', [
            'designations' => $designations,
        ]);
    }

    public function update(DesignationRequest $request): RedirectResponse
    {  
        $this->designationService->update($request);
        return $this->redirectSuccess('Designation Updated successfully!', 'designation.index');
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->designationService->destroy($id);
        return $this->redirectSuccess('Designation deleted successfully!', 'designation.index');
    }

    public function toggleStatus(Request $request, int $id): RedirectResponse
    {
        $this->designationService->toggleStatus($request, $id);
        return $this->redirectSuccess('Designation status updated successfully!', 'designation.index');
    }

}
