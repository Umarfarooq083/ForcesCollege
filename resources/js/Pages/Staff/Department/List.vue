<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link,Head } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';


const props = defineProps({
    departmentData: Object,
});

const columns = [
    { label: 'ID' },
    { label: 'Department Name' },
    { label: 'Code' },
    { label: 'Action' },
];
</script>
<template>

    <Head title="Department List" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Department List
            </h2>
        </template>
      
        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Department List</div>
                    <Link v-if="$page.props.auth.user.user_permissions.indexOf('department.create') > -1" :href="route('department.create')" class="btn btn-success btn-sm" title="Create New User"
                        data-tooltip="Create New User">
                    <i class="fa fa-plus"></i>
                    </Link>
                </div>
            </div>
         
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 c_list table-bordered">
                        <TableHeader :columns="columns" />
                       
                        <tbody>
                            <tr v-for="list,index in departmentData.data">
                                <td>
                                   
                                    {{ (departmentData.current_page - 1) * departmentData.per_page + index + 1 }}
                                </td>
                                <td>
                                    {{ list.DepartmentName }}
                                </td>
                                <td>
                                    {{ list.Code }}
                                </td>
                                <td>
                                    <div class="action_btn">
                                         <Link v-if="$page.props.auth.user.user_permissions.indexOf('department.edit') > -1" :href="route('department.edit',{id:list.id})" method="get" type="button" 
                                            class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        
                    </table>
                </div>
                <Pagination :links="departmentData.links" />
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