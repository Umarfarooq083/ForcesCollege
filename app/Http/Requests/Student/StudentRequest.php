<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentRequest extends FormRequest
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
            'SchoolId' => 'nullable|integer',
            'IsActive' => 'nullable|boolean',
            'CreatedBy' => 'nullable|integer',
            'CreatedDate' => 'nullable|date',
            'ModifiedBy' => 'nullable|integer',
            'ModifiedDate' => 'nullable|date',
            'SessionId' => 'nullable|integer',
            'RollNumber' => 'nullable|string|max:50',
            // 'IsOnlineAdmission' => 'nullable|boolean',
            // 'IsStudentEnroll' => 'nullable|boolean',
            'ClassId' => 'nullable|integer',
            'SectionId' => 'required|integer',
            'AdmissionEnquiryId' => 'nullable|integer',

            'FirstName' => 'nullable|string|max:100',
            'LastName' => 'nullable|string|max:100',
            'Gender' => 'nullable|string|in:Male,Female,Other',
            'DateOfBirth' => 'nullable|date',
            'BformNo' => 'nullable|string|max:50',
            'StudentCategoryId' => 'nullable|integer',
            'Religion' => 'nullable|string|max:50',
            'Caste' => 'nullable|string|max:50',
            'MobileNumber' => 'nullable|string|max:15',
            'Email' => [
                'nullable',
                'email',
                Rule::unique('students', 'Email')->where('tenant_id',tenant('id'))->ignore($request->id), 
            ],
            'AdmissionDate' => 'required|date',
            'StudentPhotoPath' => 'nullable',
            'BloodGroup' => 'nullable|string|max:10',
            'StudentHouseId' => 'nullable|integer',
            'Height' => 'nullable|numeric',
            'Weight' => 'nullable|numeric',
            'AsOnDate' => 'nullable|date',
            'MedicalHistory' => 'nullable|string',

            'FatherName' => 'nullable|string|max:100',
            'FatherPhone' => 'nullable|string|max:15',
            'FatherOccupation' => 'nullable|string|max:100',
            // 'FatherCnic' => 'nullable|integer|max:20',
            // 'FatherCnicName' => 'required|string|max:20',

            'FatherPhotoPath' => 'nullable',

            'MotherName' => 'nullable|string|max:100',
            'MotherPhone' => 'nullable|string|max:15',
            'MotherOccupation' => 'nullable|string|max:100',
            'MotherPhotoPath' => 'nullable',

            // 'IfGuardianIsValue' => 'nullable|boolean',
            'GuardianName' => 'nullable|string|max:100',
            'GuardianRelation' => 'nullable|integer|max:50',
            'GuardianEmail' => 'nullable|email|max:100',
            'GuardianPhotoPath' => 'nullable',
            'GuardianPhone' => 'nullable|string|max:15',
            'GuardianOccupation' => 'nullable|string|max:100',
            'GuardianAddress' => 'nullable|string|max:255',
            // 'IfGuardianAddressIsCurrentAddress' => 'nullable|boolean',

            'CurrentAddress' => 'nullable|string|max:255',
            // 'IfPermanentAddressIsCurrentAddress' => 'nullable|boolean',
            'PermanentAddress' => 'nullable|string|max:255',

            'RouteId' => 'nullable|integer',
            'HostelId' => 'nullable|integer',
            'HostelRoomId' => 'nullable|integer',

            'BankAccountNumber' => 'nullable|string|max:50',
            'BankName' => 'nullable|string|max:100',
            'IFSCCode' => 'nullable|string|max:50',
            'NationalIdentificationNumber' => 'nullable|string|max:50',
            'LocalIdentificationNumber' => 'nullable|string|max:50',

            // 'RTE' => 'nullable|boolean',
            'PreviousSchoolDetails' => 'nullable|string|max:255',
            'Note' => 'nullable|string|max:500',

            'StudentUploadDocumentsTitle1' => 'nullable|string|max:100',
            'StudentUploadDocumentPath1' => 'nullable|string|max:255',
            'StudentUploadDocumentsTitle2' => 'nullable|string|max:100',
            'StudentUploadDocumentPath2' => 'nullable|string|max:255',
            'StudentUploadDocumentsTitle3' => 'nullable|string|max:100',
            'StudentUploadDocumentPath3' => 'nullable|string|max:255',
            'StudentUploadDocumentsTitle4' => 'nullable|string|max:100',
            'StudentUploadDocumentPath4' => 'nullable|string|max:255',

            // 'IsDisable' => 'nullable|boolean',
            'DisableReasonId' => 'nullable|integer',
            
            'Password' => 'nullable|string|min:6|max:100',
            'MobDeviceId' => 'nullable|string|max:100',
            'FcmDeviceToken' => 'nullable|string|max:255',

            'Is_Guardian' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'Password.nullable' => 'Password is required.',
            // 'Email.required' => 'Email is required.',
            'Email.email' => 'Please enter a valid email address.',
            'SectionId.required' => 'Section is required.',
        ];
    }
}
