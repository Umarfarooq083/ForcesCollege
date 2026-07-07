<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { ref } from 'vue';

const props = defineProps({
    student: Object,
    lastChallan: Object,
    disable_reasons: Array,
});

const fromDate = ref('');



const reason = ref({ id: '', name: '' });
const errors = ref({});

const validateForm = () => {
    const validationErrors = {};
    if (!reason.value.id) {
        validationErrors.reason = 'Reason is required.';
    }
    if (!fromDate.value) {
        validationErrors.fromDate = 'From Date is required.';
    }
    errors.value = validationErrors;
    return Object.keys(validationErrors).length === 0;
};

const submitWithdraw = () => {
    if (!validateForm()) return;

    router.post(route('student.withdrawsubmit', props.student.id), {
        ReasonId: reason.value.id,
        Reason: reason.value,
        FromDate: fromDate.value,
        last_challan_status: props.lastChallan?.status,
        pending_amount: props.lastChallan?.pending_amount,
        challan_no: props.lastChallan?.challan_no,
        Status: 'Inprocess',
    }, {
        preserveScroll: true,
    });
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(value || 0);
};
</script>

<template>

    <Head title="Student Withdraw" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Student Withdraw
            </h2>
        </template>
        
        <div class="card">
            <div class="header">Withdraw Student</div>
            <div class="body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <InputLabel value="Student Name" />
                            <input type="text" :value="`${student.FirstName} ${student.LastName}`" class="form-control"
                                disabled />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <InputLabel value="Roll Number" />
                            <input type="text" :value="`${student.RollNumber}`" class="form-control" disabled />
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <InputLabel value="Class" />
                            <input type="text" :value="`${student.class.ClassName}`" class="form-control" disabled />
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <InputLabel value="Section" />
                            <input type="text" :value="`${student.section.SectionName}`" class="form-control" disabled />
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <InputLabel value="Address" />
                            <input type="text" :value="`${student?.CurrentAddress || 'N/A'}`" class="form-control" disabled />
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <InputLabel value="Father Name" />
                            <input type="text" :value="`${student.FatherName}`" class="form-control" disabled />
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <InputLabel value="Mobile No" />
                            <input type="text" :value="`${student.FatherPhone}`" class="form-control" disabled />
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <InputLabel value="Admission Date" />
                            <input type="text" :value="`${student.AdmissionDate}`" class="form-control" disabled />
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <InputLabel value="Last Fee Challan Number" />
                            <input type="text" :value="lastChallan?.challan_no || 'N/A'" class="form-control" disabled />
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <InputLabel value="Last Amount of Challan" />
                            <input type="text" :value="formatCurrency(lastChallan?.pending_amount || 0)" class="form-control" disabled />
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <InputLabel value="Last Challan Status" />
                            <input type="text" :value="`${lastChallan?.status}`" class="form-control" disabled />
                        </div>
                    </div>

                    <!-- <div class="col-md-3" v-if="lastChallan?.total_arrears_amount > 0">
                        <div class="form-group">
                            <InputLabel value="Total Arrears Amount" />
                            <input type="text" :value="formatCurrency(lastChallan?.total_arrears_amount)" class="form-control" disabled />
                        </div>
                    </div> -->

                    <!-- <div class="col-md-3" v-if="lastChallan?.total_fine_amount > 0">
                        <div class="form-group">
                            <InputLabel value="Total Fine Amount" />
                            <input type="text" :value="formatCurrency(lastChallan?.total_fine_amount)" class="form-control" disabled />
                        </div>
                    </div> -->

                    <div class="col-md-3" v-if="lastChallan?.waived_amount > 0">
                        <div class="form-group">
                            <InputLabel value="Waived Amount" />
                            <input type="text" :value="formatCurrency(lastChallan?.waived_amount)" class="form-control" disabled />
                        </div>
                    </div>

                    <!-- <div class="col-md-12" v-if="lastChallan?.arrears?.length > 0">
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered table-sm">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Challan No</th>
                                        <th>Month</th>
                                        <th>Arrears Amount</th>
                                        <th>Fine</th>
                                        <th>Waived</th>
                                        <th>Is Partial Payment</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(arrear, index) in lastChallan?.arrears" :key="index">
                                        <td>{{ arrear?.challan_no || 'N/A' }}</td>
                                        <td>{{ arrear?.ChallanMonth || 'N/A' }}</td>
                                        <td>{{ formatCurrency(arrear?.total_amount) }}</td>
                                        <td>{{ formatCurrency(arrear?.total_fine) }}</td>
                                        <td>{{ formatCurrency(arrear?.waived) }}</td>
                                        <td>
                                            <span v-if="arrear?.is_fine" class="badge badge-info">Fine</span>
                                            <span v-else-if="arrear?.is_partial_payment" class="badge badge-warning">Yes</span>
                                            <span v-else class="badge badge-success">No</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div> -->

                    <div class="col-md-3 form-group">
                        <label>Withdrawal Reason<span class="text-danger font-12 position-absolute">★</span></label>
                        <select class="form-control" v-model="reason">
                            <option disabled value="">Select reason</option>
                            <option v-for="r in disable_reasons" :key="r.id"
                                :value="{ id: r.id, name: r.DisableReasonName }">
                                {{ r.DisableReasonName }}
                            </option>
                        </select>
                        <small v-if="errors.reason" class="text-danger">{{ errors.reason }}</small>
                    </div>

                    <div class="col-md-3 form-group">
                        <label>Withdrawal Date<span class="text-danger font-12 position-absolute">★</span></label>
                        <TextInput type="date" v-model="fromDate" class="form-control" />
                        <InputError class="mt-2" :message="errors.fromDate" />
                    </div>

                </div>

                <div class="mt-4">
                    <Link style="margin-right: 6px !important" :href="route('student.index')" class="btn btn-secondary">Cancel</Link>
                    <PrimaryButton @click="submitWithdraw">Withdraw Student</PrimaryButton>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>