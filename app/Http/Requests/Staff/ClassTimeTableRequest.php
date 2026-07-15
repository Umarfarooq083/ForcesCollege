<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class ClassTimeTableRequest extends FormRequest
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
            'StaffId' => 'required|exists:staff,id',
            'Day' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'rows' => 'required|array|min:1',
            'rows.*.ClassId' => 'required|exists:classes,id',
            'rows.*.SectionId' => 'required|exists:sectiones,id',
            'rows.*.SubjectId' => 'required|exists:subjects,id',
            'rows.*.TimeFrom' => 'required|date_format:H:i',
            'rows.*.TimeTo' => 'required|date_format:H:i|after:rows.*.TimeFrom',
            'DatesArray' => 'required|array',
            'DatesArray.0' => 'required',
            'DatesArray.*' => 'date_format:Y-m-d',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'StaffId.required' => 'Please select a staff member.',
            'StaffId.exists' => 'The selected staff member does not exist.',
            'Day.required' => 'Please select a day.',
            'Day.in' => 'Please select a valid day.',
            'rows.required' => 'At least one timetable row is required.',
            'rows.min' => 'At least one timetable row is required.',
            'rows.*.ClassId.required' => 'Class is required for each row.',
            'rows.*.ClassId.exists' => 'The selected class does not exist.',
            'rows.*.SectionId.required' => 'Section is required for each row.',
            'rows.*.SectionId.exists' => 'The selected section does not exist.',
            'rows.*.SubjectId.required' => 'Subject is required for each row.',
            'rows.*.SubjectId.exists' => 'The selected subject does not exist.',
            'rows.*.TimeFrom.required' => 'Start time is required for each row.',
            'rows.*.TimeFrom.date_format' => 'Start time must be in HH:MM format.',
            'rows.*.TimeTo.required' => 'End time is required for each row.',
            'rows.*.TimeTo.date_format' => 'End time must be in HH:MM format.',
            'rows.*.TimeTo.after' => 'End time must be after start time.',

            // DatesArray custom messages
            'DatesArray.required' => 'At least one date must be generated.',
            'DatesArray.array' => 'Dates must be provided as an array.',
            'DatesArray.0.required' => 'At least one date is required in the dates array.',
            'DatesArray.*.date_format' => 'Each date must be in the format YYYY-MM-DD.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Add any custom logic to prepare data before validation if needed
    }
}
