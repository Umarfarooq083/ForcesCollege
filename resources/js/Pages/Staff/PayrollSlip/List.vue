<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, Head, router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import { ref } from 'vue';

const props = defineProps({
    payrollSlips: Object,
    staffList: Array,
    filters: Object,
});

const months = {
    1: 'January', 2: 'February', 3: 'March', 4: 'April',
    5: 'May', 6: 'June', 7: 'July', 8: 'August',
    9: 'September', 10: 'October', 11: 'November', 12: 'December',
};

const filterForm = ref({
    staff_id: props.filters?.staff_id || '',
    month: props.filters?.month || '',
    year: props.filters?.year || '',
});

const columns = [
    { label: 'ID' },
    { label: 'Staff' },
    { label: 'Month/Year' },
    { label: 'Gross Salary' },
    { label: 'Net Salary' },
    { label: 'Status' },
    { label: 'Action' },
];

const applyFilter = () => {
    router.get(route('payrollslip.show'), filterForm.value, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Payroll Slips" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Payroll Slips</h2>
        </template>

        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Payroll Slips List</div>
                    <Link :href="route('payrollslip.index')" class="btn btn-success btn-sm">
                        <i class="fa fa-plus"></i> Generate New
                    </Link>
                </div>
            </div>

            <div class="body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <select class="form-control" v-model="filterForm.staff_id">
                            <option value="">All Staff</option>
                            <option v-for="staff in staffList" :key="staff.id" :value="staff.id">
                                {{ staff.FirstName }} {{ staff.LastName }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" v-model="filterForm.month">
                            <option value="">All Months</option>
                            <option v-for="(label, value) in months" :key="value" :value="value">
                                {{ label }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="number" class="form-control" v-model="filterForm.year" placeholder="Year" />
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary" @click="applyFilter">Filter</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover mb-0 c_list table-bordered">
                        <TableHeader :columns="columns" />

                        <tbody>
                            <tr v-for="slip, index in payrollSlips.data" :key="slip.id">
                                <td>{{ (payrollSlips.current_page - 1) * payrollSlips.per_page + index + 1 }}</td>
                                <td>{{ slip.staff?.FirstName }} {{ slip.staff?.LastName }}</td>
                                <td>{{ months[slip.payroll_month] }} {{ slip.payroll_year }}</td>
                                <td>{{ slip.gross_salary }}</td>
                                <td>{{ slip.net_salary }}</td>
                                <td>
                                    <span class="badge bg-success" v-if="slip.status === 'generated'">Generated</span>
                                    <span class="badge bg-warning" v-else>{{ slip.status }}</span>
                                </td>
<td>
    <div class="action_btn">
        <Link :href="route('payrollslip.detail', { id: slip.id })" method="get" type="button"
              class="btn btn-info btn-sm" title="View"><i class="fa fa-eye"></i></Link>
        <a :href="route('payrollslip.download', { id: slip.id })" target="_blank" rel="noopener noreferrer"
              class="btn btn-success btn-sm" title="Download PDF"><i class="fa fa-download"></i></a>
    </div>
</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <Pagination :links="payrollSlips.links" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
.action_btn {
    display: flex;
    gap: 5px;
}
</style>