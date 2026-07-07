<?php

namespace App\Http\Requests\UploadContent;

use Illuminate\Foundation\Http\FormRequest;

class UploadContentRequest extends FormRequest
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
            'ContentTitle' => 'required',
            'ContentType' => 'required',
            'ClassId' => 'required',
            'subjectId' => 'nullable',
            'termId' => 'required',
            'monthId' => 'required',
            'weekId' => 'required',
            'UploadDate' => 'required',
            'Description' => 'nullable',
            // 'AvailableForAllCampuses' => 'nullable',
            'UploadContentGroupId' => 'required',
            'regionId' => 'required',
        ];
    }
}
