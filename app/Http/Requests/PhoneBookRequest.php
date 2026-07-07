<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PhoneBookRequest extends FormRequest
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
         $tenantId = tenant('id');

        if ($this->isMethod('post')) {
            return [
                'name' => ['required', 'string', 'max:255'],

                'contact_no' => [ 'required', 'string','max:15',
                    Rule::unique('phone_books')->where(fn($q) => $q->where('tenant_id', $tenantId)),
                ],

                'phonebook_group_id' => ['required', 'integer', 'max:255', 
                 Rule::exists('phonebook_groups', 'id')->where(fn($q) => $q->where('tenant_id', $tenantId)),
                ],
            ];
        }

        if ($this->isMethod('put')) {
            return [
                'id' => ['required', 'integer',
                    Rule::exists('phone_books', 'id')->where(fn($q) => $q->where('tenant_id', $tenantId)),
                ],

                'name' => ['required', 'string', 'max:255'],
                
                'contact_no' => [ 'required', 'string','max:15',
                    Rule::unique('phone_books')->where(fn($q) => $q->where('tenant_id', $tenantId))->ignore($this->id),
                ],

                'phonebook_group_id' => ['required', 'integer', 'max:255', 
                    Rule::exists('phonebook_groups', 'id')->where(fn($q) => $q->where('tenant_id', $tenantId)),
                ],
            ];
        }

        return [];
    }
}
