<?php

namespace App\Http\Requests\Fees;

use Illuminate\Foundation\Http\FormRequest;

class GenerateFeeChallanRequest extends FormRequest
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
            'ClassId' => 'required',
            'SectionId' => 'required',
            'StudentId' => ['required', 'array'],
            'StudentId.*' => ['required'],
            'ChallanMonth' => ['nullable', 'date_format:Y-m', 'required_without:ChallanMonths'],
            'ChallanMonths' => ['nullable', 'array', 'min:1', 'required_without:ChallanMonth'],
            'ChallanMonths.*' => ['required', 'date_format:Y-m', 'distinct'],
            'CombineMonths' => ['nullable', 'boolean'],
            'DueDate' => ['nullable', 'date', 'required_with:ChallanMonth'],
            'ExpiryDate' => ['nullable', 'date', 'required_with:ChallanMonth'],
        ];
    }
}
