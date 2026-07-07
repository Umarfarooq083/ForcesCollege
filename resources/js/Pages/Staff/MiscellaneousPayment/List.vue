<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, Head, router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    miscellaneousPayments: Object,
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

const deleteMiscellaneousPayment = (id) => {
    if (confirm('Are you sure you want to delete this miscellaneous payment?')) {
        router.delete(route('miscellaneouspayment.delete', { id }), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Miscellaneous Payments" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Miscellaneous Payments</h2>
        </template>

        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Miscellaneous Payments List</div>
                    <Link :href="route('miscellaneouspayment.create')" class="btn btn-success btn-sm" title="Create New Miscellaneous Payment">
                        <i class="fa fa-plus"></i>
                    </Link>
                </div>
            </div>

            <div class="body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 c_list table-bordered">
                        <TableHeader :columns="columns" />

                        <tbody>
                            <tr v-for="miscellaneousPayment, index in miscellaneousPayments.data" :key="miscellaneousPayment.id">
                                <td>{{ (miscellaneousPayments.current_page - 1) * miscellaneousPayments.per_page + index + 1 }}</td>
                                <td>{{ miscellaneousPayment.staff?.FirstName }} {{ miscellaneousPayment.staff?.LastName }}</td>
                                <td>{{ miscellaneousPayment.apply_year }}</td>
                                <td>{{ months[miscellaneousPayment.applicable_month] }}</td>
                                <td>{{ miscellaneousPayment.amount }}</td>
                                <td>{{ miscellaneousPayment.reason }}</td>
                                <td>
                                    <div class="action_btn">
                                        <Link :href="route('miscellaneouspayment.edit', { id: miscellaneousPayment.id })" method="get" type="button"
                                            class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></Link>
                                        <button type="button" @click="deleteMiscellaneousPayment(miscellaneousPayment.id)"
                                            class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="miscellaneousPayments.links" />
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