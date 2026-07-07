<?php

namespace App\Http\Requests\Campus;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CampusRequest extends FormRequest
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
        $rules = [
            'SchoolId' => 'nullable',
            'IsActive' => 'required|boolean',
            'IsDeleted' => 'required|boolean',
            'OwnerName' => 'nullable|string|max:255',
            'Address' => 'required|string',
            'PhoneNo' => 'required|digits_between:4,15',
            'OfficePhone' => 'nullable|digits_between:4,15',
            'MobileNo' => 'nullable|digits_between:4,15',
            'Area' => 'nullable|string|max:255',
            'Rooms' => 'nullable|integer',
            'City' => 'required|string|max:100',
            'TotalFaculty' => 'nullable|integer',
            'Rental' => 'nullable|integer',
            'ContractDuration' => 'nullable|integer',
            'Comments' => 'nullable|string',
            'Other' => 'nullable|string',
            'AgreementPath' => 'nullable|string|max:255',
            'SchoolType' => 'nullable|string|max:100',
            'URL' => 'nullable|url',
            'Code' => 'nullable|max:100',
            'AccountNo' => 'nullable|max:100',
            'AccountTitle' => 'nullable|max:100',
            'bankName' => 'nullable|max:100',
            'BranchCode' => 'nullable|max:100',
            'Logo' => 'nullable|string|max:255',
            'IsAvailableForMobApp' => 'required|boolean',
            'SortOrder' => 'nullable|integer',
            'zoneid' => 'required',
            'regionid' => 'nullable|integer|exists:regions,id',
            'class_type_ids' => ['required', 'array', 'min:1'],
            'DomainName' => 'required|string|max:255',
        ];

        // For create (POST)
        if ($this->isMethod('POST')) {
            $rules['SchoolName'] = 'required|string|max:255|unique:campuses,SchoolName';
            $rules['EmailAddress'] = 'required|email|unique:campuses,EmailAddress';
        }

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $campusId = $this->route('id') ?? $this->id;

            $rules['id'] = 'required|exists:campuses,id';

            // unique with ignore
            $rules['SchoolName'] = [
                'required',
                'string',
                'max:255',
                Rule::unique('campuses', 'SchoolName')->ignore($campusId),
            ];
            $rules['EmailAddress'] = [
                'nullable', // 👈 changed from required to nullable
                'email',
                Rule::unique('campuses', 'EmailAddress')->ignore($campusId),
            ];

            // make these optional if not editable on update
            $rules['zoneid'] = 'nullable';
            $rules['regionid'] = 'nullable|integer|exists:regions,id';
            $rules['class_type_ids'] = 'nullable|array|min:1';
            $rules['DomainName'] = 'nullable|string|max:255';
            $rules['IsActive'] = 'sometimes|boolean';
            $rules['IsDeleted'] = 'sometimes|boolean';
        }


        return $rules;
    }
}
