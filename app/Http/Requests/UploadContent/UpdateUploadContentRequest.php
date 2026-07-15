<?php

namespace App\Http\Requests\UploadContent;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUploadContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'required|integer',
            'ContentTitle' => 'required',
            'ContentType' => 'required',
            'ClassId' => 'required',
            'subjectId' => 'nullable',
            'termId' => 'required',
            'monthId' => 'required',
            'weekId' => 'required',
            'UploadDate' => 'required',
            'Description' => 'nullable',
            'UploadContentGroupId' => 'required',
            'regionId' => 'required',
            'ContentFilePath' => 'nullable|file',
        ];
    }
}
