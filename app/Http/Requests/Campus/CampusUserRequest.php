<?php

namespace App\Http\Requests\Campus;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CampusUserRequest extends FormRequest
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

        if ($request->method() === 'PUT') {
            return [
                'id' => 'required|exists:users,id',
                'name' => ['required', 'string'],
                'roles_ids' => ['required', 'array', 'min:1'],
                'phone_no' => ['nullable', 'digits_between:4,15'],
                'roles_ids.*.id' => ['required', 'integer', 'exists:roles,id'],
            ];
        }

        return [
            'name' => ['required', 'string'],
            'email' => [
                'required',
                'string',
                'email',
                Rule::unique('users')->where(function ($query) {
                    return $query->where('tenant_id', tenant('id'));
                }),
            ],
            'password' => ['required', 'string'],
            'phone_no' => ['nullable', 'digits_between:4,15'],
            'roles_ids' => ['required', 'array', 'min:1'],
            'roles_ids.*.id' => ['required', 'integer', 'exists:roles,id'],
        ];
    }
}
