<?php

namespace App\Services;

use App\Models\HumanResourceLog;
use App\Models\LateFine;
use Illuminate\Validation\ValidationException;

class LateFineService
{
    public function index(): object
    {
        return LateFine::with('staff')
            ->where('tenant_id', tenant('id'))
            ->orderBy('id', 'desc')
            ->paginate(25)->withQueryString();
    }

    public function create(): array
    {
        $staffList = \App\Models\Staff::select('id', 'FirstName', 'LastName')
            ->where('tenant_id', tenant('id'))
            ->where('IsActive', 1)
            ->get();

        return [
            'staffList' => $staffList,
        ];
    }

    public function submit($request): void
    {
        $tenantId = tenant('id');
        $validated = $request->validated();

        $exists = LateFine::where('tenant_id', $tenantId)
            ->where('staff_id', $validated['staff_id'])
            ->where('apply_year', $validated['apply_year'])
            ->where('applicable_month', $validated['applicable_month'])
            ->whereNull('deleted_at')
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'apply_year' => ['A late fine already exists for this staff with the selected year and month.'],
            ]);
        }

        $created = LateFine::create([
            'tenant_id' => $tenantId,
            'staff_id' => $validated['staff_id'],
            'apply_year' => $validated['apply_year'],
            'applicable_month' => $validated['applicable_month'],
            'amount' => $validated['amount'],
            'reason' => $validated['reason'],
            'CreatedBy' => auth()->user()->id,
        ]);

        if ($created) {
            userActivityLogs('Late Fine Created and By User ID: '.auth()->user()->id.'', HumanResourceLog::class);
        }
    }

    public function edit($request): object
    {
        $lateFine = LateFine::with('staff')
            ->where('tenant_id', tenant('id'))
            ->findOrFail($request->id);

        $staffList = \App\Models\Staff::select('id', 'FirstName', 'LastName')
            ->where('tenant_id', tenant('id'))
            ->where('IsActive', 1)
            ->get();

        return (object) [
            'lateFine' => $lateFine,
            'staffList' => $staffList,
        ];
    }

    public function update($request): void
    {
        $tenantId = tenant('id');
        $lateFine = LateFine::where('tenant_id', $tenantId)->findOrFail($request->id);
        $validated = $request->validated();

        $exists = LateFine::where('tenant_id', $tenantId)
            ->where('staff_id', $validated['staff_id'])
            ->where('apply_year', $validated['apply_year'])
            ->where('applicable_month', $validated['applicable_month'])
            ->whereNull('deleted_at')
            ->where('id', '!=', $lateFine->id)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'apply_year' => ['A late fine already exists for this staff with the selected year and month.'],
            ]);
        }

        $updated = $lateFine->update([
            'staff_id' => $validated['staff_id'],
            'apply_year' => $validated['apply_year'],
            'applicable_month' => $validated['applicable_month'],
            'amount' => $validated['amount'],
            'reason' => $validated['reason'],
            'ModifiedBy' => auth()->user()->id,
        ]);

        if ($updated) {
            userActivityLogs('Late Fine Updated and id is '.$request->id.' By User ID: '.auth()->user()->id.'', HumanResourceLog::class);
        }
    }

    public function destroy($request): void
    {
        $lateFine = LateFine::where('tenant_id', tenant('id'))->findOrFail($request->id);
        $deleted = $lateFine->delete();

        if ($deleted) {
            userActivityLogs('Late Fine Deleted and id is '.$request->id.' By User ID: '.auth()->user()->id.'', HumanResourceLog::class);
        }
    }
}
