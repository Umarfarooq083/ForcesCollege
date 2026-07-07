<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { useForm } from '@inertiajs/vue3';
import 'vue-multiselect/dist/vue-multiselect.min.css';

const props = defineProps({
    student: Object,
    guardianrelation: Object,
});



const form = useForm({
    SessionName: props.student?.student_session?.name || '',
    RollNumber: props.student?.RollNumber || '', 
    IsOnlineAdmission: props.student?.IsOnlineAdmission || '', 
    IsStudentEnroll: '', 
    ClassName: props.student?.class?.ClassName || ''  , 
    SectionName: props.student?.section?.SectionName || ''  , 
    FirstName: props.student?.FirstName || '', 
    LastName: props.student?.LastName || '', 
    Gender: props.student?.Gender || '', 
    DateOfBirth: props.student?.DateOfBirth || '', 
    BformNo: props.student?.BformNo || '', 
    Religion: props.student?.Religion || '' , 
    Caste: props.student?.Caste || '', 
    MobileNumber: props.student?.MobileNumber || '', 
    Email: props.student?.Email || '', 
    AdmissionDate:  props.student?.AdmissionDate || '', 
    StudentPhotoPath: props.student?.StudentPhotoPath || '', 
    BloodGroup: props.student?.BloodGroup || '', 
    Height: props.student?.Height || '', 
    Weight: props.student?.Weight || '', 
    AsOnDate: props.student?.AsOnDate || '', 
    MedicalHistory: props.student?.MedicalHistory || '',
    FatherName: props.student?.FatherName || '',
    FatherPhone: props.student?.FatherPhone || '',
    FatherOccupation: props.student?.FatherOccupation || '',
    FatherCnic: props.student?.FatherCnic || '',
    FatherPhotoPath: props.student?.FatherPhotoPath || '',
    MotherName: props.student?.MotherName || '' ,
    MotherPhone: props.student?.MotherPhone || '',
    MotherOccupation: props.student?.MotherOccupation || '' ,
    MotherPhotoPath: props.student?.MotherPhotoPath || '',
    IfGuardianIsValue: props.student?.IfGuardianIsValue || '',
    GuardianName: props.student?.GuardianName || '',
    GuardianRelation: props.guardianrelation || '',
    GuardianEmail: props.student?.GuardianEmail || '',
    GuardianPhotoPath: props.student?.GuardianPhotoPath || '',
    GuardianPhone: props.student?.GuardianPhone || '',
    GuardianOccupation: props.student?.GuardianOccupation || '',
    GuardianAddress: props.student?.GuardianAddress || '',
    IfGuardianAddressIsCurrentAddress: props.student?.IfGuardianAddressIsCurrentAddress || '',
    CurrentAddress: props.student?.CurrentAddress || '',
    IfPermanentAddressIsCurrentAddress: props.student?.IfPermanentAddressIsCurrentAddress || '',
    PermanentAddress: props.student?.PermanentAddress || '',
    RouteId: props.student?.RouteId || '',
    HostelId: props.student?.HostelId || '',
    HostelRoomId: props.student?.HostelRoomId || '',
    BankAccountNumber: props.student?.BankAccountNumber || '',
    BankName: props.student?.BankName || '',
    IFSCCode: props.student?.IFSCCode || '',
    NationalIdentificationNumber: props.student?.NationalIdentificationNumber || '',
    LocalIdentificationNumber: props.student?.LocalIdentificationNumber || '',
    RTE: props.student?.RTE || '',
    PreviousSchoolDetails: props.student?.PreviousSchoolDetails || '',
    Note: props.student?.Note || '',
    StudentUploadDocumentsTitle1: props.student?.StudentUploadDocumentsTitle1 || '',
    StudentUploadDocumentPath1: '',
    StudentUploadDocumentsTitle2: '',
    StudentUploadDocumentPath2: '',
    StudentUploadDocumentsTitle3: '',
    StudentUploadDocumentPath3: '',
    StudentUploadDocumentsTitle4: '',
    StudentUploadDocumentPath4: '',
    IsDisable: props.student?.IsDisable || false,
    DisableReasonId: props.student?.DisableReasonId || '',
    MobDeviceId: props.student?.MobDeviceId || '',
    FcmDeviceToken: props.student?.FcmDeviceToken || '',
    Is_Guardian: props.student?.Is_Guardian === 1,
    
});

</script>

<template>

    <Head title="Student details" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Student details</h2>
        </template>
        <form @submit.prevent="form.post(route('student.submit'))" >
            <input type="hidden" v-model="form.tenant_id" />
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Student details</div>
                        <div class="body cstm_body">
                            <div class="row">
                                <div class="col-md-6">
                                     <div class="card">
                                        <div class="header">Student Basic Information</div>
                                        <div class="body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="First Name" />
                                                        <TextInput type="text" v-model="form.FirstName" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.FirstName" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Last Name" />
                                                        <TextInput  type="text" v-model="form.LastName" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.LastName" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Gender" />
                                                        <TextInput  type="text" v-model="form.Gender" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.Gender" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Date Of Birth" />
                                                        <TextInput  type="text" v-model="form.DateOfBirth" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.DateOfBirth" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="B-Form No" />
                                                        <TextInput  type="text" v-model="form.BformNo" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.BformNo" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Religion" />
                                                        <TextInput  type="text" v-model="form.Religion" class="form-control" readonly />
                                                        <InputError class="mt-2" :message="form.errors.Religion" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Mobile No" />
                                                        <TextInput  type="text" v-model="form.MobileNumber" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.MobileNumber" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Email" />
                                                        <TextInput  type="text" v-model="form.Email" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.Email" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                     <div class="card">
                                        <div class="header">Student Other Information</div>
                                        <div class="body">
                                            <div class="row">
                                                 <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Caste" />
                                                        <TextInput  type="text" v-model="form.Caste" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.Caste" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Admission Date" />
                                                        <TextInput  type="text" v-model="form.AdmissionDate" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.AdmissionDate" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Student Photo Path" />
                                                        <TextInput type="file" @input="form.StudentPhotoPath = $event.target.files[0]" class="form-control" accept="image/*" />
                                                        <InputError class="mt-2" :message="form.errors.StudentPhotoPath" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Blood Group" />
                                                        <TextInput  type="text" v-model="form.BloodGroup" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.BloodGroup" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Height" />
                                                        <TextInput  type="text" v-model="form.Height" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.Height" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Weight" />
                                                        <TextInput  type="text" v-model="form.Weight" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.Weight" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="As On Date" />
                                                        <TextInput  type="text" v-model="form.AsOnDate" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.AsOnDate" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Medical History" />
                                                        <TextInput  type="text" v-model="form.MedicalHistory" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.MedicalHistory" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="header">Academic Information</div>
                                        <div class="body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Session" />
                                                        <TextInput  type="text" v-model="form.SessionName" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.SessionId" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Roll Number" />
                                                        <TextInput  type="text" v-model="form.RollNumber" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.RollNumber" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Class" />
                                                        <input type="hidden" v-model="form.ClassId" />
                                                        <TextInput  type="text" v-model="form.ClassName" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.ClassId" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Section" /> <span class="text-danger font-12 position-absolute">★</span>
                                                        <TextInput  type="text" v-model="form.SectionName" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.SectionId" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <div class="flex items-center">
                                                            <input id="IsOnlineAdmission" type="checkbox" v-model="form.IsOnlineAdmission" class="mr-2" />
                                                            <InputLabel for="IsOnlineAdmission" value="IsOnlineAdmission" class="mb-0" readonly/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <div class="flex items-center">
                                                            <input id="IsStudentEnroll" type="checkbox" v-model="form.IsStudentEnroll" class="mr-2" />
                                                            <InputLabel for="IsStudentEnroll" value="IsStudentEnroll" class="mb-0" readonly/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="header">Address Information</div>
                                        <div class="body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Current Address" />
                                                        <TextInput  type="text" v-model="form.CurrentAddress" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.CurrentAddress" />
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Permanent Address" />
                                                        <TextInput  type="text" v-model="form.PermanentAddress" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.PermanentAddress" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <div class="flex items-center">
                                                            <input id="IfPermanentAddressIsCurrentAddress" type="checkbox" v-model="form.IfPermanentAddressIsCurrentAddress" class="mr-2" readonly/>
                                                            <InputLabel for="IfPermanentAddressIsCurrentAddress" value="IfPermanentAddressIsCurrentAddress" class="mb-0" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                 <div class="col-md-6">
                                    <div class="card">
                                        <div class="header">Father Information</div>
                                        <div class="body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Father Name" />
                                                        <TextInput type="text" v-model="form.FatherName" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.FatherName" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Father Phone" />
                                                        <TextInput  type="text" v-model="form.FatherPhone" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.FatherPhone" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Father Occupation" />
                                                        <TextInput  type="text" v-model="form.FatherOccupation" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.FatherOccupation" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Father CNIC" />
                                                        <TextInput  type="text" v-model="form.FatherCnic" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.FatherCnic" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Father Photo Path" />
                                                        <TextInput type="file" @input="form.FatherPhotoPath = $event.target.files[0]" class="form-control" accept="image/*" />
                                                        <InputError class="mt-2" :message="form.errors.FatherPhotoPath" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Relation" />
                                                        <TextInput type="text" v-model="form.GuardianRelation" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.GuardianRelation" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="header">Mother Information</div>
                                        <div class="body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Mother Name" />
                                                        <TextInput type="text" v-model="form.MotherName" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.FatherMotherNameName" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Mother Phone" />
                                                        <TextInput  type="text" v-model="form.MotherPhone" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.MotherPhone" />
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Mother Occupation" />
                                                        <TextInput  type="text" v-model="form.MotherOccupation" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.MotherOccupation" />
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Mother Photo Path" />
                                                        <TextInput type="file" @input="form.MotherPhotoPath = $event.target.files[0]" class="form-control" accept="image/*" />
                                                        <InputError class="mt-2" :message="form.errors.MotherPhotoPath" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="header">Guardian Information</div>
                                        <div class="body">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label>
                                                        <input type="checkbox" v-model="form.Is_Guardian" />
                                                        Is Guardian?
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="row" v-if="form.Is_Guardian">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Guardian Name" />
                                                        <TextInput type="text" v-model="form.GuardianName" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.GuardianName" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Guardian Email" />
                                                        <TextInput  type="text" v-model="form.GuardianEmail" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.GuardianEmail" />
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Guardian Photo Path" />
                                                        <TextInput type="file" @input="form.GuardianPhotoPath = $event.target.files[0]" class="form-control" accept="image/*" />
                                                        <InputError class="mt-2" :message="form.errors.GuardianPhotoPath" />
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Guardian Phone" />
                                                        <TextInput  type="text" v-model="form.GuardianPhone" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.GuardianPhone" />
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Guardian Occupation" />
                                                        <TextInput  type="text" v-model="form.GuardianOccupation" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.GuardianOccupation" />
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Guardian Address" />
                                                        <TextInput  type="text" v-model="form.GuardianAddress" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.GuardianAddress" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <div class="flex items-center">
                                                            <input id="IfGuardianIsValue" type="checkbox" v-model="form.IfGuardianIsValue" class="mr-2" readonly/>
                                                            <InputLabel for="IfGuardianIsValue" value="IfGuardianIsValue" class="mb-0" readonly/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <div class="flex items-center">
                                                            <input id="IfGuardianAddressIsCurrentAddress" type="checkbox" v-model="form.IfGuardianAddressIsCurrentAddress" class="mr-2" readonly/>
                                                            <InputLabel for="IfGuardianAddressIsCurrentAddress" value="IfGuardianAddressIsCurrentAddress" class="mb-0" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="header">Bank & Hostel Information</div>
                                        <div class="body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Route ID" />
                                                        <TextInput type="text" v-model="form.RouteId" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.RouteId" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Hostel ID" />
                                                        <TextInput  type="text" v-model="form.HostelId" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.HostelId" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Hostel Room ID" />
                                                        <TextInput  type="text" v-model="form.HostelRoomId" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.HostelRoomId" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Bank Account Number" />
                                                        <TextInput  type="text" v-model="form.BankAccountNumber" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.BankAccountNumber" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Bank Name" />
                                                        <TextInput  type="text" v-model="form.BankName" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.BankName" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="IFSC Code" />
                                                        <TextInput  type="text" v-model="form.IFSCCode" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.IFSCCode" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="National Identification Number" />
                                                        <TextInput  type="text" v-model="form.NationalIdentificationNumber" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.NationalIdentificationNumber" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Local Identification Number" />
                                                        <TextInput  type="text" v-model="form.LocalIdentificationNumber" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.LocalIdentificationNumber" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="header">Other Information</div>
                                        <div class="body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Previous School Details" />
                                                        <TextInput  type="text" v-model="form.PreviousSchoolDetails" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.PreviousSchoolDetails" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Note" />
                                                        <TextInput  type="text" v-model="form.Note" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.Note" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Disable Reason ID" />
                                                        <TextInput  type="text" v-model="form.DisableReasonId" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.DisableReasonId" />
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Mob Device ID" />
                                                        <TextInput  type="text" v-model="form.MobDeviceId" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.MobDeviceId" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Fcm Device Token" />
                                                        <TextInput  type="text" v-model="form.FcmDeviceToken" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.FcmDeviceToken" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <div class="flex items-center">
                                                            <input id="RTE" type="checkbox" v-model="form.RTE" class="mr-2" readonly/>
                                                            <InputLabel for="RTE" value="RTE" class="mb-0" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <div class="flex items-center">
                                                            <input id="IsDisable" type="checkbox" v-model="form.IsDisable" class="mr-2" readonly/>
                                                            <InputLabel for="IsDisable" value="IsDisable" class="mb-0" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                              
                                  <div class="col-md-6">
                                    <div class="card">
                                        <div class="header">Documents Information</div>
                                        <div class="body">
                                            <div class="mt-4 col-md-12" >
                                                <div class="p-3 shadow-sm card" v-for="st_document in student.student_documents">
                                                    <div class="row" >
                                                      
                                                        <div class="mb-3 col-md-5">
                                                            <InputLabel value="Title" /><br>
                                                            {{ st_document.Title }}                                                          
                                                        </div>
                                                     
                                                        <div class="mb-3 col-md-5">
                                                            <InputLabel value="Document" /><br>
                                                            <img :src="st_document.document_url" style="width: 100px; height: 100px;" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
              
            </div>
        </form>
    </AuthenticatedLayout>
</template>
