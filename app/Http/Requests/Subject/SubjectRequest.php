<?php

namespace App\Http\Requests\Subject;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubjectRequest extends FormRequest
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
    public function rules(Request $request): array
    {
        return [
            'SubjectName' => [
                'required',
                'string',
                'max:255',
                Rule::unique('subjects')
                    ->where(fn($query) => $query->where('ClassId', $request->ClassId)->where('program_level_id',$request->program_level_id)->where('tenant_id',tenant('id')))
                    ->ignore($request->id), 
            ],
            'SubjectType' => 'required',
            'SubjectCode' => 'required',
            'ClassId' => 'required',
            'program_level_id' => 'required|exists:program_levels,id',
        ];
    }
}
