<?php

namespace App\Http\Requests\Program;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProgramLevelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'programm_id' => [
                'required',
                'integer',
                Rule::exists('programs', 'id')->where(fn($query) => $query->where('tenant_id', tenant('id'))),
            ],
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'status' => 'required|boolean',
        ];
    }
}