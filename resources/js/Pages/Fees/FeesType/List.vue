<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link,Head, router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import {ref, watch} from 'vue';
import debounce from 'lodash/debounce';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    feesType: Object,
});


const columns = [
    { label: 'Fee Code' },
    { label: 'Fee Name' },
    { label: 'Fee Cycle' },
    { label: 'Is Optional' },
    { label: 'Is Refundable' },
    { label: 'Is Royality' },
    { label: 'Action' },
];

const search = ref('');
watch(search, debounce((value) => {
  router.get(route('fees.list'), { search: value }, {
    preserveState: true,
    replace: true,
  });
}, 800));
</script>
<template>

    <Head title="Fees Type" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Fees Type
            </h2>
        </template>
        
        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Fees Type</div>

                    <div class="d-flex align-items-center gap-2">
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            v-model="search"
                            placeholder="Search By Fee Name, Fee Cole & Fee Cycle"
                            style="width: 295px;"
                        />

                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('fees.create') > -1" :href="route('fees.create')" 
                            class="btn btn-success btn-sm"
                            title="Create New User Type"
                            data-tooltip="Create New User Type">
                            <i class="fa fa-plus"></i>
                        </Link>
                    </div>


                    <!-- <Link v-if="$page.props.auth.user.user_permissions.indexOf('fees.create') > -1" :href="route('fees.create')" class="btn btn-success btn-sm" title="Create New User"
                        data-tooltip="Create New User">
                    <i class="fa fa-plus"></i>
                    </Link> -->
                </div>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 c_list table-bordered">
                        <TableHeader :columns="columns" />
                        <tbody>
                           <tr v-for="type in feesType.data">
                                <td>
                                    {{ type.FeesCode }}
                                </td>
                                <td>
                                    {{type.FeeName}}
                                </td>
                                <td>
                                    {{type.FeeCycle}}
                                </td>
                                
                                <td>
                                    <span v-if="type.IsOptional === false"  >
                                        No
                                    </span>
                                    <span v-else  >
                                        Yes
                                    </span>
                                </td>
                                <td>
                                    <span v-if="type.IsRefundable === false"  >
                                        No
                                    </span>
                                    <span v-else  >
                                        Yes
                                    </span>
                                </td>
                                <td>
                                    <span v-if="type.Isroyality === false"  >
                                        No
                                    </span>
                                    <span v-else  >
                                        Yes
                                    </span>
                                </td>
                                <td>
                                    <div class="action_btn">
                                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('fees.edit') > -1" :href="route('fees.edit',{id:type.id})" method="get" type="button" 
                                            class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></Link>

                                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('fees.delete') > -1" :href="route('fees.delete',{id:type.id})" method="delete" type="button" 
                                            class="btn btn-danger btn-sm" title="Edit"><i class="fa fa-trash"></i></Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="feesType.links" />
            </div>
        </div>
        
    </AuthenticatedLayout>
</template>