<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link,Head,router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import debounce from 'lodash/debounce';
import {ref, watch} from 'vue';

const props = defineProps({
    ExamStudents: Object,
});

const search = ref('');
watch(search, debounce((value) => {
  router.get(route('examstudent.index'), { search: value }, {
    preserveState: true,
    replace: true,
  });
}, 800));


const columns = [
    { label: 'Exam' },
    { label: 'Exam Subject' },
    { label: 'Student' },
    { label: 'Class' },
    { label: 'Date' },
    { label: 'Time' },
    { label: 'Duration (Min)' },
    { label: 'Room No' },
    { label: 'Action' },
];
</script>
<template>

    <Head title="Exam Student" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Exam Student   
            </h2>
        </template>

        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Exam Student</div>
                    <div class="d-flex align-items-center gap-2">
                     <input
                            type="text"
                            class="form-control form-control-sm"
                            v-model="search"
                            placeholder="Search By Exam, Class ,Student Name & Exam Subject"
                            style="width: 360px;"
                        />
                    
                    <Link v-if="$page.props.auth.user.user_permissions.indexOf('examstudent.create') > -1" :href="route('examstudent.create')" class="btn btn-success btn-sm" title="Create New Exam Student"
                        data-tooltip="New Exam Student">
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
                            
                           <tr v-for="examStudent in ExamStudents.data"
                                :key="examStudent.id">
                                <td>{{ examStudent?.subject?.exam_type?.ExamName }}</td>
                                <td>{{ examStudent?.subject?.Title }} - {{ examStudent?.subject?.subject?.SubjectName }}</td>
                                <td>{{ examStudent?.student?.FirstName }} {{ examStudent?.student?.LastName }}</td>
                                <td>{{ examStudent?.subject?.class?.ClassName }}</td>
                                <td>{{ examStudent?.subject?.Date }}</td>
                                <td>{{ examStudent?.subject?.Time }}</td>
                                <td>{{ examStudent?.subject?.Duration }}</td>
                                <td>{{ examStudent?.subject?.RoomNo }}</td>
                                <td>
                                    <!-- <Link :href="route('examstudent.edit',{id:examStudent.id})" method="get" type="button"
                                        class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></Link> -->

                                <Link v-if="$page.props.auth.user.user_permissions.indexOf('examstudent.delete') > -1" :href="route('examstudent.delete',{id:examStudent.id})" method="delete" type="button" 
                                    class="btn btn-danger btn-sm" title="Edit"><i class="fa fa-trash"></i></Link>


                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="ExamStudents.links" />
            </div>
        </div>
        
    </AuthenticatedLayout>
</template>