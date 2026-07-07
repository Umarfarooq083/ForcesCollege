<?php

namespace App\Http\Requests\Fees;

use Illuminate\Foundation\Http\FormRequest;

class FeeTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'FeesCode' => ['required'],
            'FeeName' => ['required'],
            'FeeCycle' => ['required'],
            // 'ApplicableMonth' => ['required'],
            'Isroyality' => 'required|boolean',
            'IsRefundable' => 'required|boolean',
            'IsOptional' => 'required|boolean',
            'Description' => 'nullable',
        ];
    }
}
