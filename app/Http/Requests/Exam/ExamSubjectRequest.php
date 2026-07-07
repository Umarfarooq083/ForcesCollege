<?php

namespace App\Http\Requests\Exam;

use Clue\Redis\Protocol\Model\Request;
use Illuminate\Foundation\Http\FormRequest;

class ExamSubjectRequest extends FormRequest
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
        // ✅ DELETE case
        if ($this->isMethod('delete')) {
            return [
                'id' => ['required', 'exists:exam_subjects,id'],
            ];
        }

        
        if ($this->isMethod('put')) {
            return [
                    'Title' => 'required|string|max:255',
                    'Date' => 'required|date',
                    'Time' => 'required',
                    'Duration' => 'nullable|integer',
                    'CreditHours' => 'nullable|integer',
                    'MarksMax' => 'required|integer',
                    'MarksMin' => 'required|integer',
                    'RoomNo' => 'nullable',
            ];
        }


        return [
            'Title' => ['required', 'string', 'max:255'],
            'ExamId' => ['required', 'integer', 'exists:exam,id'],
            'ClassId' => ['required', 'integer', 'exists:classes,id'],
            'rows' => ['required', 'array', 'min:1'],
            'rows.*.SubjectId'   => ['required', 'integer', 'exists:subjects,id'],
            'rows.*.Date'        => ['required', 'date'],
            'rows.*.Time'        => ['required', 'date_format:H:i'],
            'rows.*.Duration'    => ['nullable', 'integer', 'min:1'],
            'rows.*.CreditHours' => ['nullable', 'integer', 'min:1'],
            'rows.*.MarksMax'    => ['required', 'integer', 'gte:rows.*.MarksMin'],
            'rows.*.MarksMin'    => ['required', 'integer', 'lte:rows.*.MarksMax'],
            'rows.*.RoomNo'      => ['nullable', 'max:255'],
        ];
    }

    public function attributes()
    {
      if ($this->isMethod('put')) {
        // On update (PUT) you can return empty or only relevant fields
            return [
                'Title'       => 'Title',
                'Date'        => 'Date',
                'Time'        => 'Time',
                'Duration'    => 'Duration',
                'CreditHours' => 'Credit Hours',
                'MarksMax'    => 'Maximum Marks',
                'MarksMin'    => 'Minimum Marks',
                'RoomNo'      => 'Room Number',
            ];
        }
        return [
            'rows.*.SubjectId'   => 'SubjectId',
            'rows.*.Date'        => 'Date',
            'rows.*.Time'        => 'Time',
            'rows.*.Duration'    => 'Duration',
            'rows.*.CreditHours' => 'Credit Hours',
            'rows.*.MarksMax'    => 'Maximum Marks',
            'rows.*.MarksMin'    => 'Minimum Marks',
            'rows.*.RoomNo'      => 'Room Number',
        ];
    }
}
