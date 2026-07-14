<?php

namespace App\Http\Requests\Section;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class SectionRequest extends FormRequest
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

        if ($this->isMethod('put')) {
            return [
                'id' => 'required|integer|exists:sectiones,id',
                'SectionName' => 'required|string',
            ];
        }

        if ($this->isMethod('get')) {
            return [
                'id' => 'required|integer|exists:sectiones,id',
            ];
        }

        $rules = [
            'SectionName' => 'required|string',
            'ClassId' => 'required|integer',
            'SectionType' => 'required|integer',
            'Strength' => 'required|integer',
            'SchoolId' => 'nullable',
            'ClassGroupId' => 'nullable',
        ];

        return $rules;
    }
}
