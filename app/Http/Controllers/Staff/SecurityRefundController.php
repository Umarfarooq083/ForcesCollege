<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\SecurityRefundRequest;
use App\Services\SecurityRefundService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SecurityRefundController extends Controller
{
    protected $securityRefundService;

    public function __construct(SecurityRefundService $securityRefundService)
    {
        $this->securityRefundService = $securityRefundService;
    }

    public function index(): Response
    {
        $securityRefunds = $this->securityRefundService->index();

        return Inertia::render('Staff/SecurityRefund/List', [
            'securityRefunds' => $securityRefunds,
        ]);
    }

    public function create(): Response
    {
        $data = $this->securityRefundService->create();

        return Inertia::render('Staff/SecurityRefund/Create', [
            'data' => $data,
        ]);
    }

    public function submit(SecurityRefundRequest $request): RedirectResponse
    {
        $this->securityRefundService->submit($request);

        return $this->redirectSuccess('Security refund created successfully!', 'securityrefund.index');
    }

    public function edit(Request $request): Response
    {
        $data = $this->securityRefundService->edit($request);

        return Inertia::render('Staff/SecurityRefund/Edit', [
            'securityRefund' => $data->securityRefund,
            'staffList' => $data->staffList,
        ]);
    }

    public function update(SecurityRefundRequest $request): RedirectResponse
    {
        $this->securityRefundService->update($request);

        return $this->redirectSuccess('Security refund updated successfully!', 'securityrefund.index');
    }

    public function delete(Request $request): RedirectResponse
    {
        $this->securityRefundService->destroy($request);

        return $this->redirectSuccess('Security refund deleted successfully!', 'securityrefund.index');
    }
}
