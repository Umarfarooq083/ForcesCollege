<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\SalaryTaxRequest;
use App\Services\SalaryTaxService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SalaryTaxController extends Controller
{
    protected $salaryTaxService;

    public function __construct(SalaryTaxService $salaryTaxService)
    {
        $this->salaryTaxService = $salaryTaxService;
    }

    public function index(): Response
    {
        $salaryTaxes = $this->salaryTaxService->index();
        return Inertia::render('Staff/SalaryTax/List', [
            'salaryTaxes' => $salaryTaxes
        ]);
    }

    public function create(): Response
    {
        $data = $this->salaryTaxService->create();
        return Inertia::render('Staff/SalaryTax/Create', [
            'data' => $data,
        ]);
    }

    public function submit(SalaryTaxRequest $request): RedirectResponse
    {
        $this->salaryTaxService->submit($request);
        return $this->redirectSuccess('Salary tax created successfully!', 'salarytax.index');
    }

    public function edit(Request $request): Response
    {
        $data = $this->salaryTaxService->edit($request);
        return Inertia::render('Staff/SalaryTax/Edit', [
            'salaryTax' => $data->salaryTax,
            'staffList' => $data->staffList,
        ]);
    }

    public function update(SalaryTaxRequest $request): RedirectResponse
    {
        $this->salaryTaxService->update($request);
        return $this->redirectSuccess('Salary tax updated successfully!', 'salarytax.index');
    }

    public function delete(Request $request): RedirectResponse
    {
        $this->salaryTaxService->destroy($request);
        return $this->redirectSuccess('Salary tax deleted successfully!', 'salarytax.index');
    }
}