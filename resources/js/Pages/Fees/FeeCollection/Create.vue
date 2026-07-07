<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TableHeader from '@/Components/TableHeader.vue';
import { computed, watch, ref } from 'vue';
import 'vue-multiselect/dist/vue-multiselect.min.css';


const isDeleting = ref(false)

const props = defineProps({
    GenerateFeeChallan: Object,
    PartialChallanList: Array,
    TotalReceivedAmount: Number,
    FineLateFee: Number,
    latestChallan: String,
    dueDate: String,
    TotalChallanAmount: Number
});

// Initialize form with proper TotalReceivedAmount handling
const form = useForm({
    name: props.GenerateFeeChallan?.student?.FirstName,
    due_date: props?.dueDate || '',
    // balance_after_discount: props.GenerateFeeChallan?.transection_sum_balancefeeafterdiscount || 0,
    balance_after_discount: props.TotalChallanAmount || 0,
    waived_amount: props.GenerateFeeChallan?.WaivedFineAmount || 0,
    ReceivedAmount: 0,
    ChallanMonth: props.GenerateFeeChallan?.ChallanMonth || '',
    PaymentMode: 'cash',
    SubmitDate: props?.dueDate || '',
    note: '',
    fine_amount: 0,
    ChallanNo: props.GenerateFeeChallan?.challan_no || '',
    GenerateClassChallanId: props.GenerateFeeChallan?.id || '',
    // Add these for proper display
    already_paid: props.TotalReceivedAmount || 0,
    current_balance: 0,
    ExpiryDate: props.GenerateFeeChallan?.ExpiryDate || '',
});

//checking expiry date
// const isExpired = computed(() => {
//   if (!props.GenerateFeeChallan?.ExpiryDate) return false
//   const today = new Date().toISOString().split('T')[0] // yyyy-mm-dd
//   return props.GenerateFeeChallan.ExpiryDate < today
// })


const isExpired = computed(() => {
    if (!props.GenerateFeeChallan?.ExpiryDate) return false;

    const today = new Date();
    today.setHours(0,0,0,0);

    const expiry = new Date(props.GenerateFeeChallan.ExpiryDate);
    expiry.setHours(0,0,0,0);

    return today > expiry;
});

// Initialize current_balance
form.current_balance = form.balance_after_discount - form.already_paid;

// min-max for inputs
const min = 0;
const step = 0.01;

// -------------------- Fine Calculation --------------------
const calculateFine = () => {
    const dueDate = new Date(form.due_date);
    const submitDate = new Date(form.SubmitDate);

    if (form.SubmitDate && submitDate > dueDate) {
        const daysLate = Math.ceil((submitDate - dueDate) / (1000 * 3600 * 24));
        return daysLate * props.FineLateFee;
    }
    return 0;
};

watch(() => form.SubmitDate, () => {
    form.fine_amount = calculateFine();
});

// -------------------- Balance & Waived Amount Logic --------------------

// Maximum waived amount calculation
const maxWaivedAmount = computed(() => {
    return Math.max(0, form.balance_after_discount - (props.TotalReceivedAmount || 0));
});

// Balance amount calculation
const balanceAmount = computed(() => {
    const balance = form.balance_after_discount - (props.TotalReceivedAmount || 0) - form.waived_amount;
    form.current_balance = Math.max(0, balance); // Update form field
    return Math.max(0, balance); // Ensure balance never goes negative
});

// Watcher for waived amount with proper validation
watch(() => form.waived_amount, (newWaivedAmount) => {
    // Prevent waived amount from exceeding the maximum allowed
    if (newWaivedAmount > maxWaivedAmount.value) {
        form.waived_amount = maxWaivedAmount.value;
        console.log('Waived amount capped at maximum:', maxWaivedAmount.value);
        return;
    }

    // Prevent negative waived amount
    if (newWaivedAmount < 0) {
        form.waived_amount = 0;
        console.log('Waived amount set to 0 (minimum)');
        return;
    }

    // Auto-adjust received amount based on remaining balance
    const remainingBalance = form.balance_after_discount - (props.TotalReceivedAmount || 0) - form.waived_amount;
    form.ReceivedAmount = Math.max(0, remainingBalance);
});

// Watcher for received amount to ensure it doesn't exceed balance
watch(() => form.ReceivedAmount, (newReceivedAmount) => {
    const maxReceivable = balanceAmount.value;
    
    if (newReceivedAmount > maxReceivable) {
        form.ReceivedAmount = maxReceivable;
    }
    
    if (newReceivedAmount < 0) {
        form.ReceivedAmount = 0;
    }
});

const setWaivedToBalance = () => {
    form.waived_amount = maxWaivedAmount.value;
    form.ReceivedAmount = 0;
};

const resetWaivedAmount = () => {
    form.waived_amount = 0;
    form.ReceivedAmount = maxWaivedAmount.value;
};

// -------------------- Current Month --------------------
const today = new Date();
const year = today.getFullYear();
const month = today.getMonth();
const currentMonthStart = `${year}-${String(month + 1).padStart(2, '0')}-01`;
const lastDay = new Date(year, month + 1, 0).getDate();
const currentMonthEnd = `${year}-${String(month + 1).padStart(2, '0')}-${lastDay}`;

// -------------------- Table Headings --------------------
const columns = [
    { label: 'Challan No' },
    { label: 'Student' },
    { label: 'Class' },
    { label: 'Section' },
    { label: 'Challan Month' },
    { label: 'Submit Date' },
    { label: 'Received' },
    { label: 'Payment Mode' },
    { label: 'Actions' },
];

// -------------------- Delete Modal --------------------
const challanToDelete = ref(null);

function openDeleteModal(challanItem) {
    challanToDelete.value = challanItem;
    $('#deleteModal').modal('show');
}

function confirmDelete() {
    if (challanToDelete.value) {
        isDeleting.value = true;


        router.delete(route('fee.challan.delete', challanToDelete.value.id), {
            onFinish: () => {
                isDeleting.value = false;
                $('#deleteModal').modal('hide');
                challanToDelete.value = null;
                router.reload();
            },
        });
    }
}
</script>

<template>
    <Head title="Fee Challan Collection" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Fee Challan Collection</h2>
        </template>

        <form @submit.prevent="form.post(route('fee.collection.submit'))">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Fee Challan Collection</div>
                        <div class="body">
                            <div class="row">
                                <!-- Challan No -->
                                <div class="col-md-4">
                                    <div class="mb-1">
                                        <InputLabel value="Challan Number" /> <span class="text-danger">★</span>
                                        <TextInput type="text" v-model="form.ChallanNo" class="form-control" disabled />
                                        <InputError class="mt-2" :message="form.errors.ChallanNo" />
                                    </div>
                                </div>

                                <!-- Student Name -->
                                <div class="col-md-4">
                                    <div class="mb-1">
                                        <InputLabel value="Student Name" /> <span class="text-danger">★</span>
                                        <TextInput type="text" v-model="form.name" class="form-control" disabled />
                                        <InputError class="mt-2" :message="form.errors.name" />
                                    </div>
                                </div>

                                <!-- Due Date -->
                                <div class="col-md-4">
                                    <div class="mb-1">
                                        <InputLabel value="Due Date" /> <span class="text-danger">★</span>
                                        <TextInput type="date" v-model="form.due_date" class="form-control" disabled />
                                        <InputError class="mt-2" :message="form.errors.due_date" />
                                    </div>
                                </div>

                                <!-- Challan Month -->
                                <div class="col-md-4">
                                    <div class="mb-1">
                                        <InputLabel value="Challan Month" /> <span class="text-danger">★</span>
                                        <TextInput type="text" v-model="form.ChallanMonth" class="form-control" disabled />
                                        <InputError class="mt-2" :message="form.errors.ChallanMonth" />
                                    </div>
                                </div>

                                <!-- Amount -->
                                <div class="col-md-4">
                                    <div class="mb-1">
                                        <InputLabel value="Amount" /> <span class="text-danger">★</span>
                                        <TextInput type="number" v-model="form.balance_after_discount" class="form-control" disabled />
                                        <InputError class="mt-2" :message="form.errors.balance_after_discount" />
                                    </div>
                                </div>

                                <!-- Already Paid -->
                                <div class="col-md-4" v-if="props.TotalReceivedAmount">
                                    <div class="mb-1">
                                        <InputLabel value="Already Paid Amount" />
                                        <TextInput 
                                            type="number" 
                                            v-model="form.already_paid" 
                                            class="form-control" 
                                            disabled 
                                        />
                                    </div>
                                </div>

                                <!-- Balance Amount with helper info -->
                                <div class="col-md-4">
                                    <div class="mb-1">
                                        <InputLabel value="Balance Amount" /> <span class="text-danger">★</span> <small class="text-muted"> Remaining: {{ balanceAmount }} </small>
                                        <TextInput 
                                            type="number" 
                                            v-model="form.current_balance" 
                                            class="form-control" 
                                            disabled 
                                        />
                                        
                                    </div>
                                </div>

                                <!-- Waived Amount with quick action buttons -->
                                <div class="col-md-4">
                                    <div class="mb-1">
                                        <InputLabel value="Waived Amount" /> <span class="text-danger">★</span>
                                        <div class="input-group">
                                            <TextInput
                                                type="number"
                                                v-model="form.waived_amount"
                                                class="form-control"
                                                :min="0"
                                                :max="maxWaivedAmount"
                                                :step="1"
                                            />
                                            <div class="input-group-append">
                                                <button 
                                                    type="button" 
                                                    class="btn btn-sm btn-outline-primary"
                                                    @click="setWaivedToBalance"
                                                    :disabled="form.waived_amount >= maxWaivedAmount"
                                                    title="Set waived amount to make balance 0"
                                                >
                                                    Max
                                                </button>
                                                <button 
                                                    type="button" 
                                                    class="btn btn-sm btn-outline-secondary"
                                                    @click="resetWaivedAmount"
                                                    :disabled="form.waived_amount <= 0"
                                                    title="Reset waived amount to 0"
                                                >
                                                    Reset
                                                </button>
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            Max waivable: {{ maxWaivedAmount }}
                                        </small>
                                        <InputError class="mt-2" :message="form.errors.waived_amount" />
                                    </div>
                                </div>

                                <!-- Received Amount -->
                                <div class="col-md-4">
                                    <div class="mb-1">
                                        <InputLabel value="Received Amount" /> <span class="text-danger">★</span>
                                        <TextInput
                                            type="number"
                                            v-model="form.ReceivedAmount"
                                            class="form-control"
                                            :min="0"
                                            :max="balanceAmount"
                                            :step="1"
                                        />
                                        <small class="text-muted">
                                            Max receivable: {{ balanceAmount }}
                                        </small>
                                        <InputError class="mt-2" :message="form.errors.ReceivedAmount" />
                                    </div>
                                </div>

                                <!-- Submit Date -->
                                <div class="col-md-4">
                                    <div class="mb-1">
                                        <InputLabel value="Submit Date" /> <span class="text-danger">★</span>
                                        <TextInput
                                            type="date"
                                            v-model="form.SubmitDate"
                                            class="form-control"
                                            :min="currentMonthStart"
                                            :max="currentMonthEnd"
                                        />
                                        <InputError class="mt-2" :message="form.errors.SubmitDate" />
                                    </div>
                                </div>

                                <!-- Fine Amount -->
                                <div class="col-md-4">
                                    <div class="mb-1">
                                        <InputLabel value="Fine Amount" /> <span class="text-danger">★</span>
                                        <TextInput type="number" v-model="form.fine_amount" class="form-control" disabled />
                                        <InputError class="mt-2" :message="form.errors.fine_amount" />
                                    </div>
                                </div>

                                <!-- Payment Mode -->
                                <div class="col-md-4">
                                    <div class="mb-1">
                                        <InputLabel value="Payment Mode" /> <span class="text-danger">★</span>
                                        <ul class="nav nav-tabs">
                                            <li v-for="method in ['Cash','Cheque','DD']" :key="method" class="nav-item">
                                                <button
                                                    class="nav-link"
                                                    :class="{ active: form.PaymentMode === method }"
                                                    @click.prevent="form.PaymentMode = method"
                                                >
                                                    {{ method.toUpperCase() }}
                                                </button>
                                            </li>
                                        </ul>
                                        <div class="mt-3">
                                            <p class="text-muted">Selected: {{ form.PaymentMode }}</p>
                                        </div>
                                        <InputError class="mt-2" :message="form.errors.PaymentMode" />
                                    </div>
                                </div>

                                <!-- Note -->
                                <div class="col-md-4">
                                    <div class="mb-1">
                                        <InputLabel value="Note" />
                                        <TextInput type="text" v-model="form.note" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.note" />
                                    </div>
                                </div>
                                
                                <!-- Latest Challan / Submit -->
                                <!-- Check Expiry -->
                                <div class="col-md-12" v-if="isExpired">
                                    <div class="text-end">
                                        <h5 class="text-danger">Your Challan is Expired Please Create New Challan</h5>
                                    </div>
                                </div>

                                <!-- Latest Challan -->
                                <div class="col-md-12" v-else-if="props.latestChallan">
                                    <div class="text-end">
                                        <h4 class="text-danger">{{ props.latestChallan }}</h4>
                                    </div>
                                </div>

                                <!-- Submit -->
                                <div class="col-md-12" v-else>
                                    <div class="text-end">
                                        <PrimaryButton :disabled="(balanceAmount > 0 && form.ReceivedAmount < 0) || form.processing">
                                            Submit
                                        </PrimaryButton>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Challan List -->
        <div class="card" v-if="PartialChallanList.length > 0">
            <div class="header">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="fw-bold">Collected Challan List</div>
                </div>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table mb-0 text-center table-hover table-bordered">
                        <TableHeader :columns="columns" />
                        <tbody>
                            <tr v-for="(challan, index) in PartialChallanList" :key="index">
                                <td>{{ challan?.fee_challan?.challan_no }}</td>
                                <td>{{ challan?.fee_challan?.student?.FirstName }}</td>
                                <td>{{ challan?.fee_challan?.student?.class?.ClassName }}</td>
                                <td>{{ challan?.fee_challan?.student?.section?.SectionName }}</td>
                                <td>{{ challan?.fee_challan?.ChallanMonth }}</td>
                                <td>{{ challan?.SubmitDate }}</td>
                                <td>{{ challan?.ReceivedAmount }}</td>
                                <td>{{ challan?.PaymentMode }}</td>
                                <td>
                                    <button class="btn btn-sm btn-danger" @click="openDeleteModal(challan)">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Delete Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Delete</h5>
                        <button type="button" class="text-white close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="text-center modal-body fw-bold">
                        Are you sure you want to delete the challan item?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" :disabled="isDeleting">Cancel</button>
                        <button @click="confirmDelete" type="button" class="btn btn-danger" :disabled="isDeleting">{{ isDeleting ? 'Deleting...' : 'Delete' }}</button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>