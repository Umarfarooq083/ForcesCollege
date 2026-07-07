<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import { ref } from 'vue';


const props = defineProps({
    designations: Object
})


const toggleStatus = (designation) => {
    router.put(route('designation.toggleStatus', designation.id), {
        status: !designation.IsActive
    }, {
        preserveScroll: true,
    });
};


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
    { label: 'Name' },
    { label: 'Status' },
    { label: 'Action' },
];

</script>
<template>
    <Head title="Designation" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Designation
            </h2>
        </template>
   
        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Designation List</div>
                    <Link v-if="$page.props.auth.user.user_permissions.indexOf('designation.create') > -1" :href="route('designation.create')" class="btn btn-success btn-sm" title="Create New Designation" >
                        <i class="fa fa-plus"></i>
                    </Link>
                </div>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 c_list table-bordered">
                        <TableHeader :columns="columns" />
                        <tbody>
                            <tr v-for="(designation, index) in designations.data" :key="designation.id">
                                <td> {{ (designations.current_page - 1) * designations.per_page + index + 1 }}</td>
                                <td>{{ designation.DesignationName }}</td>
                                <td>
                                    <span class="badge" :class="designation.IsActive ? 'badge-success' : 'badge-danger'">
                                        {{ designation.IsActive ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action_btn">
                                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('designation.edit') > -1" :href="route('designation.edit', {id:designation.id})" class="btn btn-sm btn-info">
                                            <i class="fa fa-edit"></i>
                                        </Link>
                                        <button v-if="$page.props.auth.user.user_permissions.indexOf('designation.toggleStatus') > -1"
                                            class="btn btn-sm btn-danger"
                                            @click="openDeleteModal(designation)"
                                        >
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="designations.links" />
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                   <div class="modal-body">
                        <p class="font-weight-bold text-center font-16 m-0">
                          Are you sure you want to delete the designation '<span  class="text-danger font-weight-bold" >{{ designationToDelete?.DesignationName }}</span>'?
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
