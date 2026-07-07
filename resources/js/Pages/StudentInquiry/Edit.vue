<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { useForm } from '@inertiajs/vue3';
import { formatPhone } from '@/Utils/formatters';
import 'vue-multiselect/dist/vue-multiselect.min.css'

const props = defineProps({
    inquiryData: Object,
    sessionList: Object,
    source: Object,
    campusZoneSession: Object,
    guardianrelation: Object,
    classList: Object,
});

const form = useForm({
    id: props?.inquiryData.id,
    SessionId: props?.campusZoneSession?.session_year?.id,
    Date: props.inquiryData.Date,
    ClassId: props.inquiryData.ClassId,
    Name: props.inquiryData.Name,
    LastName: props.inquiryData.LastName,
    BirthDate: props.inquiryData.BirthDate,
    Gender: props.inquiryData.Gender,
    PreviousInstitute: props.inquiryData.PreviousInstitute,
    Address: props.inquiryData.Address,
    FatherName: props.inquiryData.FatherName,
    FatherPhoneNo: formatPhone(props.inquiryData.FatherPhoneNo),
    MotherName: props.inquiryData.MotherName,
    MotherPhoneNo: formatPhone(props.inquiryData.MotherPhoneNo),
    SourceId: props.inquiryData.SourceId,
    ReferenceId: props.inquiryData.ReferenceId,
    IsSmsSent: props.inquiryData.IsSmsSent,
    cnic: props.inquiryData.guardian.cnic,
    guardian_id: props.inquiryData.guardian_id,
    guardian_relation_id:props.inquiryData.guardian_relation_id
});


const gendarList = [
    { id: 1, name: 'Male' },
    { id: 2, name: 'Female' },
];


const reference = [
    { id: 1, name: 'General' },
    { id: 2, name: 'System Associate' },
    { id: 2, name: 'Principal' },
    { id: 2, name: 'Vice Principal' },
    { id: 2, name: 'Teacher' },
    { id: 2, name: 'Admission Incharge' },
    { id: 2, name: 'Admin' },
    { id: 2, name: 'School Parent' },
    { id: 2, name: 'Other' },
];

</script>

<template>

    <Head title="Student Inquiry" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Create Campus</h2>
        </template>
        <!-- Add Form Data Fileds  -->
        <form @submit.prevent="form.put(route('inquiry.update'))" >
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Student Inquiry Form</div>
                        <div class="body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="For Session" />
                                         <select class="form-control" v-model="form.SessionId">
                                            <option selected 
                                                :value="campusZoneSession?.session_year?.id">
                                                {{ campusZoneSession?.session_year?.start_date  }} - {{ campusZoneSession?.session_year?.end_date }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Inquiry Date *" />
                                        <TextInput type="date" v-model="form.Date" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.Date" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        
                                        <InputLabel value="To which class admission required *" />
                                        <select class="form-control" v-model="form.ClassId">
                                            <option selected disabled value="">Select a class</option>
                                            <option v-for="cls in classList" :key="cls.id" :value="cls.id">
                                                {{ cls.ClassName }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.ClassId" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Inquiry Information</div>
                        <div class="body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="First Name *" />
                                        <TextInput type="text" v-model="form.Name" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.Name" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Last Name" />
                                        <TextInput type="text" v-model="form.LastName" class="form-control" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Date of Birth *" />
                                        <TextInput type="date" v-model="form.BirthDate" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.BirthDate" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Gender *" />
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
                                        <InputLabel value="Previous Institute" />
                                        <TextInput type="text" v-model="form.PreviousInstitute" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.PreviousInstitute" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Address" />
                                        <TextInput type="text" v-model="form.Address" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.Address" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Father Name" />
                                        <TextInput type="text" v-model="form.FatherName" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.FatherName" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Father CNIC" />
                                        <TextInput type="number" v-model="form.cnic" class="form-control" readonly/>
                                        <InputError class="mt-2" :message="form.errors.cnic" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Relation *" />
                                        <select class="form-control" v-model="form.guardian_relation_id">
                                            <option selected disabled value="">Select a relation</option>
                                            <option v-for="relation in guardianrelation" :key="relation.id" :value="relation.id">
                                                {{ relation.relationName }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.guardian_relation_id" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Mobile No" />
                                        <TextInput type="text" v-model="form.FatherPhoneNo" class="form-control" @input="form.FatherPhoneNo = formatPhone(form.FatherPhoneNo)"/>
                                        <InputError class="mt-2" :message="form.errors.FatherPhoneNo" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Mother Name" />
                                        <TextInput type="text" v-model="form.MotherName" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.MotherName" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Whatsapp No" />
                                        <TextInput type="text" v-model="form.MotherPhoneNo" class="form-control" @input="form.MotherPhoneNo = formatPhone(form.MotherPhoneNo)"/>
                                        <InputError class="mt-2" :message="form.errors.MotherPhoneNo" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Source of Info *" />
                                        <select class="form-control" v-model="form.SourceId">
                                            <option selected disabled value="">Select a Source</option>
                                            <option v-for="src in source" :key="src.id" :value="src.id">
                                                {{ src.SourceName }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.SourceId" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Reference *" />
                                        <select class="form-control" v-model="form.ReferenceId">
                                            <option selected disabled value="">Select a reference</option>
                                            <option v-for="ref in reference" :key="ref.id" :value="ref.id">
                                                {{ ref.name }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.ReferenceId" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Send Notification"
                                            style="padding-right: 31px; padding-top: 20px;" />
                                           
                                        <input type="checkbox" v-model="form.IsSmsSent" />
                                        <InputError class="mt-2" :message="form.errors.IsSmsSent" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3 text-end">
                                        <label class="w-100 d-inline-block">&nbsp;</label>
                                        <PrimaryButton>Submit</PrimaryButton>
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
