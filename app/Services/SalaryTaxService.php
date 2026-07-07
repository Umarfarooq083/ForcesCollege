<?php

namespace App\Services;

use App\Models\SalaryTax;
use App\Models\HumanResourceLog;
use Illuminate\Validation\ValidationException;

class SalaryTaxService
{
    public function index(): object
    {
        return SalaryTax::with('staff')
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

        $exists = SalaryTax::where('tenant_id', $tenantId)
            ->where('staff_id', $validated['staff_id'])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'staff_id' => ['A salary tax already exists for this staff.']
            ]);
        }

        $created = SalaryTax::create([
            'tenant_id' => $tenantId,
            'staff_id' => $validated['staff_id'],
            'amount' => $validated['amount'],
            'reason' => $validated['reason'],
            'CreatedBy' => auth()->user()->id,
        ]);

        if ($created) {
            userActivityLogs('Salary Tax Created and By User ID: ' . auth()->user()->id . '', HumanResourceLog::class);
        }
    }

    public function edit($request): object
    {
        $salaryTax = SalaryTax::with('staff')
            ->where('tenant_id', tenant('id'))
            ->findOrFail($request->id);

        $staffList = \App\Models\Staff::select('id', 'FirstName', 'LastName')
            ->where('tenant_id', tenant('id'))
            ->where('IsActive', 1)
            ->get();

        return (object) [
            'salaryTax' => $salaryTax,
            'staffList' => $staffList,
        ];
    }

    public function update($request): void
    {
        $tenantId = tenant('id');
        $salaryTax = SalaryTax::where('tenant_id', $tenantId)->findOrFail($request->id);
        $validated = $request->validated();

        $exists = SalaryTax::where('tenant_id', $tenantId)
            ->where('staff_id', $validated['staff_id'])
            ->where('id', '!=', $salaryTax->id)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'staff_id' => ['A salary tax already exists for this staff.']
            ]);
        }

        $updated = $salaryTax->update([
            'staff_id' => $validated['staff_id'],
            'amount' => $validated['amount'],
            'reason' => $validated['reason'],
            'ModifiedBy' => auth()->user()->id,
        ]);

        if ($updated) {
            userActivityLogs('Salary Tax Updated and id is '.$request->id.' By User ID: ' . auth()->user()->id . '', HumanResourceLog::class);
        }
    }

    public function destroy($request): void
    {
        $salaryTax = SalaryTax::where('tenant_id', tenant('id'))->findOrFail($request->id);
        $deleted = $salaryTax->delete();

        if ($deleted) {
            userActivityLogs('Salary Tax Deleted and id is '.$request->id.' By User ID: ' . auth()->user()->id . '', HumanResourceLog::class);
        }
    }
}