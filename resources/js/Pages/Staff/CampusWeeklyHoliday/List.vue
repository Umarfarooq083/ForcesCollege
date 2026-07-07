<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, Head, router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    holidays: Object,
});

const weekendOptions = {
    saturday: 'Saturday',
    sunday: 'Sunday',
    both: 'Saturday & Sunday',
    none: 'No Weekend',
};

const columns = [
    { label: 'ID' },
    { label: 'Campus' },
    { label: 'Weekend Day' },
    { label: 'Status' },
    { label: 'Action' },
];

const deleteHoliday = (id) => {
    if (confirm('Are you sure you want to delete this campus weekly holiday?')) {
        router.delete(route('campus-weekly-holiday.delete', { id }), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Weekly Holidays" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Weekly Holidays</h2>
        </template>

        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Weekly Holidays</div>
                    <Link :href="route('campus-weekly-holiday.create')" class="btn btn-success btn-sm" title="Create New Holiday Configuration">
                        <i class="fa fa-plus"></i>
                    </Link>
                </div>
            </div>

            <div class="body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 c_list table-bordered">
                        <TableHeader :columns="columns" />

                        <tbody>
                            <tr v-for="holiday, index in holidays.data" :key="holiday.id">
                                <td>{{ (holidays.current_page - 1) * holidays.per_page + index + 1 }}</td>
                                <td>{{ holiday.campus?.SchoolName }}</td>
                                <td>{{ weekendOptions[holiday.weekend_day] }}</td>
                                <td>
                                    <span v-if="holiday.is_active" class="badge badge-success">Active</span>
                                    <span v-else class="badge badge-secondary">Inactive</span>
                                </td>
                                <td>
                                    <div class="action_btn">
                                        <Link :href="route('campus-weekly-holiday.edit', { id: holiday.id })" method="get" type="button"
                                            class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></Link>
                                        <button type="button" @click="deleteHoliday(holiday.id)"
                                            class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="holidays.links" />
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