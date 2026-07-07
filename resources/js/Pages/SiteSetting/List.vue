<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';

const props = defineProps({
    settings: Array
})

const columns = [
  { label: 'ID' },
  { label: 'Name' },
  { label: 'Value' },
  { label: 'Action' },
];

</script>
<template>
    <Head title="Zone" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Site Settings
            </h2>
        </template>
   
        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Site Setting List</div>
                    <Link v-if="$page.props.auth.user.user_permissions.indexOf('setting.create') > -1" :href="route('setting.create')" class="btn btn-success btn-sm" title="Create New Zone">
                        <i class="fa fa-plus"></i>
                    </Link>
                </div>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table mb-0 text-center table-hover c_list table-bordered">
                        <TableHeader :columns="columns" />
                        <tbody>
                            <tr v-for="setting in settings" :key="setting.id">
                                <td>{{ setting.id }}</td>
                                <td>{{ setting.name }}</td>
                                <td>
                                    <span class="badge">
                                        {{ setting.value }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action_btn">
                                       <div class="custom-control custom-switch">
                                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('setting.edit') > -1" :href="route('setting.edit',{setting_id:setting.id})" method="get" type="button" 
                                                class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></Link>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
