<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link,Head,router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import RecordNotFound from '@/Components/TableRecordNotFound.vue';
import {ref, watch} from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    studentInquiry: Object
});

const toggleStatus = (inquiry) => {
    console.log(inquiry.id)
    router.put(route('inquiry.status', inquiry.id), {
        status: !inquiry.Status
    }, {
        preserveScroll: true,
    });
};

const columns = [
    { label: 'ID' },
    { label: 'Name' },
    { label: 'Date' },
    { label: 'Gender' },
    { label: 'DOB' },
    { label: 'Father Name' },
    { label: 'Phone No' },
    { label: 'Address' },
    { label: 'Source' },
    { label: 'Action' },
];

const search = ref('');
watch(search, debounce((value) => {
  router.get(route('inquiry.index'), { search: value }, {
    preserveState: true,
    replace: true,
  });
}, 800));
</script>
<template>

    <Head title="Students Inquiries" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Students Inquiries
            </h2>
        </template>
      
        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center align-items-center">
                    <div class="font-weight-bold">Students Inquiries</div>
                    <div class="d-flex align-items-center gap-2">
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            v-model="search"
                            placeholder="Search By Inquiry Name, Father Name & Phone No."
                            style="width: 350px;"
                        />

                        <Link
                            v-if="$page.props.auth.user.user_permissions.indexOf('inquiry.create') > -1"
                            :href="route('inquiry.create')"
                            class="btn btn-success btn-sm"
                            title="Create New User"
                            data-tooltip="Create New User"
                        >
                            <i class="fa fa-plus"></i>
                        </Link>
                    </div>
                </div>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table mb-0 table-hover c_list table-bordered">
                        <TableHeader :columns="columns" />
                        <tbody v-if="studentInquiry.data.length > 0">
                            <tr v-for="inquiry, index in studentInquiry.data">

                                <td>
                                    {{ (studentInquiry.current_page - 1) * studentInquiry.per_page + index + 1 }}
                                </td>
                                <td>
                                    {{ inquiry.Name }}
                                </td>
                                <td>
                                    {{ inquiry.Date }}
                                </td>
                                <td>
                                    {{ inquiry.Gender }}
                                </td>
                                <td>
                                    {{ inquiry.BirthDate }}
                                </td>
                                <td>
                                    {{ inquiry.FatherName }}
                                </td>
                                <td>
                                    {{ inquiry.FatherPhoneNo ?? '0000-0000000' }}
                                </td>
                                <td>
                                    {{ inquiry.Address }}
                                </td>
                                 <td>
                                    {{ inquiry?.source?.SourceName }}
                                </td>
                                <td>
                                    <div class="action_btn">
                                       <!-- <div class="custom-control custom-switch">
                                        <input
                                            type="checkbox"
                                            class="custom-control-input"
                                            :id="'switch-' + inquiry.id"
                                            :checked="inquiry.Status"
                                            @change="toggleStatus(inquiry)"
                                            >
                                            <label class="custom-control-label cstm_switch" :for="'switch-' + inquiry.id"></label>
                                        </div> -->
                                        <span v-if="inquiry.student_inquiry_relation"> 
                                           <a style="color: #fff;" v-if="$page.props.auth.user.user_permissions.indexOf('student.create') > -1"
                                                class="btn btn-success btn-sm"
                                                title="Admission Inquiry" >
                                                <i class="fa fa-plus"></i>
                                           </a>  
                                        </span>
                                        <span v-else> 
                                           <Link v-if="$page.props.auth.user.user_permissions.indexOf('student.create') > -1"
                                                :href="route('student.create', {id:inquiry.id})"
                                                type="button"
                                                class="btn btn-danger btn-sm"
                                                title="Admission Inquiry"
                                                >
                                                <i class="fa fa-plus"></i>
                                            </Link>  
                                        </span>
                                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('inquiry.detail') > -1"
                                            :href="route('inquiry.detail', {id:inquiry.id})"
                                            method="get" type="button" class="btn btn-info btn-sm"
                                            title="Detail">
                                            <i class="fa fa-info-circle"></i>
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                           <RecordNotFound></RecordNotFound>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="studentInquiry.links" />
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