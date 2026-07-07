<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\MiscellaneousPaymentRequest;
use App\Services\MiscellaneousPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MiscellaneousPaymentController extends Controller
{
    protected $miscellaneousPaymentService;

    public function __construct(MiscellaneousPaymentService $miscellaneousPaymentService)
    {
        $this->miscellaneousPaymentService = $miscellaneousPaymentService;
    }

    public function index(): Response
    {
        $miscellaneousPayments = $this->miscellaneousPaymentService->index();
        return Inertia::render('Staff/MiscellaneousPayment/List', [
            'miscellaneousPayments' => $miscellaneousPayments
        ]);
    }

    public function create(): Response
    {
        $data = $this->miscellaneousPaymentService->create();
        return Inertia::render('Staff/MiscellaneousPayment/Create', [
            'data' => $data,
        ]);
    }

    public function submit(MiscellaneousPaymentRequest $request): RedirectResponse
    {
        $this->miscellaneousPaymentService->submit($request);
        return $this->redirectSuccess('Miscellaneous payment created successfully!', 'miscellaneouspayment.index');
    }

    public function edit(Request $request): Response
    {
        $data = $this->miscellaneousPaymentService->edit($request);
        return Inertia::render('Staff/MiscellaneousPayment/Edit', [
            'miscellaneousPayment' => $data->miscellaneousPayment,
            'staffList' => $data->staffList,
        ]);
    }

    public function update(MiscellaneousPaymentRequest $request): RedirectResponse
    {
        $this->miscellaneousPaymentService->update($request);
        return $this->redirectSuccess('Miscellaneous payment updated successfully!', 'miscellaneouspayment.index');
    }

    public function delete(Request $request): RedirectResponse
    {
        $this->miscellaneousPaymentService->destroy($request);
        return $this->redirectSuccess('Miscellaneous payment deleted successfully!', 'miscellaneouspayment.index');
    }
}