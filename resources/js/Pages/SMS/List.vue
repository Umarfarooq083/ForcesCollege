<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import { Link, router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';

defineProps({
    SmsCredits: Object
});

const columns = [
  { label: 'Credit', key: 'credit' },
  { label: 'Added Date', key: 'created_at' },
];

</script>

<template>

    <Head title="SMS Credit" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                SMS Credit
            </h2>
        </template>
       
        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">SMS Credit</div>
                    <div class="d-flex align-items-center gap-2">
                       
                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('smscredit.create') > -1" :href="route('smscredit.create')" 
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
                            <tr v-for="creditlist,index in SmsCredits.data">
                                <td >
                                     {{creditlist.smsCreditCount}}
                                </td>
                                <td>
                                     {{creditlist.created_at}}
                                </td>       
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="SmsCredits.links" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
