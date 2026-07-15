<?php

namespace App\Http\Requests\Exam;

use Illuminate\Foundation\Http\FormRequest;

class ExamGradeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        if (isset($this->ClassId) && is_array($this->ClassId)) {
            $this->merge([
                // sirf id extract karega aur ClassId array of integers banayega
                'ClassId' => collect($this->ClassId)->pluck('id')->toArray(),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return [
                'GradeName' => 'required|string|max:255',
                'ClassId' => 'required|array',
                'ClassId.*' => 'required|exists:classes,id',
                'PercentFrom' => 'required|numeric|min:0|max:100',
                'PercentUpt' => 'required|numeric|min:0|max:100|gte:PercentFrom',
                'Description' => 'nullable|string|max:1000',
            ];
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            // Update ke liye
            return [
                'GradeName' => 'required|string|max:255',
                'ClassId' => 'required|exists:classes,id', // yahan single id
                'PercentFrom' => 'required|numeric|min:0|max:100',
                'PercentUpt' => 'required|numeric|min:0|max:100|gte:PercentFrom',
                'Description' => 'nullable|string|max:1000',
            ];
        }

        return [];
    }
}
