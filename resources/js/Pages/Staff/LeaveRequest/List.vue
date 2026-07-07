<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, Head, router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    leaveRequests: Object,
});

const columns = [
    { label: 'ID' },
    { label: 'Staff Name' },
    { label: 'Date' },
    { label: 'Leave Type' },
    { label: 'Reason' },
    { label: 'Status' },
    { label: 'Approved By' },
    { label: 'Action' },
];

const deleteLeaveRequest = (id) => {
    if (confirm('Are you sure you want to delete this leave request?')) {
        router.delete(route('leave-request.delete', { id }), {
            preserveScroll: true,
        });
    }
};

const approveLeaveRequest = (id) => {
    if (confirm('Are you sure you want to approve this leave request?')) {
        router.put(route('leave-request.approve', { id }), {
            status: 'approved',
            approval_note: '',
        }, {
            preserveScroll: true,
        });
    }
};

const rejectLeaveRequest = (id) => {
    if (confirm('Are you sure you want to reject this leave request?')) {
        router.put(route('leave-request.approve', { id }), {
            status: 'rejected',
            approval_note: '',
        }, {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Leave Requests" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Leave Requests</h2>
        </template>

        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Leave Requests List</div>
                    <Link :href="route('leave-request.create')" class="btn btn-success btn-sm" title="Create New Leave Request">
                        <i class="fa fa-plus"></i>
                    </Link>
                </div>
            </div>

            <div class="body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 c_list table-bordered">
                        <TableHeader :columns="columns" />

                        <tbody>
                            <tr v-for="list, index in leaveRequests.data" :key="list.id">
                                <td>{{ (leaveRequests.current_page - 1) * leaveRequests.per_page + index + 1 }}</td>
                                <td>{{ list?.staff?.FirstName }} {{ list?.staff?.LastName }}</td>
                                <td>{{ list.date ? list.date.split('T')[0] : '' }}</td>
                                <td>{{ list.leave_type }}</td>
                                <td>{{ list.reason }}</td>
                                <td>
                                    <span v-if="list.status === 'pending'" class="text-white badge bg-warning">Pending</span>
                                    <span v-else-if="list.status === 'approved'" class="text-white badge bg-success">Approved</span>
                                    <span v-else class="text-white badge bg-danger">Rejected</span>
                                </td>
                                <td>{{ list?.approver?.name || '-' }}</td>
                                <td>
                                    <div class="action_btn">
                                        <Link :href="route('leave-request.edit', { id: list.id })" method="get" type="button"
                                            class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></Link>
                                        <button v-if="list.status === 'pending'" type="button"
                                            @click="approveLeaveRequest(list.id)"
                                            class="btn btn-success btn-sm" title="Approve"><i class="fa fa-check"></i></button>
                                        <button v-if="list.status === 'pending'" type="button"
                                            @click="rejectLeaveRequest(list.id)"
                                            class="btn btn-danger btn-sm" title="Reject"><i class="fa fa-times"></i></button>
                                        <button type="button" @click="deleteLeaveRequest(list.id)"
                                            class="btn btn-outline-danger btn-sm" title="Delete"><i class="fa fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="leaveRequests.links" />
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