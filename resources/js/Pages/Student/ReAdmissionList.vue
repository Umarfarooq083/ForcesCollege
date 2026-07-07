<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, Link } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    readmissionStudents: Object
})

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
  { label: 'Withdrawal Reason' },
  { label: 'Status' },
  { label: 'Action' },
];
</script>

<template>
    <Head title="Re-Admission Student List" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Re-Admission Student List
            </h2>
        </template>
    
        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Re-Admission Student List</div>
                </div>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 c_list table-bordered">
                        <TableHeader :columns="columns" />
                        <tbody>
                            <tr v-for="(student, index) in readmissionStudents.data" :key="student.id">
                                <td>{{ (readmissionStudents.current_page - 1) * readmissionStudents.per_page + index + 1 }}</td>
                                <td>{{ student.FirstName }} {{ student.LastName }}</td>
                                <td>{{ student.RollNumber }}</td>
                                <td>{{ student.class?.ClassName || 'N/A' }}</td>
                                <td>{{ student.section?.SectionName || 'N/A' }}</td>
                                <td>{{ student?.last_challan_no }}</td>
                                <td>{{ student?.last_challan_amount }}</td>
                                <td>{{ student?.last_challan_status  }}</td>
                                <td>{{ formatDate(student.withdraw_date) }}</td>
                                <td>{{ student.withdraw_reason || '—' }}</td>
                                <td>
                                    <span class="badge badge-success">
                                        Approved (Withdrawn)
                                    </span>
                                </td>
                                <td>
                                    <div class="action_btn">
                                        <Link :href="route('student.readmission', student.id)" 
                                            class="btn btn-primary btn-sm" 
                                            title="Re-Admit Student">
                                            <i class="fa fa-undo"></i>
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="readmissionStudents.links" />
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