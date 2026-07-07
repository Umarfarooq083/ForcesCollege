<?php

namespace App\Http\Controllers\APIImportData;

use App\Models\Api_imported_models\ImportedStudent;
use App\Models\Api_imported_models\ImportedStudentInquiry;
use Illuminate\Support\Facades\Hash;
use App\Models\GuardianInfo;
use App\Models\Student;
use App\Models\StudentInquiry;

class ImportStudent
{
    protected $ImportHelper;
    public function __construct(ImportHelper $ImportHelper)
    {
        $this->ImportHelper = $ImportHelper;
    }

    public function handleStudent($finalResponse)
    {
        set_time_limit(0);
        $current_tenant_id = $this->ImportHelper->getTenantId();
        ImportedStudent::truncate();
        //ImportedStudent::insert($finalResponse);
        collect($finalResponse)->chunk(150)->each(function ($chunk) {
            ImportedStudent::insert($chunk->toArray());
        });

        $ImportedStudentFullData = ImportedStudent::get();
        $ImportedStudentFullData = collect($ImportedStudentFullData);
        // CNIC Infromation Insertion into Guardia Info and assign ids to imported student  
        // Guardia Info Start From Here 
        $ImportedStudentData = $ImportedStudentFullData->whereNotNull('fatherCnic');
        $studentWithCnic = $ImportedStudentData->select('id', 'fatherCnic', 'fatherName');
        foreach ($studentWithCnic as $cnic) {
            $GuardianInfoExist = GuardianInfo::where('tenant_id', $current_tenant_id)->where('cnic', $cnic['fatherCnic'])->first();
            if ($GuardianInfoExist) {
                ImportedStudent::where('id', $cnic['id'])->update([
                    'internal_guardian_id' => $GuardianInfoExist->id
                ]);
            } else {
                $GuardianInfoCreate = new GuardianInfo();
                $GuardianInfoCreate->tenant_id = $current_tenant_id;
                $GuardianInfoCreate->cnic = $cnic['fatherCnic'];
                $GuardianInfoCreate->name = $cnic['fatherName'] ?? 'Father Name';
                $GuardianInfoCreate->save();

                ImportedStudent::where('id', $cnic['id'])->update([
                    'internal_guardian_id' => $GuardianInfoCreate->id
                ]);
            }
        }
        // Guardia Info End Here 

        // Start Student Inquiry Creation From Here 
        $ImportedStudentInquiryData = $ImportedStudentFullData->whereNotNull('admissionEnquiryId');
        foreach ($ImportedStudentInquiryData as $inquirycreate) {
            $StudentInquiryExist = StudentInquiry::where('imported_inquiry_id', $inquirycreate['id'])->where('tenant_id', $current_tenant_id)->first();
            $internal_class_id = $this->ImportHelper->getClassId($inquirycreate);
            $currentSessionId = $this->ImportHelper->getSessionId($inquirycreate);
            if ($StudentInquiryExist) {
                ImportedStudent::where('id', $inquirycreate['id'])->update([
                    'internal_enquiry_id' => $StudentInquiryExist->id
                ]);
            } else {
                $newStudentInquiryCreate = new StudentInquiry();
                $newStudentInquiryCreate->tenant_id = $current_tenant_id;
                $newStudentInquiryCreate->SessionId = $currentSessionId;
                $newStudentInquiryCreate->Name = $inquirycreate['firstName'];
                $newStudentInquiryCreate->LastName = $inquirycreate['lastName'];
                $newStudentInquiryCreate->StudentName = $inquirycreate['firstName'];
                $newStudentInquiryCreate->Phone = $inquirycreate['fatherPhone'];
                $newStudentInquiryCreate->Email = $inquirycreate['email'];
                $newStudentInquiryCreate->Address = $inquirycreate['permanentAddress'];
                $newStudentInquiryCreate->Date = $inquirycreate['createdDate'];
                $newStudentInquiryCreate->ClassId = $internal_class_id;
                $newStudentInquiryCreate->BirthDate = $inquirycreate['dateOfBirth'];
                $newStudentInquiryCreate->Gender = $inquirycreate['gender'];
                $newStudentInquiryCreate->FatherName = $inquirycreate['fatherName'];
                $newStudentInquiryCreate->FatherPhoneNo = $inquirycreate['fatherPhone'];
                $newStudentInquiryCreate->MotherName = $inquirycreate['motherName'];
                $newStudentInquiryCreate->MotherPhoneNo = $inquirycreate['motherPhone'];
                $newStudentInquiryCreate->IsActive = $inquirycreate['isActive'];
                $newStudentInquiryCreate->guardian_id = $inquirycreate['internal_guardian_id'];
                $newStudentInquiryCreate->imported_inquiry_id = $inquirycreate['id'];
                $newStudentInquiryCreate->save();

                ImportedStudent::where('id', $inquirycreate['id'])->update([
                    'internal_enquiry_id' => $newStudentInquiryCreate->id
                ]);
            }
        }
        // End Here Student Inquiry

        // Start Students Creation From Here
        $checkStudentExist = Student::withTrashed()->where('tenant_id', $current_tenant_id)->get();
        $checkStudentExist = collect($checkStudentExist);
        foreach ($ImportedStudentFullData as $s_key => $studentCreation) {
            $StudentExist = $checkStudentExist->where('imported_student_id', $studentCreation->id)->first();
            if (!$StudentExist) {

                // SessionId
                $currentSessionId = $this->ImportHelper->getSessionId($studentCreation);
                $Studentcreate = new Student();
                $internal_class_id = $this->ImportHelper->getClassId($studentCreation);
                $internal_section_id = $this->ImportHelper->getSectionId($studentCreation);
                $Studentcreate->SessionId = $currentSessionId;
                $Studentcreate->tenant_id = $current_tenant_id;
                $Studentcreate->IsActive = $studentCreation->isActive;
                $Studentcreate->CreatedDate = $studentCreation->createdDate;
                $Studentcreate->RollNumber = $studentCreation->rollNumber;
                $Studentcreate->IsOnlineAdmission = $studentCreation->isOnlineAdmission;
                $Studentcreate->IsStudentEnroll = $studentCreation->isStudentEnroll;
                $Studentcreate->ClassId = $internal_class_id;
                $Studentcreate->SectionId = $internal_section_id;
                $Studentcreate->AdmissionEnquiryId = $studentCreation->admissionEnquiryId;
                $Studentcreate->FirstName = $studentCreation->firstName;
                $Studentcreate->LastName = $studentCreation->lastName;
                $Studentcreate->Gender = $studentCreation->gender;
                $Studentcreate->DateOfBirth = $studentCreation->dateOfBirth;
                $Studentcreate->BformNo = $studentCreation->bformNo;
                $Studentcreate->MobileNumber = $studentCreation->mobileNumber;
                $Studentcreate->Email = $studentCreation->email;
                $Studentcreate->AdmissionDate = $studentCreation->admissionDate;
                $Studentcreate->StudentHouseId = $studentCreation->studentHouseId;
                $Studentcreate->FatherName = $studentCreation->fatherName;
                $Studentcreate->FatherPhone = $studentCreation->fatherPhone;
                $Studentcreate->FatherOccupation = $studentCreation->fatherOccupation;
                $Studentcreate->FatherCnic = $studentCreation->fatherCnic;
                $Studentcreate->MotherName = $studentCreation->motherName;
                $Studentcreate->MotherPhone = $studentCreation->motherPhone;
                $Studentcreate->MotherOccupation = $studentCreation->motherOccupation;
                $Studentcreate->GuardianName = $studentCreation->guardianName;
                $Studentcreate->GuardianRelation = $studentCreation->guardianRelation;
                $Studentcreate->GuardianEmail = $studentCreation->guardianEmail;
                $Studentcreate->GuardianPhone = $studentCreation->guardianPhone;
                $Studentcreate->GuardianOccupation = $studentCreation->guardianOccupation;
                $Studentcreate->GuardianAddress = $studentCreation->guardianAddress;
                $Studentcreate->IfGuardianAddressIsCurrentAddress = $studentCreation->ifGuardianAddressIsCurrentAddress;
                $Studentcreate->CurrentAddress = $studentCreation->currentAddress;
                $Studentcreate->PermanentAddress = $studentCreation->permanentAddress;
                $Studentcreate->BankAccountNumber = $studentCreation->bankAccountNumber;
                $Studentcreate->BankName = $studentCreation->bankName;
                $Studentcreate->IFSCCode = $studentCreation->ifscCode;
                $Studentcreate->IsDisable = $studentCreation->isDisable;
                $Studentcreate->DisableReasonId = $studentCreation->disableReasonId;
                $Studentcreate->Password = Hash::make($studentCreation->password);
                $Studentcreate->MobDeviceId = $studentCreation->mobDeviceId;
                $Studentcreate->deleted_at = ($studentCreation['isDeleted'] == 1) ? now() : NULL;
                $Studentcreate->imported_student_id = $studentCreation->id;
                $Studentcreate->save();
            }
        }
        // End Here Student Creation
    }

    public function handleStudentInquiry($finalResponse)
    {
        ini_set('max_execution_time', 0);
        ImportedStudentInquiry::truncate();
        ImportedStudentInquiry::insert($finalResponse);
        $current_tenant_id = $this->ImportHelper->getTenantId();
        $importedImportedStudentInquiry = ImportedStudentInquiry::get();
        foreach ($importedImportedStudentInquiry as $importedInquiry) {
            $existStudentInquiry = StudentInquiry::where('tenant_id', $current_tenant_id)->where('imported_lost_inquiry', $importedInquiry->id)->first();
            if ($existStudentInquiry) {
                ImportedStudentInquiry::where('id', $importedInquiry['id'])->update([
                    'internal_student_inquiry' => $existStudentInquiry->id
                ]);
            } else {
                $internal_class_id = $this->ImportHelper->getClassId($importedInquiry);
                $currentSessionId = $this->ImportHelper->getSessionId($importedInquiry);
                $newStudentInquiryCreate = new StudentInquiry();
                $newStudentInquiryCreate->tenant_id = $current_tenant_id;
                $newStudentInquiryCreate->SessionId = $currentSessionId;
                $newStudentInquiryCreate->Name = $importedInquiry['name'];
                $newStudentInquiryCreate->LastName = $importedInquiry['name'];
                $newStudentInquiryCreate->StudentName = $importedInquiry['studentName'];
                $newStudentInquiryCreate->Phone = $importedInquiry['phone'];
                $newStudentInquiryCreate->Email = $importedInquiry['email'];
                $newStudentInquiryCreate->Address = $importedInquiry['address'];
                $newStudentInquiryCreate->Date = $importedInquiry['date'];
                $newStudentInquiryCreate->BirthDate = $importedInquiry['date'];
                $newStudentInquiryCreate->Gender = 'Male';
                $newStudentInquiryCreate->ClassId = $internal_class_id;
                $newStudentInquiryCreate->IsActive = $importedInquiry['isActive'];
                $newStudentInquiryCreate->imported_lost_inquiry = $importedInquiry['id'];
                $newStudentInquiryCreate->save();

                ImportedStudentInquiry::where('id', $importedInquiry['id'])->update([
                    'internal_student_inquiry' => $newStudentInquiryCreate->id
                ]);
            }
        }
    }
}
