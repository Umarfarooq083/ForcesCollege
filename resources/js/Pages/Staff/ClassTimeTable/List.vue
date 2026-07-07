<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { ref } from 'vue';


const props = defineProps({
    staffList: Object,
    timetables: Object
})

const form = useForm({
    StaffId: '',
    month: '',
    date: '',
});


const monthList = [
  'January', 'February', 'March', 'April', 'May', 'June',
  'July', 'August', 'September', 'October', 'November', 'December'
];

function submitForm() {
    form.get(route('classtimetable.index'), {
        preserveState: true,
        replace: true,
    });
}

const designationToDelete = ref(null);

function openDeleteModal(designation) {
    designationToDelete.value = designation;
    $('#deleteModal').modal('show');
}

function confirmDelete() {
    if (designationToDelete.value) {
        router.delete(route('designation.destroy', designationToDelete.value.id), {
            onFinish: () => {
                $('#deleteModal').modal('hide');
                designationToDelete.value = null;
                router.reload();
            }
        });
    }
}

const columns = [
    { label: 'Sr. No' },
    { label: 'Class' },
    { label: 'Section' },
    { label: 'Subject' },
    { label: 'Staff' },
    { label: 'Day' },
    { label: 'Date' },
    { label: 'Time From' },
    { label: 'Time To' },
    { label: 'Action' },
];

</script>
<template>

    <Head title="Class Timetable" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Class Timetable
            </h2>
        </template>
        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Class Timetable List</div>
                    <Link v-if="$page.props.auth.user.user_permissions.indexOf('classtimetable.create') > -1" :href="route('classtimetable.create')" class="btn btn-success btn-sm"
                        title="Create New Class Timetable" >
                    <i class="fa fa-plus"></i>
                    </Link>
                </div>
            </div>
            
            <div class="body">
                 <div class="w-100">
                    <form @submit.prevent="submitForm()">
                        <div class="row align-items-end">
                            <!-- Staff -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <InputLabel value="Staff" class="font-14 font-weight-bold"/>
                                    <select v-model="form.StaffId" class="form-control">
                                        <option value="" disabled>Select Staff</option>
                                        <option v-for="staff in props.staffList" :key="staff.id" :value="staff.id">
                                            {{ staff.FirstName }} {{ staff.LastName }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.StaffId" />
                                </div>
                            </div>

                            <!-- Month -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <InputLabel value="Month" class="font-14 font-weight-bold"/>
                                    <select class="form-control" v-model="form.month">
                                        <option disabled value="">Select a Month</option>
                                        <option v-for="(month, index) in monthList" :key="index" :value="index + 1">
                                            {{ month }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.month" />
                                </div>
                            </div>

                            <!-- Date -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <InputLabel value="Date" class="font-14 font-weight-bold"/>
                                    <TextInput type="date" v-model="form.date" class="form-control" />
                                    <InputError class="mt-2" :message="form.errors.date" />
                                </div>
                            </div>

                            <!-- Button -->
                            <div class="col-md-3">
                                <div class="mb-3">
                                    <PrimaryButton :disabled="form.processing" class="w-100">
                                        <span v-if="form.processing" class="spinner-border spinner-border-sm me-1"></span>
                                        {{ form.processing ? 'Applying...' : 'Apply' }}
                                    </PrimaryButton>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="table-responsive">
                    <table class="table mb-0 table-hover c_list table-bordered">
                        <TableHeader :columns="columns" />
                        <tbody>
                            <tr v-for="(timetable, index) in timetables.data" :key="timetable.id">
                                <td> {{ (timetables.current_page - 1) * timetables.per_page + index + 1 }}</td>
                                <td>{{ timetable.ClassName }}</td>
                                <td>{{ timetable.SectionName }}</td>
                                <td>{{ timetable.SubjectName }}</td>
                                <td>{{ timetable.StaffName }}</td>
                                <td>{{ timetable.Day }}</td>
                                <td>{{ timetable.Date }}</td>
                                <td>{{ timetable.TimeFrom }}</td>
                                <td>{{ timetable.TimeTo }}</td>
                                <td>
                                    <Link class="btn btn-info btn-sm" title="Detail"><i class="fa fa-info-circle pull-right"></i></Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="timetables.links" />
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                        <button type="button" class="text-white close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="m-0 text-center font-weight-bold font-16">
                            Are you sure you want to delete the designation '<span
                                class="text-danger font-weight-bold">{{
                                    designationToDelete?.DesignationName }}</span>'?
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button @click="confirmDelete" type="button" class="btn btn-danger">Yes, Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
