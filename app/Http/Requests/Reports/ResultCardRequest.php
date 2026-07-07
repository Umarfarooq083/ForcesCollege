<?php

namespace App\Http\Requests\Reports;

use Illuminate\Foundation\Http\FormRequest;

class ResultCardRequest extends FormRequest
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
       // agar route `getexam.types` hai
        if ($this->routeIs('getexam.types')) {
            return [
                'examtermid' => 'required|integer|exists:exam_terms,id',
            ];
        }

        // agar route `get.exam.students` hai
        if ($this->routeIs('get.exam.students')) {
            return [
                'ExamId'  => 'required|integer|exists:exam_students,ExamId',
                'ClassId' => 'required|integer|exists:exam_students,ClassId',
            ];
        }

        // agar route `get.exam.students` hai
        if ($this->routeIs('result.card')) {
            return [
                'examtermid' => 'required|integer|exists:exam_terms,id',
                'examid'  => 'required|integer|exists:exam_students,ExamId',
                'classid' => 'required|integer|exists:exam_students,ClassId',
                'studentid' => 'required|integer|exists:exam_students,StudentId',
                'session'  => 'required',
            ];
        }

        return [];
    }
}
