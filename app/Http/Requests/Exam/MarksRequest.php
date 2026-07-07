<?php

namespace App\Http\Requests\Exam;

use Illuminate\Foundation\Http\FormRequest;

class MarksRequest extends FormRequest
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

        if ($this->isMethod('get')) {
            return [
                'ExamId' => ['required', 'integer', 'exists:exam,id'],
            ];
        }

        if ($this->isMethod('post')) {
            $maxMarks = $this->input('TotalMarks.MarksMax', 0);

            return [
                'ExamId' => ['required', 'integer', 'exists:exam,id'],
                'ClassId' => ['required', 'integer', 'exists:classes,id'],
                'SubjectId' => ['required', 'integer'],

                'StudentsData' => ['required', 'array', 'min:1'],
                'StudentsData.*.StudentId' => ['required', 'integer', 'exists:students,id'],
                'StudentsData.*.Marks' => ['nullable', 'numeric', 'min:0', 'max:'.$maxMarks],
                'StudentsData.*.Remarks' => ['nullable', 'string', 'max:255'],
            ];
        }

        if ($this->isMethod('put')) {
            $maxMarks = $this->input('TotalMarks.MarksMax', 0);

            return [
                'id' => ['required', 'integer', 'exists:exam_marks,id'],
                'ExamId' => ['required', 'integer', 'exists:exam,id'],
                'ClassId' => ['required', 'integer', 'exists:classes,id'],
                'SubjectId' => ['required', 'integer'],

                'StudentsData' => ['required', 'array', 'min:1'],
                'StudentsData.*.StudentId' => ['required', 'integer', 'exists:students,id'],
                'StudentsData.*.Marks' => ['nullable', 'numeric', 'min:0', 'max:'.$maxMarks],
                'StudentsData.*.Remarks' => ['nullable', 'string', 'max:255'],
            ];
        }

        return [];
    }

    public function messages(): array
    {
        return [
            'StudentsData.*.Marks.max' => 'Obtained marks cannot exceed total marks.',
            'StudentsData.*.Marks.min' => 'Obtained marks cannot be negative.',
        ];
    }
}
