<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link,Head,router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import {ref, watch} from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    examTerms: Object,
});


const columns = [
    { label: 'Name' },
    { label: 'Action' },
];

const search = ref('');
watch(search, debounce((value) => {
  router.get(route('examterm.index'), { search: value }, {
    preserveState: true,
    replace: true,
  });
}, 800));

</script>
<template>

    <Head title="Exam Term" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Exam Term
            </h2>
        </template>
        
        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Exam Term</div>

                    <div class="d-flex align-items-center gap-2">
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            v-model="search"
                            placeholder="Search By Exam Term Name"
                            style="width: 220px;"
                        />

                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('examterm.create') > -1" :href="route('examterm.create')" class="btn btn-success btn-sm" title="Create New Exam Term"
                            data-tooltip="Create New Exam Term">
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
                           <tr v-for="examTerm in examTerms.data" :key="examTerm.id">
                                <td>
                                    {{ examTerm.ExamTermName }}
                                </td>
                                
                                <td>
                                    <Link v-if="$page.props.auth.user.user_permissions.indexOf('examterm.edit') > -1" :href="route('examterm.edit',{id:examTerm.id})" method="get" type="button" 
                                            class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="examTerms.links" />
            </div>
        </div>
        
    </AuthenticatedLayout>
</template>