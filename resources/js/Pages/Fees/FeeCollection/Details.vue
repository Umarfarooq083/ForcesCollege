<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import TableHeader from '@/Components/TableHeader.vue';
import 'vue-multiselect/dist/vue-multiselect.min.css';

const props = defineProps({
    GenerateFeeChallan: Object,
    PartialChallanList: Array,
    TotalReceivedAmount: Number,
    FineLateFee: Number,
    latestChallan: String,
    dueDate: String,
});

const form = useForm({
    name: props.GenerateFeeChallan?.student?.FirstName + ' ' + props.GenerateFeeChallan?.student?.LastName || '',
    due_date: props?.dueDate || '',
    balance_after_discount: props.GenerateFeeChallan?.transection_sum_balancefeeafterdiscount || 0,
    ChallanMonth: props.GenerateFeeChallan?.ChallanMonth || '',
    ChallanNo: props.GenerateFeeChallan?.challan_no || '',
});

const columns = [
    { label: 'Challan No' },
    { label: 'Class' },
    { label: 'Section' },
    { label: 'Submit Date' },
    { label: 'Received' },
    { label: 'Payment Mode' },
];

</script>

<template>
    <Head title="Fee Challan Collection" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Fee Challan</h2>
        </template>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="header">Fee Collection </div>
                    <div class="body">
                        <div class="row">
                            <!-- Challan No -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <InputLabel value="Challan Number" /> <span class="text-danger">★</span>
                                    <TextInput type="text" v-model="form.ChallanNo" class="form-control" disabled />
                                    <InputError class="mt-2" :message="form.errors.ChallanNo" />
                                </div>
                            </div>

                            <!-- Student Name -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <InputLabel value="Student Name" /> <span class="text-danger">★</span>
                                    <TextInput type="text" v-model="form.name" class="form-control" disabled />
                                    <InputError class="mt-2" :message="form.errors.name" />
                                </div>
                            </div>

                            <!-- Due Date -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <InputLabel value="Due Date" /> <span class="text-danger">★</span>
                                    <TextInput type="date" v-model="form.due_date" class="form-control" disabled />
                                    <InputError class="mt-2" :message="form.errors.due_date" />
                                </div>
                            </div>

                            <!-- Challan Month -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <InputLabel value="Challan Month" /> <span class="text-danger">★</span>
                                    <TextInput type="text" v-model="form.ChallanMonth" class="form-control" disabled />
                                    <InputError class="mt-2" :message="form.errors.ChallanMonth" />
                                </div>
                            </div>

                            <!-- Amount -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <InputLabel value="Amount" /> <span class="text-danger">★</span>
                                    <TextInput type="number" v-model="form.balance_after_discount" class="form-control" disabled />
                                    <InputError class="mt-2" :message="form.errors.balance_after_discount" />
                                </div>
                            </div>

                            <!-- Already Paid -->
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <InputLabel value="Already Paid Amount" />
                                    <TextInput 
                                        type="number" 
                                        v-model="props.TotalReceivedAmount" 
                                        class="form-control" 
                                        disabled 
                                    />
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Challan List -->
        <div class="card" v-if="props.PartialChallanList.length > 0">
            <div class="header">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="fw-bold">Challan Installments</div>
                </div>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table mb-0 text-center table-hover table-bordered">
                        <TableHeader :columns="columns" />
                        <tbody>
                            <tr v-for="(challan, index) in props.PartialChallanList" :key="index">
                                <td>{{ challan?.fee_challan?.challan_no }}</td>
                                <td>{{ challan?.fee_challan?.student?.class?.ClassName }}</td>
                                <td>{{ challan?.fee_challan?.student?.section?.SectionName }}</td>
                                <td>{{ challan?.SubmitDate }}</td>
                                <td>{{ challan?.ReceivedAmount }}</td>
                                <td>{{ challan?.PaymentMode }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>