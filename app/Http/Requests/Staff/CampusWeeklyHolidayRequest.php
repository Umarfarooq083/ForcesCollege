<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;

class CampusWeeklyHolidayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'campus_id' => 'required|exists:campuses,id',
            'weekend_day' => 'required|in:saturday,sunday,both,none',
        ];
    }

    public function messages(): array
    {
        return [
            'campus_id.required' => 'Campus is required.',
            'weekend_day.required' => 'Weekend day selection is required.',
        ];
    }
}
