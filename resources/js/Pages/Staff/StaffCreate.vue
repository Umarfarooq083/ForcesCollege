<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';   
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { useForm } from '@inertiajs/vue3';
import 'vue-multiselect/dist/vue-multiselect.min.css'
import Checkbox from '@/Components/Checkbox.vue';


const props = defineProps({
    data: Object,
});
const form = useForm({
        RolesId: '',
        DesignationId: '',
        DepartmentId: '',
        FirstName: '',
        LastName: '',
        FatherName: '',
        MotherName: '',
        Email: '',
        PhotoPath: '',
        Gender: '',
        DateOfBirth: '',
        DateOfJoining: '',
        Phone: '',
        EmergencyContactNumber: '',
        MaritalStatus: '',
        CurrentAddress: '',
        PermanentAddress: '',
        Qualification: '',
        WorkExperience: '',
        Note: '',
        BasicSalary: '',
        TransportAllowance: '',
        ComputerAllowance: '',
        MobileAllowance: '',
        RecreationAllowance: '',
        HasProvidentFund: false,
        ProvidentFundAmount: '',
        HasEOBI: false,
        EOBIAmount: '',
        employerAmount: '',
        CreateUser: false,
});

const GenderList = [
    { id: 'Male', name: 'Male' },
    { id: 'Female', name: 'Female' },
];

const MeritalStatusList = [
    { id: 'Single', name: 'Single' },
    { id: 'Enggaged', name: 'Enggaged' },
    { id: 'Married', name: 'Married' },
    { id: 'Divorced', name: 'Divorced' },
    { id: 'Widow', name: 'Widow' },
    { id: 'Unknown', name: 'Unknown' },
];

</script>

<template>

    <Head title="Create Staff" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Create Staff</h2>
        </template>

        <form @submit.prevent="form.post(route('staff.submit'))">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Create Staff</div>
                        <div class="body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <InputLabel value="Role *" />
                                         <select class="form-control" v-model="form.RolesId">
                                            <option selected disabled value="">Select a Type</option>
                                            <option v-for="roleList in data.rolesList" :key="roleList.id" :value="roleList.id">
                                                {{ roleList.name }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.RolesId" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <InputLabel value="Designation *" />
                                         <select class="form-control" v-model="form.DesignationId">
                                            <option selected disabled value="">Select a Type</option>
                                            <option v-for="designationListData in data.desiginationList" :key="designationListData.id" :value="designationListData.id">
                                                {{ designationListData.DesignationName }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.DesignationId" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <InputLabel value="Department *" />
                                        <select class="form-control" v-model="form.DepartmentId">
                                            <option selected disabled value="">Select a Type</option>
                                            <option v-for="depList in data.departmentList" :key="depList.id" :value="depList.id">
                                                {{ depList.DepartmentName }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.DepartmentId" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <InputLabel value="First Name *" />
                                        <TextInput type="text" v-model="form.FirstName" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.FirstName" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <InputLabel value="Last Name  *" />
                                        <TextInput type="text" v-model="form.LastName" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.LastName" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <InputLabel value="Father Name *" />
                                        <TextInput type="text" v-model="form.FatherName" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.FatherName" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <InputLabel value="Mother Name *" />
                                        <TextInput type="text" v-model="form.MotherName" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.MotherName" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <InputLabel value="Email *" />
                                        <TextInput type="email" v-model="form.Email" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.Email" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <InputLabel value="Gender *" />
                                        <select class="form-control" v-model="form.Gender">
                                            <option selected disabled value="">Select a Type</option>
                                            <option v-for="genList in GenderList" :key="genList.id" :value="genList.id">
                                                {{ genList.name }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.Gender" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <InputLabel value="Date Of Birth  *" />
                                        <TextInput type="date" v-model="form.DateOfBirth" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.DateOfBirth" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <InputLabel value="Date Of Joining *" />
                                        <TextInput type="date" v-model="form.DateOfJoining" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.DateOfJoining" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <InputLabel value="Phone *" />
                                        <TextInput type="text" inputmode="numeric" pattern="[0-9]*" v-model="form.Phone" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.Phone" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <InputLabel value="Emergency Contact Number *" />
                                        <TextInput type="text" inputmode="numeric" pattern="[0-9]*"  v-model="form.EmergencyContactNumber" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.EmergencyContactNumber" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <InputLabel value="Marital Status *" />
                                        <select class="form-control" v-model="form.MaritalStatus">
                                            <option selected disabled value="">Select a Type</option>
                                            <option v-for="meritList in MeritalStatusList" :key="meritList.id" :value="meritList.id">
                                                {{ meritList.name }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.MaritalStatus" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <InputLabel value="Photo Path" />
                                         <TextInput type="file"
                                            @input="form.PhotoPath = $event.target.files[0]" class="form-control" accept="image/*" />
                                         <InputError class="mt-2" :message="form.errors.PhotoPath" />   
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <InputLabel value="Current Address  *" />
                                        <TextInput type="text" v-model="form.CurrentAddress" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.CurrentAddress" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <InputLabel value="Permanent Address *" />
                                        <TextInput type="text" v-model="form.PermanentAddress" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.PermanentAddress" />
                                    </div>
                                </div>
<div class="col-md-3">
                                     <div class="mb-3">
                                         <InputLabel value="Qualification *" />
                                         <TextInput type="text" v-model="form.Qualification" class="form-control" />
                                         <InputError class="mt-2" :message="form.errors.Qualification" />
                                     </div>
                                 </div>

                                 <div class="col-md-3">
                                     <div class="mb-3">
                                         <InputLabel value="Work Experience *" />
                                         <TextInput type="text" v-model="form.WorkExperience" class="form-control" />
                                         <InputError class="mt-2" :message="form.errors.WorkExperience" />
                                     </div>
                                 </div>

                                 <div class="col-md-3">
                                     <div class="mb-3">
                                         <InputLabel value="Note" />
                                         <TextInput type="text" v-model="form.Note" class="form-control" />
                                         <InputError class="mt-2" :message="form.errors.Note" />
                                     </div>
                                 </div>

                                 <div class="col-md-3">
                                     <div class="mb-3">
                                         <InputLabel value="Basic Salary *" />
                                         <TextInput type="number" v-model="form.BasicSalary" class="form-control" />
                                         <InputError class="mt-2" :message="form.errors.BasicSalary" />
                                     </div>
                                 </div>

                                 <div class="col-md-3">
                                     <div class="mb-3">
                                         <InputLabel value="Transport Allowance" />
                                         <TextInput type="number" v-model="form.TransportAllowance" class="form-control" />
                                         <InputError class="mt-2" :message="form.errors.TransportAllowance" />
                                     </div>
                                 </div>

                                 <div class="col-md-3">
                                     <div class="mb-3">
                                         <InputLabel value="Computer Allowance" />
                                         <TextInput type="number" v-model="form.ComputerAllowance" class="form-control" />
                                         <InputError class="mt-2" :message="form.errors.ComputerAllowance" />
                                     </div>
                                 </div>

                                 <div class="col-md-3">
                                     <div class="mb-3">
                                         <InputLabel value="Mobile Allowance" />
                                         <TextInput type="number" v-model="form.MobileAllowance" class="form-control" />
                                         <InputError class="mt-2" :message="form.errors.MobileAllowance" />
                                     </div>
                                 </div>

                                 <div class="col-md-3 mt-3">
                                     <div class="mb-3">
                                         <InputLabel value="Recreation Allowance" />
                                         <TextInput type="number" v-model="form.RecreationAllowance" class="form-control" />
                                         <InputError class="mt-2" :message="form.errors.RecreationAllowance" />
                                     </div>
                                 </div>

                                 <div class="col-md-3 mt-5">
                                     <div class="mb-3">
                                         <label for="HasProvidentFund" class="fancy-checkbox element-left">
                                         <Checkbox name="HasProvidentFund" id="HasProvidentFund" v-model:checked="form.HasProvidentFund" />
                                        <span class="text-dark font-weight-bold">Provident Fund</span>
                                        </label>
                                         <InputError class="mt-2" :message="form.errors.HasProvidentFund" />
                                     </div>
                                 </div>
                                
                                 
                                 <div class="col-md-3 mt-5">
                                     <div class="mb-3">
                                         <label for="HasEOBI" class="fancy-checkbox element-left">
                                             <Checkbox name="HasEOBI" id="HasEOBI" v-model:checked="form.HasEOBI" />
                                             <span class="text-dark font-weight-bold">EOBI</span>
                                         </label>
                                         <InputError class="mt-2" :message="form.errors.HasEOBI" />
                                     </div>
                                 </div>

                                 <div class="col-md-3 mt-3" v-if="form.HasProvidentFund">
                                     <div class="mb-3">
                                         <InputLabel value="Provident Fund In (%)" />
                                          <TextInput
                                                type="number"
                                                v-model="form.ProvidentFundAmount"
                                                class="form-control"
                                                min="0"
                                                max="100"
                                                step="0.01"
                                                :required="form.HasProvidentFund"
                                            />
                                         <InputError class="mt-2" :message="form.errors.ProvidentFundAmount" />
                                     </div>
                                 </div>

                                 <div class="col-md-3 mt-3" v-if="form.HasEOBI">
                                     <div class="mb-3">
                                         <InputLabel value="IP Amount" />
                                         <TextInput type="number" v-model="form.EOBIAmount" class="form-control" required />
                                         <InputError class="mt-2" :message="form.errors.EOBIAmount" />
                                     </div>
                                 </div>
                                 
                                 <div class="col-md-3 mt-3" v-if="form.HasEOBI">
                                     <div class="mb-3">
                                         <InputLabel value="Employer Amount" />
                                         <TextInput type="number" v-model="form.employerAmount" class="form-control" required />
                                         <InputError class="mt-2" :message="form.errors.employerAmount" />
                                     </div>
                                 </div>

                                <div class="col-md-12">
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
