<?php

namespace App\Http\Requests\Setting;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SiteSettingRequest extends FormRequest
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
        if ($this->isMethod('get')) {
            return [
                'setting_id' => ['required', 'integer',
                    Rule::exists('site_settings', 'id')->where(function ($query) {
                        return $query->where('tenant_id', tenant('id'));
                    }),
                ]
            ];
        }

        if ($this->isMethod('post')) {
            return [
                'name' => ['required', 'string'],
                'key' => [  'required', 'in:Fine_Late_Fee',
                    Rule::unique('site_settings')->where(function ($query){
                        return $query->where('tenant_id', tenant('id'));
                    })
                ],
                'value' => ['required', 'integer'],
            ];
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return [
                'name' => ['required', 'string'],
                'key' =>  ['required', 'string', 'in:Fine_Late_Fee'],
                'value' => ['required', 'integer'],
            ];
        }

        return [];
    }

    public function messages(): array
    {
        return [
            'key.unique' => 'This Setting is already created.',
        ];
    }
}
