<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, Head, router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    securityRefunds: Object,
});

const months = {
    1: 'January', 2: 'February', 3: 'March', 4: 'April',
    5: 'May', 6: 'June', 7: 'July', 8: 'August',
    9: 'September', 10: 'October', 11: 'November', 12: 'December',
};

const columns = [
    { label: 'ID' },
    { label: 'Staff' },
    { label: 'Apply Date' },
    { label: 'Applicable Month' },
    { label: 'Amount' },
    { label: 'Action' },
];

const deleteSecurityRefund = (id) => {
    if (confirm('Are you sure you want to delete this security refund?')) {
        router.delete(route('securityrefund.delete', { id }), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Security Refunds" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Security Refunds</h2>
        </template>

        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Security Refunds List</div>
                    <Link :href="route('securityrefund.create')" class="btn btn-success btn-sm" title="Create New Security Refund">
                        <i class="fa fa-plus"></i>
                    </Link>
                </div>
            </div>

            <div class="body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 c_list table-bordered">
                        <TableHeader :columns="columns" />

                        <tbody>
                            <tr v-for="securityRefund, index in securityRefunds.data" :key="securityRefund.id">
                                <td>{{ (securityRefunds.current_page - 1) * securityRefunds.per_page + index + 1 }}</td>
                                <td>{{ securityRefund.staff?.FirstName }} {{ securityRefund.staff?.LastName }}</td>
                                <td>{{ securityRefund.apply_date }}</td>
                                <td>{{ months[securityRefund.applicable_month] }}</td>
                                <td>{{ securityRefund.amount }}</td>
                                <td>
                                    <div class="action_btn">
                                        <Link :href="route('securityrefund.edit', { id: securityRefund.id })" method="get" type="button"
                                            class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></Link>
                                        <button type="button" @click="deleteSecurityRefund(securityRefund.id)"
                                            class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="securityRefunds.links" />
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