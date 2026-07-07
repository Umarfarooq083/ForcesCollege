<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    staffList: Array,
    errors: Object,
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
const years = [];
for (let year = currentYear - 1; year <= currentYear + 2; year++) {
    years.push(year);
}

const form = useForm({
    staff_id: '',
    month: new Date().getMonth() + 1,
    year: currentYear,
});
</script>

<template>
    <Head title="Payroll Slip" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Payroll Slip</h2>
        </template>

        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Generate Payroll Slip</div>
                </div>
            </div>

            <div class="body">
                <form @submit.prevent="form.get(route('payrollslip.create'))">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <InputLabel value="Staff" />
                                <select class="form-control" v-model="form.staff_id">
                                    <option value="">Select a Staff</option>
                                    <option v-for="staff in staffList" :key="staff.id" :value="staff.id">
                                        {{ staff.FirstName }} {{ staff.LastName }}
                                    </option>
                                </select>
                                <InputError class="mt-2" :message="errors.staff_id" />
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <InputLabel value="Month" />
                                <select class="form-control" v-model="form.month">
                                    <option v-for="month in months" :key="month.value" :value="month.value">
                                        {{ month.label }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <InputLabel value="Year" />
                                <select class="form-control" v-model="form.year">
                                    <option v-for="year in years" :key="year" :value="year">
                                        {{ year }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <PrimaryButton type="submit">Preview Payroll</PrimaryButton>
                    </div>
                </form>
            </div>
        </div>

        <!-- <div class="card mt-4">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Payroll Slip List</div>
                    <Link :href="route('payrollslip.show')" class="btn btn-primary btn-sm">
                        View All
                    </Link>
                </div>
            </div>
        </div> -->
    </AuthenticatedLayout>
</template>