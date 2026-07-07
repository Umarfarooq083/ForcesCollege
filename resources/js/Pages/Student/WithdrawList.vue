<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    withdrawingStudents: Object
})

const approveWithdraw = (id) => {
    if (confirm('Are you sure you want to approve this withdraw request?')) {
        router.put(route('student.withdrawapprove', id), {}, {
            preserveScroll: true,
        });
    }
};

const rejectWithdraw = (id) => {
    if (confirm('Are you sure you want to reject this withdraw request?')) {
        router.put(route('student.withdrawreject', id), {}, {
            preserveScroll: true,
        });
    }
};

const formatDate = (date) => {
    if (!date) return 'N/A';
    return new Date(date).toISOString().split('T')[0];
};

const columns = [
  { label: 'Sr. No' },
  { label: 'Student' },
  { label: 'Roll Number' },
  { label: 'Class' },
  { label: 'Section' },
  { label: 'Challan No' },
  { label: 'Challan Amount' },
  { label: 'Challan Status' },
  { label: 'Withdrawal Date' },
  { label: 'Reason' },
  { label: 'Status' },
  { label: 'Action' },
];
</script>

<template>
    <Head title="Withdraw Student List" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Withdraw Student List
            </h2>
        </template>
    
        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Withdraw Student List</div>
                </div>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 c_list table-bordered">
                        <TableHeader :columns="columns" />
                        <tbody>
                            <tr v-for="(student, index) in withdrawingStudents.data" :key="student.id">
                                <td>{{ (withdrawingStudents.current_page - 1) * withdrawingStudents.per_page + index + 1 }}</td>
                                <td>{{ student.FirstName }} {{ student.LastName }}</td>
                                <td>{{ student.RollNumber }}</td>
                                <td>{{ student.class?.ClassName || 'N/A' }}</td>
                                <td>{{ student.section?.SectionName || 'N/A' }}</td>
                                <td>{{ student.last_challan_no || '—' }}</td>
                                <td>{{ student.last_challan_amount || '—' }}</td>
                                <td>{{ student.last_challan_status || '—' }}</td>
                                <td>{{ formatDate(student.withdraw_date) }}</td>
                                <td>{{ student.withdraw_reason || '—' }}</td>
                                <td>
                                    <span class="badge badge-warning" v-if="student.withdraw_status === 'Inprocess'">
                                        Pending
                                    </span>
                                    <span class="badge badge-success" v-else-if="student.withdraw_status === 'Approved'">
                                        Approved
                                    </span>
                                    <span class="badge badge-danger" v-else>
                                        Rejected
                                    </span>
                                </td>
                                <td>
                                    <div class="action_btn">
                                        <button v-if="student.withdraw_status === 'Inprocess'" 
                                            type="button"
                                            @click="approveWithdraw(student.id)"
                                            class="btn btn-success btn-sm" 
                                            title="Approve">
                                            <i class="fa fa-check"></i>
                                        </button>
                                        <button v-if="student.withdraw_status === 'Inprocess'" 
                                            type="button"
                                            @click="rejectWithdraw(student.id)"
                                            class="btn btn-danger btn-sm" 
                                            title="Reject">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="withdrawingStudents.links" />
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