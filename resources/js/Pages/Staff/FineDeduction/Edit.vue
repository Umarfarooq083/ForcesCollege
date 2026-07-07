<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    fineDeduction: Object,
    staffList: Object,
});

const months = [
    { value: 1, label: 'January' },
    { value: 2, label: 'February' },
    { value: 3, label: 'March' },
    { value: 4, label: 'April' },
    { value: 5, label: 'May' },
    { value: 6, label: 'June' },
    { value: 7, label: 'July' },
    { value: 8, label: 'August' },
    { value: 9, label: 'September' },
    { value: 10, label: 'October' },
    { value: 11, label: 'November' },
    { value: 12, label: 'December' },
];

const currentYear = new Date().getFullYear();
const years = ref([]);

for (let year = currentYear; year <= currentYear + 10; year++) {
    years.value.push(year);
}

const form = useForm({
    id: props.fineDeduction.id,
    staff_id: props.fineDeduction.staff_id,
    apply_year: props.fineDeduction.apply_year,
    applicable_month: props.fineDeduction.applicable_month,
    amount: props.fineDeduction.amount,
    reason: props.fineDeduction.reason,
});
</script>

<template>
    <Head title="Edit Fine Deduction" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Edit Fine Deduction</h2>
        </template>

        <form @submit.prevent="form.put(route('finededuction.update'))">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Edit Fine Deduction</div>
                        <div class="body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Staff *" />
                                        <select class="form-control" v-model="form.staff_id">
                                            <option selected disabled value="">Select a Staff</option>
                                            <option v-for="staff in staffList" :key="staff.id" :value="staff.id">
                                                {{ staff.FirstName }} {{ staff.LastName }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.staff_id" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Apply Year *" />
                                        <select class="form-control" v-model="form.apply_year">
                                            <option selected disabled value="">Select Year</option>
                                            <option v-for="year in years" :key="year" :value="year">
                                                {{ year }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.apply_year" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Applicable Month *" />
                                        <select class="form-control" v-model="form.applicable_month">
                                            <option selected disabled value="">Select Month</option>
                                            <option v-for="month in months" :key="month.value" :value="month.value">
                                                {{ month.label }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.applicable_month" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Amount *" />
                                        <TextInput type="number" step="0.01" v-model="form.amount" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.amount" />
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <InputLabel value="Reason *" />
                                        <textarea v-model="form.reason" class="form-control" rows="3"></textarea>
                                        <InputError class="mt-2" :message="form.errors.reason" />
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3 text-end">
                                        <label class="w-100 d-inline-block">&nbsp;</label>
                                        <PrimaryButton>Update</PrimaryButton>
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