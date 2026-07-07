<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, Head, router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import { ref, computed, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    students: Object,
    disable_reasons: Array,
    errors: Object,
});


const getSectionName = (student) => {
    const sections = student.class?.sections || [];
    const match = sections.find(sec => sec.id === student.SectionId);
    return match ? match.SectionName : 'N/A';
};


const selectedStudent = ref(null);
// const reason = ref('');
const reason = ref({ id: '', name: '' });
const fromDate = ref('');
const toDate = ref('');
const errors = ref({});


const isCurrentlyActive = computed(() => selectedStudent.value?.IsActive);


const openModal = (student) => {
    selectedStudent.value = student;
    reason.value = '';
    fromDate.value = '';
    toDate.value = '';
    errors.value = {};
    $('#toggleStatusModal').modal('show');
};

const dateCalculation = (promoted_date) => {

    if (!promoted_date) return false;

    const today = new Date();
    const promoted = new Date(promoted_date.replace(' ', 'T'));

    // Only compare dates (ignore time completely)
    const todayUTC = Date.UTC(
        today.getFullYear(),
        today.getMonth(),
        today.getDate()
    );

    const promotedUTC = Date.UTC(
        promoted.getFullYear(),
        promoted.getMonth(),
        promoted.getDate()
    );

    const diffDays = Math.floor((todayUTC - promotedUTC) / (1000 * 60 * 60 * 24));

    return diffDays >= 0 && diffDays <= 5;
};

const validateForm = () => {
    const validationErrors = {};
    if (!reason.value.id) {
        validationErrors.reason = 'Reason is required.';
    }

    if (isCurrentlyActive.value) {
        if (!fromDate.value) {
            validationErrors.fromDate = 'From Date is required.';
        }
        if (!toDate.value) {
            validationErrors.toDate = 'To Date is required.';
        }
    }

    errors.value = validationErrors;
    return Object.keys(validationErrors).length === 0;
};


const toggleStatus = () => {
    if(!isCurrentlyActive){
        if (!validateForm()) return;
    }

    router.put(route('student.toggleStatus', selectedStudent.value.id), 
    {
        ReasonId: reason.value.id,
        Reason: reason.value,
        FromDate: fromDate.value,
        ToDate: toDate.value,
        Status: isCurrentlyActive.value ? 'disabled' : 'enabled',
    },
    {
        preserveScroll: true,
        onSuccess: () => {
            $('#toggleStatusModal').modal('hide');
        },
    });
};

const columns = [
    { label: 'Roll No' },
    { label: 'Class' },
    { label: 'Section' },
    { label: 'Name' },
    { label: 'Gender' },
    { label: 'Status' },
    { label: 'Action' },
];

const search = ref('');
watch(search, debounce((value) => {
  router.get(route('student.index'), { search: value }, {
    preserveState: true,
    replace: true,
  });
}, 800));

</script>
<style>
    .ribbon {
        font-size: 12px;
        font-weight: bold;
        color: #fff;
        text-transform: uppercase;
        --f: .5em; 
        --r: .8em;
        position: absolute;
        top: 5px;
        right: calc(-1*var(--f));
        padding-inline: .25em;
        line-height: 1.8;
        background: var(--green);
        border-bottom: var(--f) solid #0005;
        border-left: var(--r) solid #0000;
        clip-path: 
            polygon(var(--r) 0,100% 0,100% calc(100% - var(--f)),calc(100% - var(--f)) 100%,
            calc(100% - var(--f)) calc(100% - var(--f)),var(--r) calc(100% - var(--f)),
            0 calc(50% - var(--f)/2));
    }
</style>

<template>
    <Head title="Student" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Student
            </h2>
        </template>
       
        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Student List</div>
                    <input type="text" class="form-control form-control-sm" v-model="search" placeholder="Search By Student Name, Roll#, Class & Section" style="width: 380px;"/>
                </div>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table mb-0 table-hover c_list table-bordered">
                        <TableHeader :columns="columns" />
                        <tbody>
                            <tr v-for="(student, index) in students.data" :key="student.id">
                                <!-- {{ student.promoted_date }} -->
                                <td class="position-relative">
                                    {{ student?.RollNumber }}
                                      <span 
                                    v-if="dateCalculation(student.promoted_date)" 
                                    class="ribbon">
                                    Promoted
                                </span>
                                    <!-- <span>
                                        {{ dateCalculation(student.promoted_date) }}
                                    </span> -->
                                    <!-- <span class="ribbon">Promoted</span> -->
                                </td>
                                <td>{{ student.class?.ClassName }}</td>
                                <td>{{ getSectionName(student) }}</td>
                                <td>{{ student?.FirstName  }} {{  student?.LastName }}</td>
                                <td>{{ student.Gender }}</td>
                                <td>
                                    <span>
                                        <span class="badge badge-success" v-if="student.IsActive === 1 ">Active</span>

                                        <span v-if="student.IsActive === 0 ">
                                            <span class="badge badge-default">Freeze</span><br>
                                            <small class="text-success">{{ student?.disabled_reason?.DisableReasonName }}</small>
                                        </span>
                                    <!-- <span class="badge badge-danger" v-if="student.IsActive === 0 && student.IsDisable === 1 ">Withdrawal</span>
                                        <span class="badge badge-danger" v-if="student.IsActive === 0 && student.IsDisable === 0 ">Withdrawal</span> -->
                                    </span> <br>
                                </td>
                                <td>
                                    <div class="action_btn">
                                         <Link  
                                            v-if="$page.props.auth.user.user_permissions.indexOf('student.edit') > -1"
                                            :href="route('student.edit',{id:student.id})" method="get" type="button" 
                                            class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></Link>
                                        
                                        <button class="btn btn-sm" 
                                            :title="(student.IsActive === 1 ) ? 'Freeze Student' : 'Active Student'"
                                            :class="(student.IsActive === 0) ? 'btn-danger' : 'btn-success'" 
                                            @click="openModal(student)">
                                            <i class="fa" :class="( student.IsActive == 1) ? 'fa-unlock' : 'fa-lock'"></i>
                                        </button>

                                        <Link 
                                            v-if="$page.props.auth.user.user_permissions.indexOf('student.detail') > -1"
                                            :href="route('student.detail', {id:student.id})"
                                            method="get" type="button" class="btn btn-info btn-sm"
                                            title="Detail">
                                            <i class="fa fa-info-circle"></i>
                                        </Link>
                                        
                                        <Link 
                                            v-if="$page.props.auth.user.user_permissions.indexOf('student.card') > -1"
                                            :href="route('student.card', {id:student.id})"
                                            method="get" type="button" class="btn btn-success btn-sm"
                                            title="Student Card">
                                            <i class="fa fa-user"></i>
                                        </Link>
                                       
                                        <Link 
                                            v-if="student.withdraw_status == null || student.withdraw_status == 'Rejected' && $page.props.auth.user.user_permissions.indexOf('student.toggleStatus') > -1"
                                            :href="route('student.withdraw', {id:student.id})"
                                            method="get" type="button" class="btn btn-warning btn-sm"
                                            title="Withdraw">
                                            <i class="fa fa-sign-out"></i>
                                        </Link>
                                    </div>
                                   
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="students.links" />
            </div>
        </div>

        <div class="modal fade" id="toggleStatusModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            
                            {{ (isCurrentlyActive ===1 ) ? 'Disable' : 'Enable' }} Student
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Student Name</label>
                            <input class="form-control" :value="selectedStudent ? selectedStudent.FirstName + ' ' + selectedStudent.LastName : ''" disabled>
                        </div>
                        <!-- {{ selectedStudent.IsDisable }} -->
               
                        <div v-if="isCurrentlyActive == 1" class="form-group">
                            <label>Reason</label><span class="text-danger font-12 position-absolute">★</span>
                            <!-- <select class="form-control" v-model="reason">
                                <option value="">Select reason</option>
                                <option v-for="r in disable_reasons" :key="r.id" :value="r.DisableReasonName">
                                    {{ r.DisableReasonName }}
                                </option>
                            </select> -->
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
                        <div v-if="isCurrentlyActive === 1" class="form-group">
                            <label>From Date</label><span class="text-danger font-12 position-absolute">★</span>
                            <input type="date" class="form-control" v-model="fromDate" />
                            <small v-if="errors.fromDate" class="text-danger">{{ errors.fromDate }}</small>
                            <small v-if="props.errors" class="text-danger">{{ props.errors.FromDate }}</small>
                        </div>
                        <div v-if="isCurrentlyActive === 1" class="form-group">
                            <label>To Date</label><span class="text-danger font-12 position-absolute">★</span>
                            <input type="date" class="form-control" v-model="toDate" />
                            <small v-if="errors.toDate" class="text-danger">{{ errors.toDate }}</small>
                            <small v-if="props.errors" class="text-danger">{{ props.errors.ToDate }}</small>
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
