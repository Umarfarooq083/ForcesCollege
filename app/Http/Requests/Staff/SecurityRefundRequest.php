<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class SecurityRefundRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'staff_id' => 'required|exists:staff,id',
            'apply_date' => 'required|date',
            'applicable_month' => 'required|integer|min:1|max:12',
            'amount' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'staff_id.required' => 'The staff field is required.',
            'staff_id.exists' => 'The selected staff is invalid.',
            'apply_date.required' => 'The apply date field is required.',
            'apply_date.date' => 'The apply date must be a valid date.',
            'applicable_month.required' => 'The applicable month field is required.',
            'applicable_month.integer' => 'The applicable month must be a valid month.',
            'amount.required' => 'The amount field is required.',
            'amount.numeric' => 'The amount must be a valid number.',
        ];
    }
}
