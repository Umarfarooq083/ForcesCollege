<?php

namespace App\Services;

use App\Models\HumanResourceLog;
use App\Models\MiscellaneousPayment;
use Illuminate\Validation\ValidationException;

class MiscellaneousPaymentService
{
    public function index(): object
    {
        return MiscellaneousPayment::with('staff')
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

        $exists = MiscellaneousPayment::where('tenant_id', $tenantId)
            ->where('staff_id', $validated['staff_id'])
            ->where('apply_year', $validated['apply_year'])
            ->where('applicable_month', $validated['applicable_month'])
            ->whereNull('deleted_at')
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'apply_year' => ['A miscellaneous payment already exists for this staff with the selected year and month.'],
            ]);
        }

        $created = MiscellaneousPayment::create([
            'tenant_id' => $tenantId,
            'staff_id' => $validated['staff_id'],
            'apply_year' => $validated['apply_year'],
            'applicable_month' => $validated['applicable_month'],
            'amount' => $validated['amount'],
            'reason' => $validated['reason'],
            'CreatedBy' => auth()->user()->id,
        ]);

        if ($created) {
            userActivityLogs('Miscellaneous Payment Created and By User ID: '.auth()->user()->id.'', HumanResourceLog::class);
        }
    }

    public function edit($request): object
    {
        $miscellaneousPayment = MiscellaneousPayment::with('staff')
            ->where('tenant_id', tenant('id'))
            ->findOrFail($request->id);

        $staffList = \App\Models\Staff::select('id', 'FirstName', 'LastName')
            ->where('tenant_id', tenant('id'))
            ->where('IsActive', 1)
            ->get();

        return (object) [
            'miscellaneousPayment' => $miscellaneousPayment,
            'staffList' => $staffList,
        ];
    }

    public function update($request): void
    {
        $tenantId = tenant('id');
        $miscellaneousPayment = MiscellaneousPayment::where('tenant_id', $tenantId)->findOrFail($request->id);
        $validated = $request->validated();

        $exists = MiscellaneousPayment::where('tenant_id', $tenantId)
            ->where('staff_id', $validated['staff_id'])
            ->where('apply_year', $validated['apply_year'])
            ->where('applicable_month', $validated['applicable_month'])
            ->whereNull('deleted_at')
            ->where('id', '!=', $miscellaneousPayment->id)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'apply_year' => ['A miscellaneous payment already exists for this staff with the selected year and month.'],
            ]);
        }

        $updated = $miscellaneousPayment->update([
            'staff_id' => $validated['staff_id'],
            'apply_year' => $validated['apply_year'],
            'applicable_month' => $validated['applicable_month'],
            'amount' => $validated['amount'],
            'reason' => $validated['reason'],
            'ModifiedBy' => auth()->user()->id,
        ]);

        if ($updated) {
            userActivityLogs('Miscellaneous Payment Updated and id is '.$request->id.' By User ID: '.auth()->user()->id.'', HumanResourceLog::class);
        }
    }

    public function destroy($request): void
    {
        $miscellaneousPayment = MiscellaneousPayment::where('tenant_id', tenant('id'))->findOrFail($request->id);
        $deleted = $miscellaneousPayment->delete();

        if ($deleted) {
            userActivityLogs('Miscellaneous Payment Deleted and id is '.$request->id.' By User ID: '.auth()->user()->id.'', HumanResourceLog::class);
        }
    }
}
