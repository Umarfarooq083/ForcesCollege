<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import { ref } from 'vue';

const props = defineProps({
    disablestudents: Object
})

const selectedStudent = ref(null);
const comment = ref('');
const errors = ref({});

const enableStudent = (student) => {
    selectedStudent.value = student;
    comment.value = '';
    errors.value = {};
    $('#enableStudentModal').modal('show');
};

const validateForm = () => {
    const validationErrors = {};
    if (!comment.value) {
        validationErrors.comment = 'Comment is required.';
    }
    errors.value = validationErrors;
    return Object.keys(validationErrors).length === 0;
};

const confirmEnable = () => {
    if (!validateForm()) return;
    
    router.put(route('student.toggleStatus', selectedStudent.value.student_id), {
        ReasonId: selectedStudent.value.DisableReasonId,
        Reason: comment.value,
        FromDate: selectedStudent.value.FromDate,
        ToDate: selectedStudent.value.ToDate,
        Status: 'enabled',
    }, {
        preserveScroll: true,
        onSuccess: () => {
            $('#enableStudentModal').modal('hide');
        },
    });
};

const columns = [
  { label: 'Sr. No' },
  { label: 'Student' },
  { label: 'From' },
  { label: 'To' },
  { label: 'Reason' },
  { label: 'Current Status' },
  { label: 'Action' },
];

</script>
<template>
    <Head title="Disable Student List" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Disable Student List
            </h2>
        </template>
   
        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold"> Disable Student List</div>
                </div>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 c_list table-bordered">
                        <TableHeader :columns="columns" />
                        <tbody>
                            <tr v-for="(disablestudent, index) in disablestudents.data" :key="disablestudent.id">
                                <td> {{ (disablestudents.current_page - 1) * disablestudents.per_page + index + 1 }}</td>
                                <td>{{ disablestudent.name }}</td>
                                <td>{{ disablestudent.FromDate ??  disablestudent.created_at}}</td>
                                <td>{{ disablestudent.ToDate ?? 'N/A' }}</td>
                                <td>{{ disablestudent.Reason ?? '—' }}</td>
                                <td>
                                    <span class="badge" :class="disablestudent.status ? 'badge-success' : 'badge-danger'">
                                        {{ disablestudent.status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action_btn">
                                        <button type="button" class="btn btn-sm btn-success" 
                                            @click="enableStudent(disablestudent)"
                                            title="Enable Student">
                                            <i class="fa fa-unlock"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="disablestudents.links" />
            </div>
        </div>

        <div class="modal fade" id="enableStudentModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Enable Student</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Student Name</label>
                            <input class="form-control" :value="selectedStudent ? selectedStudent.name : ''" disabled>
                        </div>
                        <div class="form-group">
                            <label>Comment</label><span class="text-danger font-12 position-absolute">★</span>
                            <textarea class="form-control" v-model="comment" rows="3" placeholder="Enter reason for enabling"></textarea>
                            <small v-if="errors.comment" class="text-danger">{{ errors.comment }}</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success" @click="confirmEnable">Yes, Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
