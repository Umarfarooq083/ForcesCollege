<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\DepartmentRequest;
use App\Models\Department;
use App\Services\DepartmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DepartmentController extends Controller
{
    protected $departmentService;

    public function __construct(DepartmentService $departmentService)
    {
        $this->departmentService = $departmentService;
    }

    public function index(): Response
    {
        $departmentData = Department::orderBy('id', 'desc')->paginate(25)->withQueryString();

        return Inertia::render('Staff/Department/List', [
            'departmentData' => $departmentData,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Staff/Department/Create');
    }

    public function submit(DepartmentRequest $request): RedirectResponse
    {
        $this->departmentService->submit($request);

        return $this->redirectSuccess('Department created successfully!', 'department.list');
    }

    public function edit(Request $request): Response
    {
        $Department = Department::findOrFail($request->id);

        return Inertia::render('Staff/Department/Edit', [
            'Department' => $Department,
        ]);
    }

    public function update(DepartmentRequest $request): RedirectResponse
    {
        $this->departmentService->update($request);

        return $this->redirectSuccess('Department updated successfully!', 'department.list');
    }
}
