<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, Head, router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    lateFines: Object,
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

const deleteLateFine = (id) => {
    if (confirm('Are you sure you want to delete this late fine?')) {
        router.delete(route('latefine.delete', { id }), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Late Fines" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Late Fines</h2>
        </template>

        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Late Fines List</div>
                    <Link :href="route('latefine.create')" class="btn btn-success btn-sm" title="Create New Late Fine">
                        <i class="fa fa-plus"></i>
                    </Link>
                </div>
            </div>

            <div class="body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 c_list table-bordered">
                        <TableHeader :columns="columns" />

                        <tbody>
                            <tr v-for="lateFine, index in lateFines.data" :key="lateFine.id">
                                <td>{{ (lateFines.current_page - 1) * lateFines.per_page + index + 1 }}</td>
                                <td>{{ lateFine.staff?.FirstName }} {{ lateFine.staff?.LastName }}</td>
                                <td>{{ lateFine.apply_year }}</td>
                                <td>{{ months[lateFine.applicable_month] }}</td>
                                <td>{{ lateFine.amount }}</td>
                                <td>{{ lateFine.reason }}</td>
                                <td>
                                    <div class="action_btn">
                                        <Link :href="route('latefine.edit', { id: lateFine.id })" method="get" type="button"
                                            class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></Link>
                                        <button type="button" @click="deleteLateFine(lateFine.id)"
                                            class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="lateFines.links" />
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