<?php

namespace App\Services;

use App\Models\HumanResourceLog;
use App\Models\SecurityDeduction;
use Illuminate\Validation\ValidationException;

class SecurityDeductionService
{
    public function index(): object
    {
        return SecurityDeduction::with('staff')
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

        $hasOverlap = SecurityDeduction::where('tenant_id', $tenantId)
            ->where('staff_id', $validated['staff_id'])
            ->where('apply_year', $validated['apply_year'])
            ->whereNull('deleted_at')
            ->where(function ($query) use ($validated) {
                $query->whereBetween('from_month', [$validated['from_month'], $validated['to_month']])
                    ->orWhereBetween('to_month', [$validated['from_month'], $validated['to_month']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('from_month', '<=', $validated['from_month'])
                            ->where('to_month', '>=', $validated['to_month']);
                    });
            })
            ->exists();

        if ($hasOverlap) {
            throw ValidationException::withMessages([
                'apply_year' => ['A security deduction already exists for this staff within the selected month range.'],
            ]);
        }

        $created = SecurityDeduction::create([
            'tenant_id' => $tenantId,
            'staff_id' => $validated['staff_id'],
            'apply_year' => $validated['apply_year'],
            'from_month' => $validated['from_month'],
            'to_month' => $validated['to_month'],
            'amount' => $validated['amount'],
            'CreatedBy' => auth()->user()->id,
        ]);

        if ($created) {
            userActivityLogs('Security Deduction Created and By User ID: '.auth()->user()->id.'', HumanResourceLog::class);
        }
    }

    public function edit($request): object
    {
        $securityDeduction = SecurityDeduction::with('staff')
            ->where('tenant_id', tenant('id'))
            ->findOrFail($request->id);

        $staffList = \App\Models\Staff::select('id', 'FirstName', 'LastName')
            ->where('tenant_id', tenant('id'))
            ->where('IsActive', 1)
            ->get();

        return (object) [
            'securityDeduction' => $securityDeduction,
            'staffList' => $staffList,
        ];
    }

    public function update($request): void
    {
        $tenantId = tenant('id');
        $securityDeduction = SecurityDeduction::where('tenant_id', $tenantId)->findOrFail($request->id);
        $validated = $request->validated();

        $hasOverlap = SecurityDeduction::where('tenant_id', $tenantId)
            ->where('staff_id', $validated['staff_id'])
            ->where('apply_year', $validated['apply_year'])
            ->whereNull('deleted_at')
            ->where('id', '!=', $securityDeduction->id)
            ->where(function ($query) use ($validated) {
                $query->whereBetween('from_month', [$validated['from_month'], $validated['to_month']])
                    ->orWhereBetween('to_month', [$validated['from_month'], $validated['to_month']])
                    ->orWhere(function ($q) use ($validated) {
                        $q->where('from_month', '<=', $validated['from_month'])
                            ->where('to_month', '>=', $validated['to_month']);
                    });
            })
            ->exists();

        if ($hasOverlap) {
            throw ValidationException::withMessages([
                'apply_year' => ['A security deduction already exists for this staff within the selected month range.'],
            ]);
        }

        $updated = $securityDeduction->update([
            'staff_id' => $validated['staff_id'],
            'apply_year' => $validated['apply_year'],
            'from_month' => $validated['from_month'],
            'to_month' => $validated['to_month'],
            'amount' => $validated['amount'],
            'ModifiedBy' => auth()->user()->id,
        ]);

        if ($updated) {
            userActivityLogs('Security Deduction Updated and id is '.$request->id.' By User ID: '.auth()->user()->id.'', HumanResourceLog::class);
        }
    }

    public function destroy($request): void
    {
        $securityDeduction = SecurityDeduction::where('tenant_id', tenant('id'))->findOrFail($request->id);
        $deleted = $securityDeduction->delete();

        if ($deleted) {
            userActivityLogs('Security Deduction Deleted and id is '.$request->id.' By User ID: '.auth()->user()->id.'', HumanResourceLog::class);
        }
    }
}
