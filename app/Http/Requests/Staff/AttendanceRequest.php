<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceRequest extends FormRequest
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
            'attendance' => 'required|array',
            'attendance.*.status' => 'required|in:0,1,2',
            'attendance.*.start_time' => 'nullable|date_format:H:i',
            'attendance.*.end_time' => 'nullable|date_format:H:i',
            'department_id' => 'nullable|integer',
            'all_staff' => 'nullable',
            'date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    $date = \Carbon\Carbon::parse($value);
                    if ($date->isSunday()) {
                        $fail('Sunday dates are not allowed for staff attendance.');
                    }
                },
            ],
        ];
    }
}
