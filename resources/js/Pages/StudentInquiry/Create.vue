<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { useForm } from '@inertiajs/vue3';
import 'vue-multiselect/dist/vue-multiselect.min.css'
import { formatCNIC, formatPhone } from '@/Utils/formatters';


const props = defineProps({
    source: Object,
    guardianrelation: Object,
    campusZoneSession: Object,
    classList: Object,
});


const form = useForm({
    SessionId: props?.campusZoneSession?.session_year?.id ?? '',
    Date: '',
    ClassId: '',
    Name: '',
    LastName: '',
    BirthDate: '',
    Gender: '',
    PreviousInstitute: '',
    Address: '',
    FatherName: '',
    FatherPhoneNo: '+92',
    MotherName: '',
    MotherPhoneNo: '+92',
    SourceId: '',
    ReferenceId: '',
    IsSmsSent: false,
    cnic: '',
    guardian_id: '',
    guardian_relation_id: '',
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
const isFormVisible = ref(false);
const guardianInfo = ref(null);
const studentInquiry = ref([]);
const inquery_message = ref('');

function showForm() {
    isFormVisible.value = true; 
}

const sendCnicRequest = async () => {
    try {
        const response = await axios.post('check-guardian', {
            cnic: form.cnic
        })
        guardianInfo.value = response.data.guardianInfo
        if(response.data.guardianInfo == null){
            inquery_message.value = 'No record found'
        }
        form.guardian_id = response?.data?.guardianInfo?.id ?? null
        studentInquiry.value = response.data.studentInquiry    
    } catch (error) {
        console.error('Request failed:', error)
    }
}

</script>

<template>

    <Head title="Student Inquiry" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Create Campus</h2>
        </template>

        <!-- Buttons For Add or Edit  -->
        <div class="row guardian_class" :style="{ display: isFormVisible ? 'none' : 'block' }">
            <div class="col-md-12">
                <div class="card">
                    <div class="header">Guardian CNIC</div>
                    <div class="body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <InputLabel value="Enter CNIC" />
                                    <input type="text" v-model="form.cnic" class="form-control" @input="form.cnic = formatCNIC(form.cnic)" placeholder="11111-1111111-1"/>
                                    <InputError class="mt-2" :message="form.errors.cnic" />
                                    <InputError class="mt-2" :message="inquery_message" />
                                </div>
                            </div>

                            <div class="col-md-2"  v-if="form.cnic && form.cnic.length >= 15 " style="margin-top: 29px;">
                                <button class="btn btn-success" @click.prevent="sendCnicRequest">Fetch Inquiry</button>
                            </div>
                            <!-- v-if="studentInquiry.length > 0" -->
                            <div class="col-md-2" style="margin-top: 29px;"  v-if="form.cnic && form.cnic.length >= 15 ">
                                <button class="btn btn-success" @click="showForm">Add New</button>
                            </div>
                            <!-- <div class="col-md-2" style="margin-top: 29px;" v-if="inquery_message && studentInquiry.length === 0">
                                <button class="btn btn-success" @click="showForm">Add New</button>
                            </div> -->

                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Edit Card Data  -->
        <div class="row" v-if="studentInquiry.length > 0">
            <div class="col-md-6" v-for="studentInquiry in studentInquiry">
                <div class="border-0 shadow card rounded-4">
                    <div class="card-header bg-light border-bottom fw-semibold fs-5">
                        <i class="bi bi-person-vcard me-2 text-primary"></i>
                        <Link v-if="studentInquiry.Status == 0 || studentInquiry.Status == 2 && $page.props.auth.user.user_permissions.indexOf('inquiry.edit') > -1" type="button"
                            class="btn btn-info btn-sm" title="Edit" :href="route('inquiry.edit', { id: studentInquiry.id })" method="get" ><i class="fa fa-edit"></i></Link>
                        {{ studentInquiry.Name }}
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-6">
                                <small class="text-muted">Class</small>
                                <div class="fw-bold text-dark">{{ studentInquiry.Name }}</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Father CNIC</small>
                                <div class="fw-bold text-dark">{{ guardianInfo.cnic }}</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Status</small>
                                <div>
                                    <span class="badge">
                                        <span class="badge bg-primary text-light font-weight-bold" v-if="studentInquiry.Status == 0">
                                            Generated
                                        </span>
                                        <span class="badge bg-success text-light font-weight-bold" v-if="studentInquiry.Status == 1">
                                            Won
                                        </span>
                                        <span class="badge bg-danger text-light font-weight-bold" v-if="studentInquiry.Status == 2">
                                            Lose
                                        </span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Creation Date</small>
                                <div class="fw-bold text-dark">{{ studentInquiry.Date }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add Form Data Fileds  -->
        <form @submit.prevent="form.post(route('inquiry.submit'))" class="form_show"
            :style="{ display: isFormVisible ? 'block' : 'none' }">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Student Inquiry Form <h5 v-if="!campusZoneSession.session_year" style="color: red;">
                                        No Session is created or active
                                   </h5></div>
                        
                        <div class="body">
                            <div class="row">
                                <div class="col-md-6">
                                   
                                    <div class="mb-3">
                                        <InputLabel value="For Session" />
                                        <select class="form-control" v-model="form.SessionId">
                                            <option  
                                                :value="campusZoneSession?.session_year?.id">
                                                {{ campusZoneSession?.session_year?.start_date  }} - {{ campusZoneSession?.session_year?.end_date }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.SessionId" />
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
                                        <InputLabel value="Father Name *" />
                                        <TextInput type="text" v-model="form.FatherName" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.FatherName" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Father CNIC" />
                                        <TextInput type="text" v-model="form.cnic" class="form-control" @input="form.cnic = formatCNIC(form.cnic)" readonly />
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
                                        <TextInput  type="text"  v-model="form.FatherPhoneNo" class="form-control" @input="form.FatherPhoneNo = formatPhone(form.FatherPhoneNo)"/>
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
                                        <TextInput  type="text" v-model="form.MotherPhoneNo" class="form-control" @input="form.MotherPhoneNo = formatPhone(form.MotherPhoneNo)" />
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
