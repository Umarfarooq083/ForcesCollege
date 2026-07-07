<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link,Head,router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import {ref, watch} from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    ExamData: Array,
});


const columns = [
    { label: 'Exam Term' },
    { label: 'Exam Type' },
    { label: 'Subject' },
    { label: 'Class' },
    { label: 'Action' },
];

const search = ref('');
watch(search, debounce((value) => {
  router.get(route('marks.index'), { search: value }, {
    preserveState: true,
    replace: true,
  });
}, 800));
</script>
<template>

    <Head title="Exam Subject" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Exam Marks   
            </h2>
        </template>

        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Exam Marks</div>
                    <div class="d-flex align-items-center gap-2">
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            v-model="search"
                            placeholder="Search By Exam Term, Type, Subject & Class Name"
                            style="width: 350px;"
                        />

                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('marks.create') > -1" :href="route('marks.create')" class="btn btn-success btn-sm" title="Create New Exam Marks"
                            data-tooltip="New Exam Marks">
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
                           <tr v-for="exam in ExamData.data" :key="exam.id">
                               <td>{{ exam?.exam_subject?.exam_type?.exam_term?.ExamTermName }}</td>
                                <td>{{ exam?.exam_subject?.exam_type?.ExamName }}</td>
                                <td>{{ exam?.exam_subject?.subject?.SubjectName }}</td>
                                <td>{{ exam?.exam_subject?.class?.ClassName }}</td>
                                <td class="d-flex gap-1 justify-content-center">
                                    <Link v-if="$page.props.auth.user.user_permissions.indexOf('marks.show') > -1" method="get" :href="route('marks.show',{id:exam.id, ClassId:exam?.exam_subject?.class?.id})" type="button" class="btn btn-info btn-sm" title="Detail"><i class="fa fa-info-circle"></i></Link>
                                    <Link v-if="$page.props.auth.user.user_permissions.indexOf('marks.edit') > -1" method="get" :href="route('marks.edit',{id:exam.id, ClassId:exam?.exam_subject?.class?.id})" type="button" class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></Link>
                                    <Link v-if="$page.props.auth.user.user_permissions.indexOf('marks.delete') > -1" :href="route('marks.delete',{id:exam.id})" method="delete" type="button" class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash"></i></Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="ExamData.links" />
            </div>
        </div>
        
    </AuthenticatedLayout>
</template>