<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import { Link, Head, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import { ref, watch } from "vue";

const props = defineProps({
    studentFeeDiscount: Object,
    filters: Object,
});


const columns = [
    { label: 'Class'},
    { label: 'Section'},
    { label: 'Student'},
    { label: 'Session'},
    { label: 'Fee Cycle'},
    { label: 'Total Fee'},
    { label: 'Discount Amount'},
    { label: 'Balance Fee'},
    { label: 'Action'},
];

// const search = ref(props.filters.search || "");

const applySearch = () => {
  router.get(route("discount.list"), { search: search.value }, { preserveState: true, replace: true });
};

const search = ref('');
watch(search, debounce((value) => {
  router.get(route('discount.list'), { search: value }, {
    preserveState: true,
    replace: true,
  });
}, 800));
</script>
<template>

    <Head title="Fee Discount" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Fee Discount
            </h2>
        </template>
     
        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Fee Discount</div>

                    <div class="d-flex align-items-center gap-2">
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            v-model="search"
                            placeholder="Search By Class, Section, Student and Type Name ..."
                            style="width: 350px;"
                        />

                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('discount.create') > -1" :href="route('discount.create')"
                            class="btn btn-success btn-sm"
                            title="Create New Fee Master"
                            data-tooltip="Create New Fee Master">
                            <i class="fa fa-plus"></i>
                        </Link>
                    </div>

                    <!-- <Link v-if="$page.props.auth.user.user_permissions.indexOf('discount.create') > -1" :href="route('discount.create')" class="btn btn-success btn-sm" title="Create New User"
                        data-tooltip="Create New User">
                    <i class="fa fa-plus"></i>
                    </Link> -->
                </div>
            </div>
            <div class="body">
                <!-- <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="search_filter">Search</label>
                        <div class="input-group">
                            <input type="text" v-model="search" placeholder="Search..." class="form-control" @keyup.enter="applySearch"/>
                            <button class="btn btn-primary" @click="applySearch">Search</button>
                        </div>
                    </div>
                </div> -->
                <div class="table-responsive">
                    <table class="table table-hover mb-0 c_list table-bordered">
                        <TableHeader :columns="columns" />
                        <tbody>
                           <tr v-for="list in studentFeeDiscount.data">
                                <td>
                                    {{ list?.class_rel?.ClassName }}
                                </td>
                                <td>
                                    {{ list?.section_rel?.SectionName}}
                                </td>
                                <td>
                                    {{ list?.student_rel?.FirstName }} {{ list?.student_rel?.LastName }}
                                </td>
                                <td>    
                                    {{ list?.session_rel?.start_date }} - {{ list?.session_rel?.end_date }}
                                </td>
                                <td>
                                    {{ list?.campus_fees_master_rel?.fee_type_rel?.FeeName }}
                                </td>
                                <td>
                                    {{ list.TotalFee }}
                                </td>
                                <td>
                                    {{ list.DiscountAmount }}
                                </td>
                                 <td>
                                    {{ list.BalanceFeeAfterDiscount }}
                                </td>
                                <td>
                                     <div class="action_btn">
                                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('discount.edit') > -1" 
                                            :href="route('discount.edit',{id:list.id})" method="get" type="button" 
                                            class="btn btn-info btn-sm" title="Detail"><i class="fa fa-edit"></i></Link>

                                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('discount.delete') > -1" :href="route('discount.delete',{id:list.id})" method="delete" type="button" 
                                            class="btn btn-danger btn-sm" title="Edit"><i class="fa fa-trash"></i></Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="studentFeeDiscount.links" />
            </div>
        </div>
        
    </AuthenticatedLayout>
</template>