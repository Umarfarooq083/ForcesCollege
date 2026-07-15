<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import { ref ,onMounted } from 'vue';

defineProps({
    campusList: Object
});
const isHeadOfficeDomain = ref()
onMounted(async () => {
    try {
       const fullDomain = window.location.hostname; // "headoffice.lms"
        isHeadOfficeDomain.value = fullDomain.split('.')[0]; // "headoffice"
    } catch (error) {
        console.error('Failed to load campus:', error)
    } 
})

const columns = [
  { label: 'ID', key: 'id' },
  { label: 'School Name', key: 'SchoolName' },
  { label: 'Owner Name', key: 'OwnerName' },
  { label: 'Campus Category', key: 'campus_category' },
  { label: 'Address', key: 'Address' },
  { label: 'Phone No', key: 'PhoneNo' },
  { label: 'Email', key: 'EmailAddress' },
  { label: 'Domain', key: 'DomainName' },
  { label: 'Zone' },
  { label: 'Region' },
  { label: 'Action' },
];
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
                    <div class="font-weight-bold">Campus List</div>
                    <Link v-if="isHeadOfficeDomain === 'headoffice' && $page.props.auth.user.user_permissions.indexOf('campus.create') > -1" :href="route('campus.create')" class="btn btn-success btn-sm" title="Create New Campus" data-tooltip="Create New Campus">
                        <i class="fa fa-plus"></i>
                    </Link>
                </div>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 c_list table-bordered">
                        <TableHeader :columns="columns" />

                         <tbody>
                            <tr v-for="campus,index in campusList.data">
                                <td >
                                    {{ (campusList.current_page - 1) * campusList.per_page + index + 1 }}
                                </td>
                                <td>
                                    {{campus.SchoolName}}
                                </td>
                                <td >
                                    {{campus.OwnerName}}
                                </td>
                                <td>
                                    {{campus?.campus_category?.CategoryName || 'N/A'}}
                                </td>
                                <td>
                                    {{campus.Address}}
                                </td>
                                <td>
                                    {{campus.PhoneNo}}
                                </td>
                                <td>
                                    {{campus.EmailAddress}}
                                </td>
                                <td>
                                    {{campus.DomainName}}
                                </td>
                                <td>
                                    {{campus?.zone?.name}}
                                </td>
                                <td>
                                    {{campus?.region?.name}}
                                </td>
                                 
                                <td>
                                    <div class="action_btn">
                                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('campus.edit') > -1" :href="route('campus.edit', { id: campus.id })" method="get" type="button" class="btn btn-info btn-sm" title="Edit"><i
                                            class="fa fa-edit"></i></Link>
                                    <!-- <Link :href="route('campus.delete', { id: campus.id })" method="delete"  class="btn btn-danger btn-sm"
                                        title="Delete"><i class="fa fa-trash-o"></i></Link> -->
                                    </div>
                                </td>
                            </tr>

                        </tbody>

                    </table>
                </div>
            </div>
        </div>
        <Pagination :links="campusList.links" />
    </AuthenticatedLayout>
</template>
