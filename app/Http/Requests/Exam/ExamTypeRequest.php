<?php

namespace App\Http\Requests\Exam;

use Illuminate\Foundation\Http\FormRequest;

class ExamTypeRequest extends FormRequest
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
            'ExamName' => 'required|string|max:255',
            'SessionId' => 'required|exists:lms_sessions,id',
            'ExamTermId' => 'required|exists:exam_terms,id',
            'ResultDeclarationDate' => 'required|date',
            'Description' => 'nullable|string|max:1000',
            'IsPublishResult' => 'boolean',
        ];
    }
}
