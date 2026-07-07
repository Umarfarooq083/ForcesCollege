<?php

namespace App\Http\Requests\Exam;

use App\Models\ExamStudent;
use Illuminate\Foundation\Http\FormRequest;

class ExamStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Normalize data before validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'StudentIds' => collect($this->input('StudentIds'))
                ->map(fn($student) => is_array($student) ? $student['id'] : $student)
                ->toArray()
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ExamId'     => 'required|integer|exists:exam,id',
            'ClassId'        => 'required|integer|exists:classes,id',
            'ExamSubjectId'  => 'required|integer|exists:exam_subjects,id',
            'StudentIds'     => 'required|array',
            'StudentIds.*'   => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $currentSession = fetchCurrentSession();
                    $exists = ExamStudent::where('tenant_id', tenant('id'))
                        ->where('SessionId', $currentSession['id'])
                        ->where('ExamId', $this->ExamId)
                        ->where('ClassId', $this->ClassId)
                        ->where('ExamSubjectId', $this->ExamSubjectId)
                        ->where('StudentId', $value)
                        ->with('Student')
                        ->first();
                    if ($exists) {
                        $fail("Student Name: {$exists?->Student?->FirstName} {$exists?->Student?->LastName} is already registered for this exam subject.");
                    }
                }
            ],
        ];
    }
}
