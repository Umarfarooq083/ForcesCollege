<?php

namespace App\Http\Requests\Staff;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StaffRequest extends FormRequest
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
           'RolesId' => 'required|integer',
            'DesignationId' => 'required|integer',
            'DepartmentId' => 'required|integer',
            'FirstName' => 'required',
            'LastName' => 'required',
            'FatherName' => 'required',
            'MotherName' => 'required',
            'Email' => [
                'required',
                'email',
                    Rule::unique('staff', 'Email')->ignore($request->id),
            ],
            'Gender' => 'required',
            'DateOfBirth' => 'required|date|before:today',
            'DateOfJoining' => 'required|date|before_or_equal:today',
            'Phone' => 'required|digits_between:4,15',
            'EmergencyContactNumber' => 'required|digits_between:4,15',
            'MaritalStatus' => 'required',
            'CurrentAddress' => 'required',
            'PermanentAddress' => 'required',
            'Qualification' => 'required',
            'WorkExperience' => 'required',
            'Note' => 'nullable',
'BasicSalary' => 'required|integer',
             'TransportAllowance' => 'nullable|integer',
             'ComputerAllowance' => 'nullable|integer',
             'MobileAllowance' => 'nullable|integer',
             'RecreationAllowance' => 'nullable|integer',
             'HasProvidentFund' => 'nullable|boolean',
             'ProvidentFundAmount' => ['nullable', 'numeric', 'between:0,100'],
             'HasEOBI' => 'nullable|boolean',
             'EOBIAmount' => 'nullable|integer',
             'employerAmount' => 'nullable|integer',
             // 'CreateUser' => 'required',
        ];
    }
}
