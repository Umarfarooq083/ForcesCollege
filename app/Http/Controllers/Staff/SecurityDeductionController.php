<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\SecurityDeductionRequest;
use App\Services\SecurityDeductionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SecurityDeductionController extends Controller
{
    protected $securityDeductionService;

    public function __construct(SecurityDeductionService $securityDeductionService)
    {
        $this->securityDeductionService = $securityDeductionService;
    }

    public function index(): Response
    {
        $securityDeductions = $this->securityDeductionService->index();
        return Inertia::render('Staff/SecurityDeduction/List', [
            'securityDeductions' => $securityDeductions
        ]);
    }

    public function create(): Response
    {
        $data = $this->securityDeductionService->create();
        return Inertia::render('Staff/SecurityDeduction/Create', [
            'data' => $data,
        ]);
    }

    public function submit(SecurityDeductionRequest $request): RedirectResponse
    {
        $this->securityDeductionService->submit($request);
        return $this->redirectSuccess('Security deduction created successfully!', 'securitydeduction.index');
    }

    public function edit(Request $request): Response
    {
        $data = $this->securityDeductionService->edit($request);
        return Inertia::render('Staff/SecurityDeduction/Edit', [
            'securityDeduction' => $data->securityDeduction,
            'staffList' => $data->staffList,
        ]);
    }

    public function update(SecurityDeductionRequest $request): RedirectResponse
    {
        $this->securityDeductionService->update($request);
        return $this->redirectSuccess('Security deduction updated successfully!', 'securitydeduction.index');
    }

    public function delete(Request $request): RedirectResponse
    {
        $this->securityDeductionService->destroy($request);
        return $this->redirectSuccess('Security deduction deleted successfully!', 'securitydeduction.index');
    }
}