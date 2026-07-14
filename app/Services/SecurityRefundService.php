<?php

namespace App\Services;

use App\Models\HumanResourceLog;
use App\Models\SecurityRefund;
use Illuminate\Validation\ValidationException;

class SecurityRefundService
{
    public function index(): object
    {
        return SecurityRefund::with('staff')
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

        $exists = SecurityRefund::where('tenant_id', $tenantId)
            ->where('staff_id', $validated['staff_id'])
            ->whereNull('deleted_at')
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'staff_id' => ['A security refund already exists for this staff. Each staff can have only one security refund record.'],
            ]);
        }

        $created = SecurityRefund::create([
            'tenant_id' => $tenantId,
            'staff_id' => $validated['staff_id'],
            'apply_date' => $validated['apply_date'],
            'applicable_month' => $validated['applicable_month'],
            'amount' => $validated['amount'],
            'CreatedBy' => auth()->user()->id,
        ]);

        if ($created) {
            userActivityLogs('Security Refund Created and By User ID: '.auth()->user()->id.'', HumanResourceLog::class);
        }
    }

    public function edit($request): object
    {
        $securityRefund = SecurityRefund::with('staff')
            ->where('tenant_id', tenant('id'))
            ->findOrFail($request->id);

        $staffList = \App\Models\Staff::select('id', 'FirstName', 'LastName')
            ->where('tenant_id', tenant('id'))
            ->where('IsActive', 1)
            ->get();

        return (object) [
            'securityRefund' => $securityRefund,
            'staffList' => $staffList,
        ];
    }

    public function update($request): void
    {
        $tenantId = tenant('id');
        $securityRefund = SecurityRefund::where('tenant_id', $tenantId)->findOrFail($request->id);
        $validated = $request->validated();

        $exists = SecurityRefund::where('tenant_id', $tenantId)
            ->where('staff_id', $validated['staff_id'])
            ->whereNull('deleted_at')
            ->where('id', '!=', $securityRefund->id)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'staff_id' => ['A security refund already exists for this staff. Each staff can have only one security refund record.'],
            ]);
        }

        $updated = $securityRefund->update([
            'staff_id' => $validated['staff_id'],
            'apply_date' => $validated['apply_date'],
            'applicable_month' => $validated['applicable_month'],
            'amount' => $validated['amount'],
            'ModifiedBy' => auth()->user()->id,
        ]);

        if ($updated) {
            userActivityLogs('Security Refund Updated and id is '.$request->id.' By User ID: '.auth()->user()->id.'', HumanResourceLog::class);
        }
    }

    public function destroy($request): void
    {
        $securityRefund = SecurityRefund::where('tenant_id', tenant('id'))->findOrFail($request->id);
        $deleted = $securityRefund->delete();

        if ($deleted) {
            userActivityLogs('Security Refund Deleted and id is '.$request->id.' By User ID: '.auth()->user()->id.'', HumanResourceLog::class);
        }
    }
}
