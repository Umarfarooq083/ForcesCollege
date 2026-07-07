<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, Head, router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    fineDeductions: Object,
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
    { label: 'Applicable Month' },
    { label: 'Amount' },
    { label: 'Reason' },
    { label: 'Action' },
];

const deleteFineDeduction = (id) => {
    if (confirm('Are you sure you want to delete this fine deduction?')) {
        router.delete(route('finededuction.delete', { id }), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Fine Deductions" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Fine Deductions</h2>
        </template>

        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Fine Deductions List</div>
                    <Link :href="route('finededuction.create')" class="btn btn-success btn-sm" title="Create New Fine Deduction">
                        <i class="fa fa-plus"></i>
                    </Link>
                </div>
            </div>

            <div class="body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 c_list table-bordered">
                        <TableHeader :columns="columns" />

                        <tbody>
                            <tr v-for="fineDeduction, index in fineDeductions.data" :key="fineDeduction.id">
                                <td>{{ (fineDeductions.current_page - 1) * fineDeductions.per_page + index + 1 }}</td>
                                <td>{{ fineDeduction.staff?.FirstName }} {{ fineDeduction.staff?.LastName }}</td>
                                <td>{{ fineDeduction.apply_year }}</td>
                                <td>{{ months[fineDeduction.applicable_month] }}</td>
                                <td>{{ fineDeduction.amount }}</td>
                                <td>{{ fineDeduction.reason }}</td>
                                <td>
                                    <div class="action_btn">
                                        <Link :href="route('finededuction.edit', { id: fineDeduction.id })" method="get" type="button"
                                            class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></Link>
                                        <button type="button" @click="deleteFineDeduction(fineDeduction.id)"
                                            class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="fineDeductions.links" />
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