<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { useForm } from '@inertiajs/vue3';
import 'vue-multiselect/dist/vue-multiselect.min.css'
import { formatCNIC, formatPhone } from '@/Utils/formatters';

import { computed, watch, ref } from 'vue'



const props = defineProps({
    inquiry: Object,
    student: Object,
    guardianrelation: Object,
    sections: Object,
    classList: Object,
});

const filteredSections = computed(() => {
    if (!form.ClassId) return []

    return props.sections.filter(
        sec => sec.ClassId === form.ClassId
    )
})


const gendarList = [
    { id: 1, name: 'Male' },
    { id: 2, name: 'Female' },
];

const form = useForm({
    id:props.student.id,
    Email:props.student?.Email,
    SessionName: props.student?.student_session?.name || '',
    RollNumber: props.student?.RollNumber || '', 
    IsOnlineAdmission: props.student?.IsOnlineAdmission || '', 
    IsStudentEnroll: '', 
    ClassId: props.student?.ClassId || '',
    SectionId: props.student?.SectionId || ''  , 
    FirstName: props.student?.FirstName || '', 
    LastName: props.student?.LastName || '', 
    Gender: props.student?.Gender || '', 
    DateOfBirth: props.student?.DateOfBirth || '', 
    BformNo: props.student?.BformNo || '', 
    Religion: props.student?.Religion || '' , 
    Caste: props.student?.Caste || '', 
    MobileNumber: props.student?.MobileNumber || '', 
    AdmissionDate:  props.student?.AdmissionDate || '', 
    StudentPhotoPath: props.student?.StudentPhotoPath || '', 
    BloodGroup: props.student?.BloodGroup || '', 
    Height: props.student?.Height || '', 
    Weight: props.student?.Weight || '', 
    AsOnDate: props.student?.AsOnDate || '', 
    MedicalHistory: props.student?.MedicalHistory || '',
    FatherName: props.student?.FatherName || '',
    FatherPhone: formatPhone(props.student?.FatherPhone || ''),
    FatherOccupation: props.student?.FatherOccupation || '',
    FatherCnic: props.student?.FatherCnic || '',
    FatherCnicName: props.student?.FatherCnic || '',
    FatherPhotoPath: props.student?.FatherPhotoPath || '',
    MotherName: props.student?.MotherName || '' ,
    MotherPhone: formatPhone(props.student?.MotherPhone || ''),
    MotherOccupation: props.student?.MotherOccupation || '' ,
    MotherPhotoPath: props.student?.MotherPhotoPath || '',
    IfGuardianIsValue: props.student?.IfGuardianIsValue || '',
    GuardianName: props.student?.GuardianName || '',
    GuardianRelation: props.student?.GuardianRelation || '',
    GuardianEmail: props.student?.GuardianEmail || '',
    GuardianPhotoPath: props.student?.GuardianPhotoPath || '',
    GuardianPhone: formatPhone(props.student?.GuardianPhone || ''),
    Is_Guardian: props.student?.Is_Guardian === 1,
    GuardianOccupation: props.student?.GuardianOccupation || '',
    GuardianAddress: props.student?.GuardianAddress || '',
    IfGuardianAddressIsCurrentAddress: props.student?.IfGuardianAddressIsCurrentAddress || '',
    CurrentAddress: props.student?.CurrentAddress || '',
    IfPermanentAddressIsCurrentAddress: props.student?.IfPermanentAddressIsCurrentAddress || '',
    PermanentAddress: props.student?.PermanentAddress || '',
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
 
});

watch(
    () => form.ClassId,
    () => {
        form.SectionId = ''
    }
)

const imagePreview = ref(null);

const handleImageUpload = (e) => {
    const file = e.target.files[0];
    form.StudentPhotoPath = file;

    if (file) {
        imagePreview.value = URL.createObjectURL(file);
    }
};


const submitForm = () => {
    form.transform((data) => ({
        ...data,
        _method: 'put'
    })).post(route('student.update'), {
        forceFormData: true
    });
};


</script>

<template>
    <Head title="Edit Student" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Edit Student</h2>
        </template>
        <!-- <form @submit.prevent="form.put(route('student.update'))" > -->
        <form @submit.prevent="submitForm" >
            <input type="hidden" v-model="form.tenant_id" />
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Edit Student</div>
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
                                                        <TextInput type="text" v-model="form.FirstName" class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.FirstName" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Last Name" />
                                                        <TextInput  type="text" v-model="form.LastName" class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.LastName" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Gender" />
                                                        <select class="form-control" v-model="form.Gender">
                                                            <option selected disabled value="">Select a gender</option>
                                                            <option v-for="gen in gendarList" :key="gen.name" :value="gen.name">
                                                                {{ gen.name }}
                                                            </option>
                                                        </select>

                                                        <InputError class="mt-2" :message="form.errors.Gender" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Date Of Birth" />
                                                        <TextInput  type="date" v-model="form.DateOfBirth" class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.DateOfBirth" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="B-Form No" />
                                                        <TextInput  type="text" v-model="form.BformNo" class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.BformNo" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Religion" />
                                                        <TextInput  type="text" v-model="form.Religion" class="form-control"  />
                                                        <InputError class="mt-2" :message="form.errors.Religion" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    
                                                    <div class="mb-3">
                                                        <InputLabel  value="Mobile No" />
                                                        <TextInput  type="text" v-model="form.MobileNumber" class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.MobileNumber" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Email" />
                                                        <TextInput  type="email" v-model="form.Email" class="form-control" />
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
                                                        <TextInput  type="text" v-model="form.Caste" class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.Caste" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Admission Date" />
                                                        <TextInput  type="date" v-model="form.AdmissionDate" class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.AdmissionDate" />
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Blood Group" />
                                                        <TextInput  type="text" v-model="form.BloodGroup" class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.BloodGroup" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Height" />
                                                        <TextInput  type="text" v-model="form.Height" class="form-control"  />
                                                        <InputError class="mt-2" :message="form.errors.Height" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Weight" />
                                                        <TextInput  type="text" v-model="form.Weight" class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.Weight" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="As On Date" />
                                                        <TextInput  type="date" v-model="form.AsOnDate" class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.AsOnDate" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Medical History" />
                                                        <TextInput  type="text" v-model="form.MedicalHistory" class="form-control" />
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
                                                        <select class="form-control" v-model="form.ClassId">
                                                            <option selected disabled value="">Select a class</option>
                                                            <option v-for="cls in classList" :key="cls.id" :value="cls.id">
                                                                {{ cls.ClassName }}
                                                            </option>
                                                        </select>
                                                        
                                                        <InputError class="mt-2" :message="form.errors.ClassId" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel value="Section" /> <span class="text-danger font-12 position-absolute">★</span>
                                                        <select class="form-control" v-model="form.SectionId">
                                                            <option disabled value="">Select a section</option>
                                                            <option v-for="sec in filteredSections" :key="sec.id" :value="sec.id">
                                                                {{ sec.SectionName }}
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
                                                        <InputLabel  value="Current Address" />
                                                        <TextInput  type="text" v-model="form.CurrentAddress" class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.CurrentAddress" />
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Permanent Address" />
                                                        <TextInput  type="text" v-model="form.PermanentAddress" class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.PermanentAddress" />
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
                                                        <TextInput type="text" v-model="form.FatherName" class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.FatherName" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Father Phone" />
                                                        <TextInput  type="text" v-model="form.FatherPhone" class="form-control" @input="form.FatherPhone = formatPhone(form.FatherPhone)"/>
                                                        <InputError class="mt-2" :message="form.errors.FatherPhone" />
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Father Occupation" />
                                                        <TextInput  type="text" v-model="form.FatherOccupation" class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.FatherOccupation" />
                                                    </div>
                                                </div>
                                               
                                                <!-- {{ student }} -->
                                                <div class="col-md-4" v-if="form.FatherCnic">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Father CNIC" />
                                                        <input type="hidden" v-model="form.FatherCnic" />
                                                        <input type="hidden" v-model="form.FatherCnicName" />
                                                        <TextInput  type="text" v-model="form.FatherCnic" class="form-control" readonly/>
                                                        <InputError class="mt-2" :message="form.errors.FatherCnic" />
                                                    </div>
                                                </div>

                                                 <div class="col-md-4" v-else>
                                                    <div class="mb-3">
                                                        <InputLabel  value="Father CNIC" />
                                                        <input type="hidden" v-model="form.FatherCnic" />
                                                        <TextInput  type="text" v-model="form.FatherCnicName" class="form-control"  @input="form.FatherCnicName = formatCNIC(form.FatherCnicName)" placeholder="11111-1111111-1" required/>
                                                        <InputError class="mt-2" :message="form.errors.FatherCnicName" />
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="mb-3">
                                                        <InputLabel value="Relation" />
                                                        <input type="hidden" v-model="form.GuardianRelation" />
                                                        <select class="form-control" v-model="form.GuardianRelation">
                                                            <option selected disabled value="">Select a guardian</option>
                                                            <option v-for="gardian in guardianrelation" :key="gardian.id" :value="gardian.id">
                                                                {{ gardian.relationName }}
                                                            </option>
                                                        </select>
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
                                                        <TextInput type="text" v-model="form.MotherName" class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.FatherMotherNameName" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Mother Phone" />
                                                        <TextInput  type="text" v-model="form.MotherPhone" class="form-control" @input="form.MotherPhone = formatPhone(form.MotherPhone)"/>
                                                        <InputError class="mt-2" :message="form.errors.MotherPhone" />
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Mother Occupation" />
                                                        <TextInput  type="text" v-model="form.MotherOccupation" class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.MotherOccupation" />
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
                                                        <TextInput type="text" v-model="form.GuardianName" class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.GuardianName" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Guardian Email" />
                                                        <TextInput  type="email" v-model="form.GuardianEmail" class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.GuardianEmail" />
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Guardian Phone" />
                                                        <TextInput  type="text" v-model="form.GuardianPhone" class="form-control" @input="form.GuardianPhone = formatPhone(form.GuardianPhone)"/>
                                                        <InputError class="mt-2" :message="form.errors.GuardianPhone" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Guardian Occupation" />
                                                        <TextInput  type="text" v-model="form.GuardianOccupation" class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.GuardianOccupation" />
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Guardian Address" />
                                                        <TextInput  type="text" v-model="form.GuardianAddress" class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.GuardianAddress" />
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
                                                        <InputLabel  value="Bank Account Number" />
                                                        <TextInput  type="text" v-model="form.BankAccountNumber" class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.BankAccountNumber" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Bank Name" />
                                                        <TextInput  type="text" v-model="form.BankName" class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.BankName" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="IFSC Code" />
                                                        <TextInput  type="text" v-model="form.IFSCCode" class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.IFSCCode" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="National Identification Number" />
                                                        <TextInput  type="text" v-model="form.NationalIdentificationNumber" class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.NationalIdentificationNumber" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Local Identification Number" />
                                                        <TextInput  type="text" v-model="form.LocalIdentificationNumber" class="form-control" />
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
                                                        <TextInput  type="text" v-model="form.PreviousSchoolDetails" class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.PreviousSchoolDetails" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <InputLabel  value="Note" />
                                                        <TextInput  type="text" v-model="form.Note" class="form-control" />
                                                        <InputError class="mt-2" :message="form.errors.Note" />
                                                    </div>
                                                </div>
                                                
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Student Photo Path" />
                                        <TextInput type="file" @change="handleImageUpload" class="form-control" accept="image/*" />
                                        <InputError class="mt-2" :message="form.errors.StudentPhotoPath" />
                                    </div>
                                    <img v-if="imagePreview || form.StudentPhotoPath" :src="imagePreview ? imagePreview : '/storage/' + form.StudentPhotoPath"
                                        width="120" class="mt-2" />
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
