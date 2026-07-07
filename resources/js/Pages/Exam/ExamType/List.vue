<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link,Head,router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import {ref, watch} from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    examTypes: Object,
});


const columns = [
    { label: 'Exam Name' },
    { label: 'Session' },
    { label: 'Exam Term' },
    { label: 'Date' },
    { label: 'Action' },
];

const search = ref('');
watch(search, debounce((value) => {
  router.get(route('examtype.index'), { search: value }, {
    preserveState: true,
    replace: true,
  });
}, 800));
</script>
<template>

    <Head title="Exam" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Exam   
            </h2>
        </template>

        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Exam</div>
                    <div class="d-flex align-items-center gap-2">
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            v-model="search"
                            placeholder="Search By Exam Name & Exam Term"
                            style="width: 320px;"
                        />

                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('examtype.create') > -1" :href="route('examtype.create')" class="btn btn-success btn-sm" title="Create New Exam"
                            data-tooltip="New Exam">
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
                           <tr v-for="value in examTypes.data" :key="value.id">
                                <td>{{ value.ExamName }}</td>
                                <td>{{ value.session_rel.start_date +' - '+ value.session_rel.end_date }}</td>
                                <td>{{ value.exam_term?.ExamTermName }}</td>
                                <td>{{ value.ResultDeclarationDate }}</td>
                                <td>
                                    <Link v-if="$page.props.auth.user.user_permissions.indexOf('examtype.edit') > -1" :href="route('examtype.edit',{id:value.id})" method="get" type="button" 
                                            class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="examTypes.links" />
            </div>
        </div>
        
    </AuthenticatedLayout>
</template>