<?php

namespace App\Services;

use App\Models\FineDeduction;
use App\Models\HumanResourceLog;
use Illuminate\Validation\ValidationException;

class FineDeductionService
{
    public function index(): object
    {
        return FineDeduction::with('staff')
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

        $exists = FineDeduction::where('tenant_id', $tenantId)
            ->where('staff_id', $validated['staff_id'])
            ->where('apply_year', $validated['apply_year'])
            ->where('applicable_month', $validated['applicable_month'])
            ->whereNull('deleted_at')
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'apply_year' => ['A fine deduction already exists for this staff with the selected year and month.']
            ]);
        }

        $created = FineDeduction::create([
            'tenant_id' => $tenantId,
            'staff_id' => $validated['staff_id'],
            'apply_year' => $validated['apply_year'],
            'applicable_month' => $validated['applicable_month'],
            'amount' => $validated['amount'],
            'reason' => $validated['reason'],
            'CreatedBy' => auth()->user()->id,
        ]);

        if ($created) {
            userActivityLogs('Fine Deduction Created and By User ID: ' . auth()->user()->id . '', HumanResourceLog::class);
        }
    }

    public function edit($request): object
    {
        $fineDeduction = FineDeduction::with('staff')
            ->where('tenant_id', tenant('id'))
            ->findOrFail($request->id);

        $staffList = \App\Models\Staff::select('id', 'FirstName', 'LastName')
            ->where('tenant_id', tenant('id'))
            ->where('IsActive', 1)
            ->get();

        return (object) [
            'fineDeduction' => $fineDeduction,
            'staffList' => $staffList,
        ];
    }

    public function update($request): void
    {
        $tenantId = tenant('id');
        $fineDeduction = FineDeduction::where('tenant_id', $tenantId)->findOrFail($request->id);
        $validated = $request->validated();

        $exists = FineDeduction::where('tenant_id', $tenantId)
            ->where('staff_id', $validated['staff_id'])
            ->where('apply_year', $validated['apply_year'])
            ->where('applicable_month', $validated['applicable_month'])
            ->whereNull('deleted_at')
            ->where('id', '!=', $fineDeduction->id)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'apply_year' => ['A fine deduction already exists for this staff with the selected year and month.']
            ]);
        }

        $updated = $fineDeduction->update([
            'staff_id' => $validated['staff_id'],
            'apply_year' => $validated['apply_year'],
            'applicable_month' => $validated['applicable_month'],
            'amount' => $validated['amount'],
            'reason' => $validated['reason'],
            'ModifiedBy' => auth()->user()->id,
        ]);

        if ($updated) {
            userActivityLogs('Fine Deduction Updated and id is '.$request->id.' By User ID: ' . auth()->user()->id . '', HumanResourceLog::class);
        }
    }

    public function destroy($request): void
    {
        $fineDeduction = FineDeduction::where('tenant_id', tenant('id'))->findOrFail($request->id);
        $deleted = $fineDeduction->delete();

        if ($deleted) {
            userActivityLogs('Fine Deduction Deleted and id is '.$request->id.' By User ID: ' . auth()->user()->id . '', HumanResourceLog::class);
        }
    }
}