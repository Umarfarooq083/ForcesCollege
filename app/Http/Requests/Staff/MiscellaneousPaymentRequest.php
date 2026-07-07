<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class MiscellaneousPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = tenant('id');

        return [
            'staff_id' => 'required|exists:staff,id',
            'apply_year' => 'required|integer|min:2020|max:2030',
            'applicable_month' => 'required|integer|min:1|max:12',
            'amount' => 'required|numeric|min:0',
            'reason' => 'required|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'staff_id.required' => 'The staff field is required.',
            'staff_id.exists' => 'The selected staff is invalid.',
            'apply_year.required' => 'The apply year field is required.',
            'apply_year.integer' => 'The apply year must be a valid year.',
            'applicable_month.required' => 'The applicable month field is required.',
            'applicable_month.integer' => 'The applicable month must be a valid month.',
            'amount.required' => 'The amount field is required.',
            'amount.numeric' => 'The amount must be a valid number.',
            'reason.required' => 'The reason field is required.',
        ];
    }
}