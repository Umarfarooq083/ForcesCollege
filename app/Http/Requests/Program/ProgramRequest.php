<?php

namespace App\Http\Requests\Program;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProgramRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('programs')
                    ->where(fn($query) => $query->where('tenant_id', tenant('id')))
                    ->ignore($this->id),
            ],
            'type' => 'required|in:annual,semester',
            'duration' => 'required|string|max:255',
        ];
    }
}