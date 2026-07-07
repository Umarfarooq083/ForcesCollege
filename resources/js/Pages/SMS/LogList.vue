<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import { Link, router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';

defineProps({
    SmsLog: Object
});

const columns = [
  { label: 'Status', key: 'Status' },
  { label: 'Mobile No', key: 'MobileNo' },
  { label: 'SMS Content', key: 'SMS Content' },
  { label: 'Response Tex', key: 'Response Tex' },
  { label: 'Date', key: 'Date' },
  { label: 'SMS', key: 'SMS' },
  { label: 'SMS Length', key: 'SMS Length' },
  { label: 'Actions', key: 'Actions' },
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
                            <tr v-for="loglist,index in SmsLog.data">
                                <td>
                                    {{ loglist.apItype }}
                                </td>
                                <td>
                                    {{ loglist.mobileNo }}
                                </td>       
                                <td>
                                    {{ loglist.body.length > 60 ? loglist.body.substring(0, 60) + '...' : loglist.body }}
                                    <!-- {{ loglist.body }} -->
                                </td>       
                                <td>
                                    {{ loglist.apiResponseText }}
                                </td>       
                                <td>
                                    {{ loglist.created_at }}
                                </td>       
                                <td>
                                    {{ loglist.smsCount }}
                                    
                                </td>       
                                <td>
                                    {{ loglist.characterLength }}
                                    
                                </td>       
                                <td>
                                    <!-- {{ loglist.smsCount }} -->
                                      <Link v-if="$page.props.auth.user.user_permissions.indexOf('smslog.detail') > -1"
                                            :href="route('smslog.detail', {id:loglist.id})"
                                            method="get" type="button" class="btn btn-info btn-sm"
                                            title="Detail">
                                            <i class="fa fa-info-circle"></i>
                                        </Link>
                                    
                                </td>       
                                       
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="SmsLog.links" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
