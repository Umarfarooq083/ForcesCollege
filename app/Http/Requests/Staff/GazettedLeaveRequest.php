<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GazettedLeaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = tenant('id');

        return [
            'title' => 'required|string|max:255',
            'date' => [
                'required',
                'date',
                Rule::unique('gazetted_leaves', 'date')
                    ->where(fn($q) => $q->where('tenant_id', $tenantId)->whereNull('deleted_at'))
                    ->ignore($this->id),
            ],
            // 'status' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'date.unique' => 'A holiday already exists on this date.',
        ];
    }
}