<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, Head, router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    gazettedLeaves: Object,
});

const columns = [
    { label: 'ID' },
    { label: 'Title' },
    { label: 'Date' },
    { label: 'Status' },
    { label: 'Action' },
];

const deleteGazettedLeave = (id) => {
    if (confirm('Are you sure you want to delete this gazetted leave?')) {
        router.delete(route('gazettedleave.delete', { id }), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Gazetted Leaves" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Gazetted Leaves</h2>
        </template>

        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Gazetted Leaves List</div>
                    <Link :href="route('gazettedleave.create')" class="btn btn-success btn-sm" title="Create New Gazetted Leave">
                        <i class="fa fa-plus"></i>
                    </Link>
                </div>
            </div>

            <div class="body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 c_list table-bordered">
                        <TableHeader :columns="columns" />

                        <tbody>
                            <tr v-for="list, index in gazettedLeaves.data" :key="list.id">
                                <td>{{ (gazettedLeaves.current_page - 1) * gazettedLeaves.per_page + index + 1 }}</td>
                                <td>{{ list.title }}</td>
                                <td>{{ list.date ? list.date.split('T')[0] : '' }}</td>
                                <td>
                                    <span v-if="list.status" class="text-white badge bg-success">Active</span>
                                    <span v-else class="text-white badge bg-secondary">Inactive</span>
                                </td>
                                <td>
                                    <div class="action_btn">
                                        <Link :href="route('gazettedleave.edit', { id: list.id })" method="get" type="button"
                                            class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></Link>
                                        <button type="button" @click="deleteGazettedLeave(list.id)"
                                            class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="gazettedLeaves.links" />
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