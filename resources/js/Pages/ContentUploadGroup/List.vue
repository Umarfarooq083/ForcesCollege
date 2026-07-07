<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link,Head,router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    UploadContentGroup: Object,
});

const columns = [
    { label: 'ID' },
    { label: 'Group Name' },
    { label: 'Action' },
];
</script>
<template>

    <Head title="Content Group" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Content Group
            </h2>
        </template>
      
        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Content Group</div>
                    <Link v-if="$page.props.auth.user.user_permissions.indexOf('content.create') > -1" :href="route('content.create')" class="btn btn-success btn-sm" title="Create New User"
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
                            <tr v-for="content,index  in UploadContentGroup.data">
                                <td>
                                    {{ index + 1 }}
                                    <!-- {{ (subject.current_page - 1) * subject.per_page + index + 1 }} -->
                                </td>
                                <td>
                                  {{ content.name }}
                                </td>
                                <td>
                                    <div class="action_btn">
                                         <Link v-if="$page.props.auth.user.user_permissions.indexOf('content.edit') > -1" :href="route('content.edit',{id:content.id})" method="get" type="button" 
                                            class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></Link>
                                        <Link :href="route('content.delete', { id: content.id })" method="get"  class="btn btn-danger btn-sm"
                                        title="Delete"><i class="fa fa-trash-o"></i></Link>

                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        
                    </table>
                </div>
                <Pagination :links="UploadContentGroup.links" />
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