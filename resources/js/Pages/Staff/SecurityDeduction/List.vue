<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, Head, router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    securityDeductions: Object,
});

const months = {
    1: 'January', 2: 'February', 3: 'March', 4: 'April',
    5: 'May', 6: 'June', 7: 'July', 8: 'August',
    9: 'September', 10: 'October', 11: 'November', 12: 'December',
};

const columns = [
    { label: 'ID' },
    { label: 'Staff' },
    { label: 'Apply Year' },
    { label: 'From Month' },
    { label: 'To Month' },
    { label: 'Amount' },
    { label: 'Action' },
];

const deleteSecurityDeduction = (id) => {
    if (confirm('Are you sure you want to delete this security deduction?')) {
        router.delete(route('securitydeduction.delete', { id }), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Security Deductions" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Security Deductions</h2>
        </template>

        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Security Deductions List</div>
                    <Link :href="route('securitydeduction.create')" class="btn btn-success btn-sm" title="Create New Security Deduction">
                        <i class="fa fa-plus"></i>
                    </Link>
                </div>
            </div>

            <div class="body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 c_list table-bordered">
                        <TableHeader :columns="columns" />

                        <tbody>
                            <tr v-for="securityDeduction, index in securityDeductions.data" :key="securityDeduction.id">
                                <td>{{ (securityDeductions.current_page - 1) * securityDeductions.per_page + index + 1 }}</td>
                                <td>{{ securityDeduction.staff?.FirstName }} {{ securityDeduction.staff?.LastName }}</td>
                                <td>{{ securityDeduction.apply_year }}</td>
                                <td>{{ months[securityDeduction.from_month] }}</td>
                                <td>{{ months[securityDeduction.to_month] }}</td>
                                <td>{{ securityDeduction.amount }}</td>
                                <td>
                                    <div class="action_btn">
                                        <Link :href="route('securitydeduction.edit', { id: securityDeduction.id })" method="get" type="button"
                                            class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></Link>
                                        <button type="button" @click="deleteSecurityDeduction(securityDeduction.id)"
                                            class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="securityDeductions.links" />
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