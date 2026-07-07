<?php

namespace App\Http\Requests\Fees;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FeeCollectionRequest extends FormRequest
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
        if($this->isMethod('get')) {
            return [
                'ChallanNo' => ['required', 'integer',
                    Rule::exists('generate_fee_challan', 'challan_no')->where('tenant_id', tenant('id'))
                ],
            ];
        }

        if($this->isMethod('post')) {
            return [
                'GenerateClassChallanId' => ['required', 'integer',
                    Rule::exists('generate_fee_challan', 'id')->where('tenant_id', tenant('id'))
                ],
                'balance_after_discount' => ['required', 'numeric', 'min:0'],
                'waived_amount' => ['nullable', 'numeric', 'min:0', 'lte:balance_after_discount'],
                'ReceivedAmount' => ['required', 'numeric', 'min:0','lte:balance_after_discount'],
                'note' => ['nullable', 'string'],
                'PaymentMode' => ['required', 'string'],
                'SubmitDate' => ['required', 'date', 'after_or_equal:'.now()->startOfMonth()->toDateString(), 'before_or_equal:ExpiryDate'],
            ];
        }
        return [];
    }
}
