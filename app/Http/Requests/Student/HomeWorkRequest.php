<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class HomeWorkRequest extends FormRequest
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
            'classId' => 'required|integer|exists:classes,id',
            'class' => 'required|string|max:255',
            'sectionId' => 'required|integer|exists:sectiones,id',
            'section' => 'required|string|max:255',
            'subjectId' => 'required|integer|exists:subjects,id',
            'subject' => 'required|string|max:255',
            'homeworkDate' => 'required|date',
            'submissionDate' => 'required|date|after_or_equal:homeworkDate',
            'description' => 'nullable|string|max:500',
            'attachDocumentPath' => ['nullable','mimes:pdf,doc,docx,jpg,jpeg,png','max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'ClassId.required' => 'Please select a class.',
            'SectionId.required' => 'Please select a section.',
            'SubjectId.required' => 'Please select a subject.',
            'HomeWorkDate.required' => 'Homework date is required.',
            'SubmissionDate.required' => 'Submission date is required.',
            'SubmissionDate.after_or_equal' => 'Submission date must be after or equal to homework date.',
            'attachDocumentPath.mimes' => 'Only PDF, Word, and image files are allowed.',
            'attachDocumentPath.max' => 'Attached file must not exceed 2MB.',
        ];
    }
}
