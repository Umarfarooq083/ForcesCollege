<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link,Head, router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import { ref, computed } from 'vue';

const props = defineProps({
    staffList: Object,
    disable_reasons: Array,
    errors: Object,
});

const columns = [
    { label: 'Profile' },
    { label: 'Staff Code' },
    { label: 'Role' },
    { label: 'Designation' },
    { label: 'Department' },
    { label: 'Name' },
    { label: 'Gender' },
    { label: 'Date Of Joining' },
    { label: 'Status' },
    { label: 'Action' },
];

const selectedStaff = ref(null);
const reason = ref({ id: '', name: '' });
const fromDate = ref('');
const errors = ref({});

const isCurrentlyActive = computed(() => selectedStaff.value?.IsActive);

const openModal = (staff) => {
    selectedStaff.value = staff;
    reason.value = '';
    fromDate.value = '';
    errors.value = {};
    $('#toggleStatusModal').modal('show');
};

const validateForm = () => {
    const validationErrors = {};
    
    // For disable: reason must have an id (dropdown selection)
    // For enable: reason must be a non-empty string (textarea)
    if (isCurrentlyActive.value) {
        if (!reason.value || !reason.value.id) {
            validationErrors.reason = 'Reason is required.';
        }
        if (!fromDate.value) {
            validationErrors.fromDate = 'From Date is required.';
        }
    } else {
        if (!reason.value || reason.value.trim() === '') {
            validationErrors.reason = 'Comment is required.';
        }
    }

    errors.value = validationErrors;
    return Object.keys(validationErrors).length === 0;
};

const toggleStatus = () => {
    if (isCurrentlyActive.value) {
        if (!validateForm()) return;
    }

    router.put(route('staff.toggleStatus', selectedStaff.value.id), 
    {
        ReasonId: reason.value.id,
        Reason: reason.value,
        FromDate: fromDate.value,
        Status: isCurrentlyActive.value ? 'disabled' : 'enabled',
    },
    {
        preserveScroll: true,
        onSuccess: () => {
            $('#toggleStatusModal').modal('hide');
        },
    });
};
</script>
<template>

    <Head title="Staff List" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Staff List
            </h2>
        </template>
      
        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Staff List</div>
                    <Link v-if="$page.props.auth.user.user_permissions.indexOf('staff.create') > -1" :href="route('staff.create')" class="btn btn-success btn-sm" title="Create New Staff">
                    <i class="fa fa-plus"></i>
                    </Link>
                </div>
            </div>
            
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 c_list table-bordered">
                        <TableHeader :columns="columns" />
                       
                        <tbody>
                            <tr v-for="list,index in staffList.data">
                                <td>
                                     <img :src="list.PhotoPathUrl" style="width: 60px; height: 60px;" />
                                </td>
                                <td>
                                      {{ list.department_rel?.Code }} - {{ list.id }}
                                </td>
                                <td>    
                                        {{ list.staff_role?.name }}
                                </td>
                                <td>
                                        {{ list.designation_rel?.DesignationName }}
                                </td>
                                <td>
                                         {{ list.department_rel?.DepartmentName }}
                                </td>
                                <td>
                                        {{ list?.FirstName }} {{ list?.LastName }}
                                </td>
                                <td>
                                        {{ list.Gender }}
                                </td>
                                <td>
                                        {{ list.DateOfJoining }}
                                </td>
                                <td>
                                    <span>
                                        <span class="badge badge-success" v-if="list.IsActive === 1 ">Active</span>
                                        <span v-if="list.IsActive === 0 ">
                                            <span class="badge badge-default">{{ list?.disabled_reason?.DisableReasonName }}</span><br>
                                        </span>
                                    </span>
                                </td>
                                <td>
                                    <div class="action_btn">
                                         <Link v-if="$page.props.auth.user.user_permissions.indexOf('staff.edit') > -1" :href="route('staff.edit',{id:list.id})" method="get" type="button" 
                                             class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></Link>

                                         <Link v-if="$page.props.auth.user.user_permissions.indexOf('staff.delete') > -1" :href="route('staff.delete',{id:list.id})" method="delete" type="button" 
                                             class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash"></i></Link>
                                      
                                         <button
                                             v-if="list.IsActive === 0 && $page.props.auth.user.user_permissions.indexOf('staff.toggleStatus') > -1"
                                             @click="openModal(list)"
                                             class="btn btn-dark btn-sm"
                                             title="Enable">
                                             <i class="fa fa-lock"></i>
                                         </button>

                                         <button
                                             v-if="list.IsActive === 1 && $page.props.auth.user.user_permissions.indexOf('staff.toggleStatus') > -1"
                                             @click="openModal(list)"
                                             class="btn btn-success btn-sm"
                                             title="Disable">
                                             <i class="fa fa-unlock"></i>
                                         </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        
                    </table>
                </div>
                <Pagination :links="staffList.links" />
            </div>
        </div>

        <div class="modal fade" id="toggleStatusModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            
                            {{ isCurrentlyActive ? 'Disable' : 'Enable' }} Staff
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Staff Name</label>
                            <input class="form-control" :value="selectedStaff ? selectedStaff.FirstName + ' ' + selectedStaff.LastName : ''" disabled>
                        </div>
                   
                        <div v-if="isCurrentlyActive" class="form-group">
                            <label>Reason</label><span class="text-danger font-12 position-absolute">★</span>
                            <select class="form-control" v-model="reason">
                                <option disabled value="">Select reason</option>
                                <option
                                    v-for="r in disable_reasons"
                                    :key="r.id"
                                    :value="{ id: r.id, name: r.DisableReasonName }"
                                >
                                    {{ r.DisableReasonName }}
                                </option>
                            </select>
                            <small v-if="errors.reason" class="text-danger">{{ errors.reason }}</small>
                        </div>
                        <div v-else class="form-group">
                            <label>Comment</label><span class="text-danger font-12 position-absolute">★</span>
                            <textarea class="form-control" v-model="reason" rows="3" placeholder="Enter reason for enabling"></textarea>
                            <small v-if="errors.reason" class="text-danger">{{ errors.reason }}</small>
                        </div>
                        <div v-if="isCurrentlyActive" class="form-group">
                            <label>From Date</label><span class="text-danger font-12 position-absolute">★</span>
                            <input type="date" class="form-control" v-model="fromDate" />
                            <small v-if="errors.fromDate" class="text-danger">{{ errors.fromDate }}</small>
                            <small v-if="props.errors" class="text-danger">{{ props.errors.FromDate }}</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success" @click="toggleStatus">Yes, Confirm</button>
                    </div>
                </div>
            </div>
        </div>
        
    </AuthenticatedLayout>
</template>
<style>
.roles_class {
    background-color: green;
    padding: 5px 4px 5px 5px;
    border-radius: 20px;
    color: #fff;
}

.roles_not_selected {
    background-color: #e23636d4;
    padding: 5px 4px 5px 5px;
    border-radius: 20px;
    color: #fff;
}
</style>