<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\LeaveRequest;
use App\Services\LeaveRequestService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LeaveRequestController extends Controller
{
    protected $leaveRequestService;

    public function __construct(LeaveRequestService $leaveRequestService)
    {
        $this->leaveRequestService = $leaveRequestService;
    }

    public function index(): Response
    {
        $leaveRequests = $this->leaveRequestService->index();

        return Inertia::render('Staff/LeaveRequest/List', [
            'leaveRequests' => $leaveRequests,
        ]);
    }

    public function create(): Response
    {
        $data = $this->leaveRequestService->create();

        return Inertia::render('Staff/LeaveRequest/Create', [
            'staffList' => $data['staffList'],
        ]);
    }

    public function submit(LeaveRequest $request): RedirectResponse
    {
        $this->leaveRequestService->submit($request);

        return $this->redirectSuccess('Leave request(s) submitted successfully!', 'leave-request.index');
    }

    public function edit(Request $request): Response
    {
        $leaveRequest = \App\Models\LeaveRequest::with(['staff', 'approver'])
            ->where('tenant_id', tenant('id'))
            ->findOrFail($request->id);

        $data = $this->leaveRequestService->create();

        return Inertia::render('Staff/LeaveRequest/Edit', [
            'leaveRequest' => $leaveRequest,
            'staffList' => $data['staffList'],
        ]);
    }

    public function update(LeaveRequest $request): RedirectResponse
    {
        $this->leaveRequestService->update($request);

        return $this->redirectSuccess('Leave request updated successfully!', 'leave-request.index');
    }

    public function approve(Request $request, $id): RedirectResponse
    {
        $this->leaveRequestService->approve($request, $id);

        return $this->redirectSuccess('Leave request updated successfully!', 'leave-request.index');
    }

    public function delete(Request $request): RedirectResponse
    {
        $this->leaveRequestService->destroy($request);

        return $this->redirectSuccess('Leave request deleted successfully!', 'leave-request.index');
    }
}
