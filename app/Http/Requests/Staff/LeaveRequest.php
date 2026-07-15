<?php

namespace App\Http\Requests\Staff;

use App\Models\LeaveRequest as LeaveRequestModel;
use Illuminate\Foundation\Http\FormRequest;

class LeaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = tenant('id');
        $isEdit = $this->isMethod('put') || $this->filled('id');

        if ($isEdit) {
            return [
                'id' => 'required|exists:leave_requests,id',
                'leave_type' => 'required|string|max:50',
                'reason' => 'nullable|string',
                'staff_id' => 'required|exists:staff,id',
                'date' => [
                    'required',
                    'date',
                    function ($attribute, $value, $fail) use ($tenantId) {
                        $exists = LeaveRequestModel::query()
                            ->where('tenant_id', $tenantId)
                            ->where('staff_id', $this->staff_id)
                            ->where('date', $value)
                            ->whereNull('deleted_at')
                            ->where('id', '!=', $this->id)
                            ->exists();

                        if ($exists) {
                            $fail("A leave request already exists for the date: {$value}");
                        }
                    },
                ],
            ];
        }

        return [
            'staff_id' => 'required|exists:staff,id',

            'dates' => [
                'required',
                'array',
                'min:1',
                function ($attribute, $value, $fail) {
                    if (count($value) !== count(array_unique($value))) {
                        $fail('Same date cannot be selected more than once.');
                    }
                },
            ],

            'dates.*' => [
                'required',
                'date',
                'distinct',
                function ($attribute, $value, $fail) use ($tenantId) {
                    $exists = LeaveRequestModel::query()
                        ->where('tenant_id', $tenantId)
                        ->where('staff_id', $this->staff_id)
                        ->where('date', $value)
                        ->whereNull('deleted_at')
                        ->exists();

                    if ($exists) {
                        $fail("A leave request already exists for the date: {$value}");
                    }
                },
            ],

            'leave_type' => 'required|string|max:50',
            'reason' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'staff_id.required' => 'Staff is required.',
            'staff_id.exists' => 'Selected staff does not exist.',

            'dates.required' => 'At least one date is required.',
            'dates.array' => 'Dates must be an array.',
            'dates.min' => 'At least one date is required.',

            'dates.*.required' => 'Date is required.',
            'dates.*.date' => 'Invalid date format.',
            'dates.*.distinct' => 'Duplicate dates are not allowed.',

            'leave_type.required' => 'Leave type is required.',
            'leave_type.max' => 'Leave type may not be greater than 50 characters.',
        ];
    }
}
