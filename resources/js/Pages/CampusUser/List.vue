<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Link, router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import {ref, watch} from 'vue';
import debounce from 'lodash/debounce';

const props =  defineProps({
    users:Object
})
const columns = [
  { label: 'ID' },
  { label: 'Name' },
  { label: 'Email' },
  { label: 'Phone No' },
  { label: 'Address' },
  { label: 'Roles' },
  { label: 'Action' },
];

const search = ref('');
watch(search, debounce((value) => {
  router.get(route('user.index'), { search: value }, {
    preserveState: true,
    replace: true,
  });
}, 800));

</script>
<template>
    <Head title="Campus" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Campus
            </h2>
        </template>
   
        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Campus User List</div>

                    <div class="d-flex align-items-center gap-2">
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            v-model="search"
                            placeholder="Search By User Name"
                            style="width: 220px;"
                        />

                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('user.create') > -1" :href="route('user.create')" 
                            class="btn btn-success btn-sm"
                            title="Create New User"
                            data-tooltip="Create New User">
                            <i class="fa fa-plus"></i>
                        </Link>
                    </div>
                </div>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 c_list table-bordered">
                        <TableHeader :columns="columns" />
                         <tbody>
                            <tr v-for="userList,index in users.data">
                                <td>
                                    {{ (users.current_page - 1) * users.per_page + index + 1 }}
                                </td>
                                <td>
                                     {{ userList.name }}
                                </td>
                                  <td>
                                    {{ userList.email }}
                                </td>
                                  <td>
                                    {{ userList.phone_no ?? '0000-0000000' }}                      
                                </td>
                                  <td>
                                    {{ userList.address }}
                                    
                                </td>
                                <td>
                                     <span  v-if="userList.roles.length > 0" v-for="role in userList.roles">
                                        <span class="badge badge-primary"> {{ role?.name ?? 'No Role' }}</span>&nbsp;
                                    </span> 
                                    <span class="badge badge-danger" v-else>
                                        No Role Selected
                                    </span> 
                                    
                                </td>
                                <td>
                                    <div class="action_btn">
                                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('user.edit') > -1" :href="route('user.edit', { id: userList.id })" method="get" type="button" class="btn btn-info btn-sm" title="Edit"><i
                                            class="fa fa-edit"></i></Link>
                                    <Link v-if="$page.props.auth.user.user_permissions.indexOf('user.delete') > -1" :href="route('user.delete', { id: userList.id })" method="delete"  class="btn btn-danger btn-sm"
                                        title="Delete"><i class="fa fa-trash-o"></i></Link>
                                    </div>
                                </td>
                            </tr>

                        </tbody>

                    </table>
                </div>
            </div>
        </div>
        <Pagination :links="users.links" />
    </AuthenticatedLayout>
</template>