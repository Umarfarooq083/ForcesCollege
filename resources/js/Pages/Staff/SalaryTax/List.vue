<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, Head, router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    salaryTaxes: Object,
});

const columns = [
    { label: 'ID' },
    { label: 'Staff' },
    { label: 'Amount' },
    { label: 'Reason' },
    { label: 'Action' },
];

const deleteSalaryTax = (id) => {
    if (confirm('Are you sure you want to delete this salary tax?')) {
        router.delete(route('salarytax.delete', { id }), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Salary Taxes" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Salary Taxes</h2>
        </template>

        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Salary Taxes List</div>
                    <Link :href="route('salarytax.create')" class="btn btn-success btn-sm" title="Create New Salary Tax">
                        <i class="fa fa-plus"></i>
                    </Link>
                </div>
            </div>

            <div class="body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 c_list table-bordered">
                        <TableHeader :columns="columns" />

                        <tbody>
                            <tr v-for="salaryTax, index in salaryTaxes.data" :key="salaryTax.id">
                                <td>{{ (salaryTaxes.current_page - 1) * salaryTaxes.per_page + index + 1 }}</td>
                                <td>{{ salaryTax.staff?.FirstName }} {{ salaryTax.staff?.LastName }}</td>
                                <td>{{ salaryTax.amount }}</td>
                                <td>{{ salaryTax.reason }}</td>
                                <td>
                                    <div class="action_btn">
                                        <Link :href="route('salarytax.edit', { id: salaryTax.id })" method="get" type="button"
                                            class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></Link>
                                        <button type="button" @click="deleteSalaryTax(salaryTax.id)"
                                            class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="salaryTaxes.links" />
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