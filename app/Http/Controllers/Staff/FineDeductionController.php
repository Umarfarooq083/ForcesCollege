<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\FineDeductionRequest;
use App\Services\FineDeductionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FineDeductionController extends Controller
{
    protected $fineDeductionService;

    public function __construct(FineDeductionService $fineDeductionService)
    {
        $this->fineDeductionService = $fineDeductionService;
    }

    public function index(): Response
    {
        $fineDeductions = $this->fineDeductionService->index();

        return Inertia::render('Staff/FineDeduction/List', [
            'fineDeductions' => $fineDeductions,
        ]);
    }

    public function create(): Response
    {
        $data = $this->fineDeductionService->create();

        return Inertia::render('Staff/FineDeduction/Create', [
            'data' => $data,
        ]);
    }

    public function submit(FineDeductionRequest $request): RedirectResponse
    {
        $this->fineDeductionService->submit($request);

        return $this->redirectSuccess('Fine deduction created successfully!', 'finededuction.index');
    }

    public function edit(Request $request): Response
    {
        $data = $this->fineDeductionService->edit($request);

        return Inertia::render('Staff/FineDeduction/Edit', [
            'fineDeduction' => $data->fineDeduction,
            'staffList' => $data->staffList,
        ]);
    }

    public function update(FineDeductionRequest $request): RedirectResponse
    {
        $this->fineDeductionService->update($request);

        return $this->redirectSuccess('Fine deduction updated successfully!', 'finededuction.index');
    }

    public function delete(Request $request): RedirectResponse
    {
        $this->fineDeductionService->destroy($request);

        return $this->redirectSuccess('Fine deduction deleted successfully!', 'finededuction.index');
    }
}
