<?php

namespace App\Services;

use App\Models\HumanResourceLog;
use App\Models\LeaveRequest;
use App\Models\Staff;
use Illuminate\Support\Facades\DB;

class LeaveRequestService
{
    public function index(): object
    {
        return LeaveRequest::query()
            ->tenant()
            ->with(['staff', 'approver'])
            ->orderBy('id', 'desc')
            ->paginate(25)
            ->withQueryString();
    }

    public function create(): array
    {
        $staffList = Staff::select('id', 'FirstName', 'LastName')
            ->where('tenant_id', tenant('id'))
            ->where('IsActive', 1)
            ->get();

        return [
            'staffList' => $staffList,
        ];
    }

    public function submit($request): void
    {
        $validated = $request->validated();
        $tenantId = tenant('id');

        DB::transaction(function () use ($validated, $tenantId) {
            foreach ($validated['dates'] as $date) {
                $leave = LeaveRequest::create([
                    'tenant_id' => $tenantId,
                    'staff_id' => $validated['staff_id'],
                    'date' => $date,
                    'leave_type' => $validated['leave_type'],
                    'reason' => $validated['reason'],
                    'status' => 'pending',
                    'CreatedBy' => auth()->id(),
                ]);

                if ($leave) {
                    userActivityLogs('Leave request created for date: ' . $date . ' and By User ID: ' . auth()->user()->id, HumanResourceLog::class);
                }
            }
        });
    }

    public function update($request): void
    {
        $validated = $request->validated();
        $leave = LeaveRequest::findOrFail($request->id);

        $leave->update([
            'staff_id' => $validated['staff_id'],
            'date' => $validated['date'],
            'leave_type' => $validated['leave_type'],
            'reason' => $validated['reason'],
            'ModifiedBy' => auth()->id(),
        ]);

        userActivityLogs('Leave request updated and id is '.$request->id.' By User ID: ' . auth()->user()->id, HumanResourceLog::class);
    }

    public function approve($request, $id): void
    {
        $leave = LeaveRequest::where('tenant_id', tenant('id'))->findOrFail($id);

        $leave->update([
            'status' => $request->status,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
            'approval_note' => $request->approval_note,
            'ModifiedBy' => auth()->id(),
        ]);

        userActivityLogs('Leave request ' . $request->status . ' and By User ID: ' . auth()->user()->id, HumanResourceLog::class);
    }

    public function destroy($request): void
    {
        $leave = LeaveRequest::where('tenant_id', tenant('id'))->findOrFail($request->id);
        $deleted = $leave->delete();

        if ($deleted) {
            userActivityLogs('Leave request deleted and id is '.$request->id.' By User ID: ' . auth()->user()->id, HumanResourceLog::class);
        }
    }
}