<?php

namespace App\Http\Requests\StudentInquiry;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class InquiryRequest extends FormRequest
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
                'id' => 'required|exists:student_inquiries,id',
                'SessionId' => 'required ' ,
                'Date' => ['required'],
                'ClassId' => ['required'],
                'Name' => ['required'],
                'LastName' => 'nullable',
                'BirthDate' => ['required', 'date', 'before:today'],
                'Gender' => ['required'],
                'PreviousInstitute' => 'nullable',
                'Address' => 'nullable',
                'FatherName' => 'required',
                'FatherPhoneNo' => [
                    'required',
                    'regex:/^\+92\d{10}$/'
                ],
                'MotherName' => 'nullable',
                'MotherPhoneNo' => [
                    'nullable',
                    'regex:/^\+92\d{10}$/'
                ],
                'SourceId' => 'required|numeric',
                'ReferenceId' => ['required'],
                'cnic' => ['required'],
                'guardian_relation_id' => ['required'],
                'IsSmsSent' => 'nullable',
            ];
        }


         return [
                'SessionId' => 'required',
                'Date' => ['required'],
                'ClassId' => ['required'],
                'Name' => ['required'],
                'LastName' => 'nullable',
                'BirthDate' => ['required', 'date', 'before:today'],
                'Gender' => ['required'],
                'PreviousInstitute' => 'nullable',
                'Address' => 'nullable',
                'FatherName' => 'required',
                'FatherPhoneNo' => [
                    'required',
                    'regex:/^\+92\d{10}$/'
                ],
                'MotherName' => 'nullable',
                'MotherPhoneNo' => [
                    'nullable',
                    'regex:/^\+92\d{10}$/'
                ],
                'SourceId' => 'required|numeric',
                'ReferenceId' => ['required'],
                'cnic' => ['required'],
                'guardian_relation_id' => ['required'],
                'IsSmsSent' => 'nullable',
            ];
    }
}
