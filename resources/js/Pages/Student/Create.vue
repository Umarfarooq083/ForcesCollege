<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { useForm } from '@inertiajs/vue3';
import 'vue-multiselect/dist/vue-multiselect.min.css';
import { formatPhone } from '@/Utils/formatters';
import { computed } from 'vue';

const props = defineProps({
    inquiry: Object,
    classesList: Object,
    campusSession: Object,
    guardianInfo: Object,
    guardianrelation: Object,
});

const selectedSession = computed(() => {
    return props.campusSession.start_date_formatted + ' - ' + props.campusSession.end_date_formatted;
});


const form = useForm({
    inquiryId: props?.inquiry?.id,
    tenant_id: props?.inquiry?.tenant_id,
    SessionId: props?.inquiry?.SessionId,
    SessionName: selectedSession,
    RollNumber: '',
    ClassId: props.classesList?.id,
    ClassName: props.classesList?.ClassName,
    SectionId: '',
    FirstName: props.inquiry.Name,
    LastName: props.inquiry.LastName,
    Gender: props.inquiry.Gender,
    DateOfBirth: props.inquiry.BirthDate,
    BformNo: '',
    Religion: '',
    Caste: '',
    MobileNumber: props.inquiry?.FatherPhoneNo,
    WhatsAppNumber: props.inquiry?.MotherPhoneNo,
    Email: '',
    AdmissionDate: '',
    StudentPhotoPath: '',
    BloodGroup: '',
    StudentHouseId: '',
    Height: '',
    Weight: '',
    AsOnDate: '',
    MedicalHistory: '',
    FatherName: props.inquiry.FatherName,
    FatherPhone: props.inquiry.FatherPhoneNo,
    FatherOccupation: '',
    FatherCnicName: props?.inquiry?.guardian?.cnic,
    FatherCnic: props?.inquiry?.guardian?.id,
    FatherPhotoPath: '',
    MotherName: props.inquiry.MotherName,
    MotherPhone: props.inquiry.MotherPhoneNo,
    MotherOccupation: '',
    MotherPhotoPath: '',
    Is_Guardian: '',
    GuardianName: '',
    GuardianRelationName: props.guardianrelation?.relationName,
    GuardianRelation: props.guardianrelation?.id,
    GuardianEmail: '',
    GuardianPhotoPath: '',
    GuardianPhone: '+92',
    GuardianOccupation: '',
    GuardianAddress: '',
    CurrentAddress: props.inquiry.Address,
    PermanentAddress: '',
    BankAccountNumber: '',
    BankName: '',
    IFSCCode: '',
    NationalIdentificationNumber: '',
    LocalIdentificationNumber: '',
    PreviousSchoolDetails: props.inquiry.PreviousInstitute,
    Note: props.inquiry.Note,
    rows: []
});

const addRow = () => {
    form.rows.push({
        Title: '',
        document: '',
    });
};

const removeRow = (index) => {
    form.rows.splice(index, 1);
};

</script>

<template>

    <Head title="Create Student" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Create Student</h2>
        </template>
        
        <form @submit.prevent="form.post(route('student.submit'))">
            <input type="hidden" v-model="form.tenant_id" />
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Create Student</div>
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
                                                        <TextInput type="text" v-model="form.FirstName"
                                                            class="form-control" readonly />
                                                        <InputError class="mt-2" :message="form.errors.FirstName" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Last Name" />
                                                        <TextInput type="text" v-model="form.LastName"
                                                            class="form-control" readonly />
                                                        <InputError class="mt-2" :message="form.errors.LastName" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Gender" />
                                                        <TextInput type="text" v-model="form.Gender"
                                                            class="form-control" readonly />
                                                        <InputError class="mt-2" :message="form.errors.Gender" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Date Of Birth" />
                                                        <TextInput type="text" v-model="form.DateOfBirth"
                                                            class="form-control" readonly />
                                                        <InputError class="mt-2" :message="form.errors.DateOfBirth" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="B-Form No" />
                                                        <TextInput type="text" v-model="form.BformNo"
                                                            class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.BformNo" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Religion" />
                                                        <TextInput type="text" v-model="form.Religion"
                                                            class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.Religion" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Mobile No" />
                                                        <TextInput type="text" v-model="form.MobileNumber"
                                                            class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.MobileNumber" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Whatsapp No" />
                                                        <TextInput type="text" v-model="form.WhatsAppNumber"
                                                            class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.WhatsAppNumber" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Email" />
                                                        <TextInput type="text" v-model="form.Email"
                                                            class="form-control" placeholder="example@gmail.com" />
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
                                                        <InputLabel value="Caste" />
                                                        <TextInput type="text" v-model="form.Caste"
                                                            class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.Caste" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Admission Date" />
                                                        <TextInput type="date" v-model="form.AdmissionDate"
                                                            class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.AdmissionDate" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Student Photo Path" />
                                                        <TextInput type="file"
                                                            @input="form.StudentPhotoPath = $event.target.files[0]"
                                                            class="form-control" accept="image/*" />
                                                        <InputError class="mt-2"
                                                            :message="form.errors.StudentPhotoPath" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Blood Group" />
                                                        <TextInput type="text" v-model="form.BloodGroup"
                                                            class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.BloodGroup" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Height" />
                                                        <TextInput type="text" v-model="form.Height"
                                                            class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.Height" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Weight" />
                                                        <TextInput type="text" v-model="form.Weight"
                                                            class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.Weight" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="As On Date" />
                                                        <TextInput type="date" v-model="form.AsOnDate"
                                                            class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.AsOnDate" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Medical History" />
                                                        <TextInput type="text" v-model="form.MedicalHistory"
                                                            class="form-control" />
                                                        <InputError class="mt-2"
                                                            :message="form.errors.MedicalHistory" />
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
                                                        <input type="hidden" v-model="form.SessionId"
                                                            name="SessionId" />
                                                        <TextInput type="text" v-model="form.SessionName"
                                                            class="form-control" readonly />
                                                        <InputError class="mt-2" :message="form.errors.SessionId" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Roll Number" />
                                                        <TextInput type="text" v-model="form.RollNumber"
                                                            class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.RollNumber" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Class" />
                                                        <input type="hidden" v-model="form.ClassId" />
                                                        <TextInput type="text" v-model="form.ClassName"
                                                            class="form-control" readonly />
                                                        <InputError class="mt-2" :message="form.errors.ClassId" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Section" /> <span
                                                            class="text-danger font-12 position-absolute">★</span>
                                                        <select class="form-control" v-model="form.SectionId">
                                                            <option value="">Select Section</option>
                                                            <option v-for="section in classesList?.sections"
                                                                :key="section.id" :value="section.id">
                                                                {{ section?.SectionName }}
                                                            </option>
                                                        </select>
                                                        <InputError class="mt-2" :message="form.errors.SectionId" />
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
                                                        <InputLabel value="Current Address" />
                                                        <TextInput type="text" v-model="form.CurrentAddress"
                                                            class="form-control" readonly />
                                                        <InputError class="mt-2"
                                                            :message="form.errors.CurrentAddress" />
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <InputLabel value="Permanent Address" />
                                                        <TextInput type="text" v-model="form.PermanentAddress"
                                                            class="form-control" />
                                                        <InputError class="mt-2"
                                                            :message="form.errors.PermanentAddress" />
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
                                                        <TextInput type="text" v-model="form.FatherName"
                                                            class="form-control" readonly />
                                                        <InputError class="mt-2" :message="form.errors.FatherName" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Father Phone" />
                                                        <TextInput type="text" v-model="form.FatherPhone"
                                                            class="form-control" readonly />
                                                        <InputError class="mt-2" :message="form.errors.FatherPhone" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Father Occupation" />
                                                        <TextInput type="text" v-model="form.FatherOccupation"
                                                            class="form-control" />
                                                        <InputError class="mt-2"
                                                            :message="form.errors.FatherOccupation" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Father CNIC" />
                                                        <input type="hidden" v-model="form.FatherCnic" />
                                                        <TextInput type="text" v-model="form.FatherCnicName"
                                                            class="form-control" readonly />
                                                        <InputError class="mt-2" :message="form.errors.FatherCnicName" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Father Photo Path" />
                                                        <TextInput type="file"
                                                            @input="form.FatherPhotoPath = $event.target.files[0]"
                                                            class="form-control" accept="image/*" />
                                                        <InputError class="mt-2"
                                                            :message="form.errors.FatherPhotoPath" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Relation" />
                                                        <input type="hidden" v-model="form.GuardianRelation" />
                                                        <TextInput type="text" v-model="form.GuardianRelationName"
                                                            class="form-control" readonly />
                                                        <InputError class="mt-2"
                                                            :message="form.errors.GuardianRelation" />
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
                                                        <TextInput type="text" v-model="form.MotherName"
                                                            class="form-control" readonly />
                                                        <InputError class="mt-2"
                                                            :message="form.errors.FatherMotherNameName" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Mother Phone" />
                                                        <TextInput type="text" v-model="form.MotherPhone"
                                                            class="form-control" readonly />
                                                        <InputError class="mt-2" :message="form.errors.MotherPhone" />
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <InputLabel value="Mother Occupation" />
                                                        <TextInput type="text" v-model="form.MotherOccupation"
                                                            class="form-control" />
                                                        <InputError class="mt-2"
                                                            :message="form.errors.MotherOccupation" />
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <InputLabel value="Mother Photo Path" />
                                                        <TextInput type="file"
                                                            @input="form.MotherPhotoPath = $event.target.files[0]"
                                                            class="form-control" accept="image/*" />
                                                        <InputError class="mt-2"
                                                            :message="form.errors.MotherPhotoPath" />
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
                                                        <TextInput type="text" v-model="form.GuardianName"
                                                            class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.GuardianName" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Guardian Email" />
                                                        <TextInput type="email" v-model="form.GuardianEmail"
                                                            class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.GuardianEmail" />
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <InputLabel value="Guardian Photo Path" />
                                                        <TextInput type="file"
                                                            @input="form.GuardianPhotoPath = $event.target.files[0]"
                                                            class="form-control"  accept="image/*,.pdf" />
                                                        <InputError class="mt-2"
                                                            :message="form.errors.GuardianPhotoPath" />
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <InputLabel value="Guardian Phone" />
                                                        <TextInput type="text" v-model="form.GuardianPhone"
                                                            class="form-control" @input="form.GuardianPhone = formatPhone(form.GuardianPhone)" />
                                                        <InputError class="mt-2" :message="form.errors.GuardianPhone" />
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <InputLabel value="Guardian Occupation" />
                                                        <TextInput type="text" v-model="form.GuardianOccupation"
                                                            class="form-control" />
                                                        <InputError class="mt-2"
                                                            :message="form.errors.GuardianOccupation" />
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <InputLabel value="Guardian Address" />
                                                        <TextInput type="text" v-model="form.GuardianAddress"
                                                            class="form-control" />
                                                        <InputError class="mt-2"
                                                            :message="form.errors.GuardianAddress" />
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
                                                        <InputLabel value="Bank Account Number" />
                                                        <TextInput type="text" v-model="form.BankAccountNumber"
                                                            class="form-control" />
                                                        <InputError class="mt-2"
                                                            :message="form.errors.BankAccountNumber" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Bank Name" />
                                                        <TextInput type="text" v-model="form.BankName"
                                                            class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.BankName" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="IFSC Code" />
                                                        <TextInput type="text" v-model="form.IFSCCode"
                                                            class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.IFSCCode" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="National Identification Number" />
                                                        <TextInput type="text"
                                                            v-model="form.NationalIdentificationNumber"
                                                            class="form-control" />
                                                        <InputError class="mt-2"
                                                            :message="form.errors.NationalIdentificationNumber" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Local Identification Number" />
                                                        <TextInput type="text" v-model="form.LocalIdentificationNumber"
                                                            class="form-control" />
                                                        <InputError class="mt-2"
                                                            :message="form.errors.LocalIdentificationNumber" />
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
                                                        <InputLabel value="Previous School Details" />
                                                        <TextInput type="text" v-model="form.PreviousSchoolDetails"
                                                            class="form-control" readonly />
                                                        <InputError class="mt-2"
                                                            :message="form.errors.PreviousSchoolDetails" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Note" />
                                                        <TextInput type="text" v-model="form.Note"
                                                            class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.Note" />
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

                                            <PrimaryButton type="button" @click="addRow">
                                                <i class="fa fa-plus"></i> Add Documents
                                            </PrimaryButton>

                                            <div class="mt-4 col-md-12" v-if="form.rows.length > 0"
                                                v-for="(row, index) in form.rows" :key="index">
                                                <div class="p-3 shadow-sm card">
                                                    <div class="row">

                                                        <div class="mb-3 col-md-5">
                                                            <InputLabel value="Title" />
                                                            <TextInput placeholder="Title" type="text" v-model="row.Title"
                                                                class="form-control" required/>
                                                            <InputError class="mt-2"
                                                                :message="form.errors[`rows.${index}.Title`]" />
                                                        </div>
                                                     
                                                        <div class="mb-3 col-md-5">
                                                            <InputLabel value="Document" />
                                                            <TextInput type="file" @input="row.document = $event.target.files[0]" 
                                                                class="form-control"  required/>
                                                            <InputError class="mt-2"
                                                                :message="form.errors[`rows.${index}.document`]" />
                                                        </div>

                                                        <div class="mt-4 col-md-2 align-items-end text-end">
                                                            <button type="button" class="mt-2 btn btn-danger btn-sm"
                                                                @click="removeRow(index)">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 text-end">
                                    <PrimaryButton>Submit</PrimaryButton>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </form>
    </AuthenticatedLayout>
</template>
