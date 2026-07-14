<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\StaffRequest;
use App\Models\HumanResourceLog;
use App\Models\Staff;
use App\Models\StaffDisableReason;
use App\Services\StaffService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StaffController extends Controller
{
    protected $staffService;

    public function __construct(StaffService $staffService)
    {
        $this->staffService = $staffService;
    }

    public function index(): Response
    {
        $staffList = $this->staffService->index();
        // dd($staffList->toArray());
        $disableReasons = StaffDisableReason::select('id', 'DisableReasonName')->get();

        return Inertia::render('Staff/StaffList', [
            'staffList' => $staffList,
            'disable_reasons' => $disableReasons,
        ]);
    }

    public function create(): Response
    {
        $data = $this->staffService->create();

        return Inertia::render('Staff/StaffCreate', [
            'data' => $data,
        ]);
    }

    public function submit(StaffRequest $request): RedirectResponse
    {
        $this->staffService->submit($request);

        return $this->redirectSuccess('Staff created successfully!', 'staff.list');
    }

    public function edit(Request $request): Response
    {
        $staff = Staff::Findorfail($request->id);
        $data = $this->staffService->create();

        return Inertia::render('Staff/StaffEdit', [
            'staff' => $staff,
            'data' => $data,
        ]);
    }

    public function update(StaffRequest $request): RedirectResponse
    {
        $this->staffService->update($request);

        return $this->redirectSuccess('Staff updated successfully!', 'staff.list');
    }

    public function delete(Request $request): RedirectResponse
    {
        $staff = Staff::query();
        $staff->where('id', $request->id)->update([
            'ModifiedBy' => auth()->user()->id,
        ]);
        $deleted = Staff::findorFail($request->id)->delete();

        if ($deleted) {
            userActivityLogs('Staff Deleted and id is '.$request->id.' User ID: '.auth()->user()->id.'', HumanResourceLog::class);
        }

        return $this->redirectSuccess('Staff deleted successfully!', 'staff.list');
    }

    public function toggleStatus(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'FromDate' => ['nullable', 'date'],
            'Reason' => ['required', 'max:255'],
        ]);

        $this->staffService->toggleStatus($request, $id);

        return $this->redirectSuccess('Staff status updated successfully!', 'staff.list');
    }
}
