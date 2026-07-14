<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class SecurityDeductionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'staff_id' => 'required|exists:staff,id',
            'apply_year' => 'required|integer|min:2020|max:2030',
            'from_month' => 'required|integer|min:1|max:12',
            'to_month' => 'required|integer|min:1|max:12|gte:from_month',
            'amount' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'staff_id.required' => 'The staff field is required.',
            'staff_id.exists' => 'The selected staff is invalid.',
            'apply_year.required' => 'The apply year field is required.',
            'apply_year.integer' => 'The apply year must be a valid year.',
            'from_month.required' => 'The from month field is required.',
            'from_month.integer' => 'The from month must be a valid month.',
            'from_month.min' => 'The from month must be between 1 and 12.',
            'to_month.required' => 'The to month field is required.',
            'to_month.integer' => 'The to month must be a valid month.',
            'to_month.gte' => 'The to month must be greater than or equal to from month.',
            'amount.required' => 'The amount field is required.',
            'amount.numeric' => 'The amount must be a valid number.',
        ];
    }
}
