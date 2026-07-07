<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link,Head, router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import {ref, watch, computed} from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    CampusFeesMasterList: Object,
    LmsSession:Object,
});

const lms_session = computed(()=> {
    return props?.LmsSession?.start_date + ' to ' + props?.LmsSession?.end_date; 
});

const columns = [
    { label: 'Index' },
    { label: 'Fee Type' },
    { label: 'Class' },
    { label: 'Amount' },
    { label: 'Session' },
    { label: 'Action' },
];

const search = ref('');
watch(search, debounce((value) => {
  router.get(route('feemaster.list'), { search: value }, {
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
                Campus Fees Master
            </h2>
        </template>

        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Campus Fees Master</div>  
                    
                    <div class="d-flex align-items-center gap-2">
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            v-model="search"
                            placeholder="Search By Fee Type & Class Name"
                            style="width: 250px;"
                        />

                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('feemaster.create') > -1":href="route('feemaster.create')" 
                            class="btn btn-success btn-sm"
                            title="Create New Fee Master"
                            data-tooltip="Create New Fee Master">
                            <i class="fa fa-plus"></i>
                        </Link>
                    </div>
                </div>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table mb-0 text-center table-hover c_list table-bordered">
                        <TableHeader :columns="columns" />
                        <tbody>
                            <tr v-if="CampusFeesMasterList.data.length > 0" v-for="CampusFeesMaster,index in CampusFeesMasterList.data" :key="CampusFeesMaster.id">
                                   <!-- {{ CampusFeesMaster }} -->
                                    <td>
                                    {{ (CampusFeesMasterList.current_page - 1) * CampusFeesMasterList.per_page + index + 1 }}
                                    </td>
                                    <td>{{ CampusFeesMaster?.fee_master_type?.FeeName }}</td>
                                    <td>{{ CampusFeesMaster?.fee_master_class?.ClassName }}</td>
                                    <td>{{ CampusFeesMaster.Amount }}</td>
                                    <td> {{ CampusFeesMaster?.session_rel?.start_date }} -{{ CampusFeesMaster?.session_rel?.end_date }} </td>
                                    <td>
                                        <div class="action_btn">
                                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('feemaster.edit') > -1" :href="route('feemaster.edit',{campus_fee_master_id:CampusFeesMaster?.id})" method="get" type="button" 
                                                class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></Link>
                                       
                                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('feemaster.delete') > -1" :href="route('feemaster.delete',{id:CampusFeesMaster.id})" method="delete" type="button" 
                                            class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash"></i></Link>
                                        </div>
                                    </td>
                            </tr>
                            <tr v-else>
                                <td colspan="6">No record found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="CampusFeesMasterList.links" />
            </div>
        </div>
        
    </AuthenticatedLayout>
</template>