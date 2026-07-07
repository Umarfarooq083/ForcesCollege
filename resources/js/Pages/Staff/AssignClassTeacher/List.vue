<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link,Head } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';


const props = defineProps({
    assignClassTeacher: Object,
});

const columns = [
    { label: 'Class' },
    { label: 'Section' },
    { label: 'Staff' },
    { label: 'Action' },
];
</script>
<template>

    <Head title="Assign Class Teacher" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Assign Class Teacher
            </h2>
        </template>
      
        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Assign Class Teacher</div>
                    <Link v-if="$page.props.auth.user.user_permissions.indexOf('assign.class.teacher.create') > -1" :href="route('assign.class.teacher.create')" class="btn btn-success btn-sm" title="Create Assign Class Teacher"
                        data-tooltip="Create Assign Class Teacher">
                    <i class="fa fa-plus"></i>
                    </Link>
                </div>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 c_list table-bordered">
                        <TableHeader :columns="columns" />
                       
                        <tbody>
                            <tr v-for="assignClass in assignClassTeacher.data">
                                <td>
                                   {{ assignClass?.class_rel?.ClassName }}
                                </td>
                                <td>
                                    {{ assignClass?.section_rel?.SectionName }}
                                </td>
                                <td>
                                    {{ assignClass?.staff_rel?.FirstName }}
                                </td>
                                <td>
                                    <div class="action_btn">
                                         <Link v-if="$page.props.auth.user.user_permissions.indexOf('assign.class.teacher.edit') > -1" :href="route('assign.class.teacher.edit',{id:assignClass.id})" method="get" type="button" 
                                            class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        
                    </table>
                </div>
                <Pagination :links="assignClassTeacher.links" />
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