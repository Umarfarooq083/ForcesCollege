<?php

namespace App\Http\Requests\Fees;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OptionalFeesRequest extends FormRequest
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
            'ClassId' => ['required'],
            'FeesTypeNId' => ['required'],
            'CampusFeesMasterId' => ['required'],
            'SessionId' => ['required'],
            'SectionId' => ['required'],
            'StudentId' => ['required', 'array'],
            'StudentId.*' => [
                'required',
                Rule::unique('optional_fee_mapping', 'StudentId')
                    ->where(function ($query) use ($request) {
                        return $query->where('tenant_id', tenant('id'))
                            ->where('ClassId', $request->ClassId)
                            ->where('SectionId', $request->SectionId)
                            ->where('CampusFeesMasterId', $request->CampusFeesMasterId)
                            ->whereNull('deleted_at');
                    }),
            ],
            'FromMonth' => ['required', 'date_format:Y-m'],
            'ToMonth' => ['required', 'date_format:Y-m'],
        ];
    }

    public function messages()
    {
        return [
            'StudentId.*.unique' => 'This student already exists with the same class and campus fee master.',
        ];
    }
}
