<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import Checkbox from '@/Components/Checkbox.vue'
import { useForm } from '@inertiajs/vue3';
import 'vue-multiselect/dist/vue-multiselect.min.css'


const props = defineProps({
    feesType: Object,
});

const Feecycle  = [
    { id: 'Monthly', name: 'Monthly' },
    { id: 'Annually', name: 'Annually' },
    { id: 'Once', name: 'Once' },
];
const ApplicableMonth  = [
    { id : 'January' , name : 'January' }, 
    { id : 'February' , name : 'February' }, 
    { id : 'March' , name : 'March' }, 
    { id : 'April' , name : 'April' }, 
    { id : 'May' , name : 'May' }, 
    { id : 'June' , name : 'June' }, 
    { id : 'July' , name : 'July' }, 
    { id : 'August' , name : 'August' }, 
    { id : 'September' , name : 'September' }, 
    { id : 'October' , name : 'October' }, 
    { id : 'November' , name : 'November' }, 
    { id : 'December' , name : 'December' }, 
];

const form = useForm({
    id: props.feesType.id,
    FeesCode: props.feesType.FeesCode,
    FeeName: props.feesType.FeeName,
    Description: props.feesType.Description,
    FeeCycle: props.feesType.FeeCycle,
    ApplicableMonth: props.feesType.ApplicableMonth,
    Isroyality: props.feesType.Isroyality,
    IsRefundable: props.feesType.IsRefundable,
    IsOptional: props.feesType.IsOptional,
});

</script>

<template>

    <Head title="Update Fee Type" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Update Fee Type</h2>
        </template>
        
        <form @submit.prevent="form.post(route('fees.submit'))">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Update Fee Type</div>
                        <div class="body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <InputLabel value="Fees Code " /> <span class="text-danger font-12 position-absolute"> ★</span>
                                        <TextInput type="text" v-model="form.FeesCode" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.FeesCode" />
                                    </div>
                                </div>
                               
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <InputLabel value="Fee Name" /> <span class="text-danger font-12 position-absolute">★</span>
                                        <TextInput type="text" v-model="form.FeeName" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.FeeName" />
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <InputLabel value="Fee Cycle" /> <span class="text-danger font-12 position-absolute">★</span>
                                        <select class="form-control" v-model="form.FeeCycle" @change="SelectFeeCycle($event.target.value)">
                                            <option selected disabled value="">Select a Fee Cycle</option>
                                            <option v-for="feecycleList in Feecycle" :key="feecycleList.id" :value="feecycleList.id">
                                                {{ feecycleList.name }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.FeeCycle" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <InputLabel value="Applicable Month" /> <span class="text-danger font-12 position-absolute">★</span>
                                        <select class="form-control" v-model="form.ApplicableMonth">
                                            <option selected disabled value="">Select a Applicable Month</option>
                                            <option v-for="type in ApplicableMonth" :key="type.id" :value="type.id">
                                                {{ type.name }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.ApplicableMonth" />
                                    </div>
                                </div>
                             
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <InputLabel value="Description" /> 
                                        <TextInput type="text" v-model="form.Description" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.Description" />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                </div>

                                <div class="col-md-3" style="margin-top: 32px;" >
                                    <div class="mb-3">
                                         <label class="fancy-checkbox element-left">	
                                            <Checkbox v-model:checked="form.Isroyality"  />
                                            <span class="text-dark font-weight-bold">Is Royality</span>
                                        </label>
                                    </div>
                                </div>

                                 <div class="col-md-3" style="margin-top: 32px;" v-if="form.FeeCycle !== 'Monthly'">
                                    <div class="mb-3">
                                         <label class="fancy-checkbox element-left">	
                                            <Checkbox  v-model:checked="form.IsRefundable" />
                                            <span class="text-dark font-weight-bold">Is Refundable</span>
                                        </label>
                                    </div>
                                </div> 

                                 <div class="col-md-3" style="margin-top: 32px;" v-if="form.FeeCycle !== 'Monthly' && form.FeeCycle !== 'Once' ">
                                    <div class="mb-3">
                                         <label class="fancy-checkbox element-left">	
                                            <Checkbox  v-model:checked="form.IsOptional" />
                                            <span class="text-dark font-weight-bold">Is Optional</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="text-end">
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
