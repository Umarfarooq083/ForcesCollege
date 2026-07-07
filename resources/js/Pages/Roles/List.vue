<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import { Link, router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import {ref, watch} from 'vue';
import debounce from 'lodash/debounce';

defineProps({
    roles: Object
});

const columns = [
  { label: 'ID', key: 'id' },
  { label: 'Name', key: 'name' },
  { label: 'Campus Name', key: 'Campus Name' },
  { label: 'Is Super', key: 'is_super' },
  { label: 'Status', key: 'status' },
  { label: 'Action' },
];

const search = ref('');
watch(search, debounce((value) => {
  router.get(route('role.index'), { search: value }, {
    preserveState: true,
    replace: true,
  });
}, 800));

// const actions = [
//   {
//     label: 'Edit',
//     route: (row) => route('role.edit', { id: row.id }),
//     method: 'get',
//     icon: 'fa fa-edit',
//     class: 'btn-info',
//   },
//   {
//     label: 'Delete',
//     route: (row) => route('role.delete', { id: row.id }),
//     method: 'delete',
//     icon: 'fa fa-trash-o',
//     class: 'btn-danger',
//   },
  
// ];

</script>

<template>

    <Head title="Roles" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Roles
            </h2>
        </template>
       
        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Role List</div>

                    <div class="d-flex align-items-center gap-2">
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            v-model="search"
                            placeholder="Search By Role Name"
                            style="width: 220px;"
                        />

                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('role.create') > -1" :href="route('role.create')" 
                            class="btn btn-success btn-sm"
                            title="Create New Role"
                            data-tooltip="Create New Role">
                            <i class="fa fa-plus"></i>
                        </Link>
                    </div>
                    
                </div>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table mb-0 table-hover c_list table-bordered">
                        <TableHeader :columns="columns" />
                        <tbody>
                            <tr v-for="roleslist,index in roles.data">
                                <td >
                                    {{ (roles.current_page - 1) * roles.per_page + index + 1 }}
                                </td>
                                <td >
                                     {{roleslist.name}}
                                </td>
                                <td>
                                     {{roleslist?.tenant_domain?.domain ?? 'Not Selected'}}
                                </td>
                                <td>
                                     {{roleslist.is_super}}
                                </td>
                                  <td >
                                     {{roleslist.status}}
                                </td>
                                <td>
                                    <div class="action_btn">
                                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('role.edit') > -1" :href="route('role.edit', { id: roleslist.id })" method="get" type="button" class="btn btn-info btn-sm" title="Edit"><i
                                            class="fa fa-edit"></i></Link>
                                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('role.permission.assign') > -1" :href="route('role.permission.assign', { id: roleslist.id })" method="get"  class="btn btn-dark btn-sm"
                                            title="Assing Permission"><i class="fa fa-lock" aria-hidden="true"></i></Link>
                                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('role.delete') > -1" :href="route('role.delete', { id: roleslist.id })" method="delete"  class="btn btn-danger btn-sm"
                                            title="Delete"><i class="fa fa-trash-o"></i></Link>
                                    </div>  
                                </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
                <Pagination :links="roles.links" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
