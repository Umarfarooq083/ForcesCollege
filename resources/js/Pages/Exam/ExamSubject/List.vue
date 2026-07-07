<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link,Head,router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import {ref, watch} from 'vue';
import debounce from 'lodash/debounce';
import { useToast } from "vue-toastification";
const toast = useToast();

const props = defineProps({
    ExamSubject: Object,
    errors: Object,
});


const columns = [
    { label: 'Title' },
    { label: 'Exam' },
    { label: 'Class' },
    { label: 'Subject' },
    { label: 'Date' },
    { label: 'Time' },
    { label: 'Duration (Min)' },
    { label: 'Room No' },
    { label: 'Action' },
];

const search = ref('');
watch(search, debounce((value) => {
  router.get(route('examsubject.index'), { search: value }, {
    preserveState: true,
    replace: true,
  });
}, 800));

// alert(props.errors.delete_error);

// ✅ check errors prop and show toast
// if (props.errors?.delete_error) {
//     alert('adfja');
//     toast.error(props.errors.delete_error, {
//         timeout: 5000,
//         position: "top-right"
//     });
// }

watch(
    () => props.errors?.delete_error,
    (val) => {
        if (val) {
            toast.error(val, {
                timeout: 5000,
                position: "top-right",
            });
        }
    },
    { immediate: true }
);
// const deleteExamSubject = (id) => {
//     if (!confirm('Are you sure you want to delete this subject?')) return;

//     router.delete(route('examsubject.delete'), { id: id }, {
//         preserveScroll: true,
//         onSuccess: () => {
//             toast.success('Exam subject deleted successfully.', {
//                 position: 'top-right'
//             });
//         },
//         onError: (errors) => {
//             if (errors.delete_error) {
//                 toast.error(errors.delete_error[0], {
//                     position: 'top-right'
//                 });
//             }
//         }
//     });
// }

</script>

<template>

    <Head title="Exam Subject" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Exam Subject   
            </h2>
        </template>

        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Exam Subject</div>
                    <div class="d-flex align-items-center gap-2">
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            v-model="search"
                            placeholder="Search By Title, Exam, Class & Subject Name"
                            style="width: 320px;"
                        />

                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('examsubject.create') > -1" :href="route('examsubject.create')" class="btn btn-success btn-sm" title="Create New Exam Subject"
                            data-tooltip="New Exam Subject">
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
                           <tr v-for="examsubject in ExamSubject.data"
                                :key="examsubject.id">
                                <td>{{ examsubject.Title }}</td>
                                <td>{{ examsubject?.exam_type?.ExamName }}</td>
                                <td>{{ examsubject.subject.classes.ClassName }}</td>
                                <td>{{ examsubject.subject.SubjectName }}</td>
                                <td>{{ examsubject.Date }}</td>
                                <td>{{ examsubject.Time }}</td>
                                <td>{{ examsubject.Duration }}</td>
                                <td>{{ examsubject.RoomNo }}</td>
                                <td>
                                    <Link v-if="$page.props.auth.user.user_permissions.indexOf('examsubject.edit') > -1" :href="route('examsubject.edit',{id:examsubject.id})" method="get" type="button"
                                        class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></Link>

                                    <Link @click="deleteExamSubject(examsubject.id)" v-if="$page.props.auth.user.user_permissions.indexOf('examsubject.delete') > -1" :href="route('examsubject.delete',{id:examsubject.id})" method="delete" type="button" 
                                        class="btn btn-danger btn-sm" title="Edit"><i class="fa fa-trash"></i></Link>
                                
                                    <!-- <button @click="deleteExamSubject(examsubject.id)" 
                                            v-if="$page.props.auth.user.user_permissions.indexOf('examsubject.delete') > -1"
                                            class="btn btn-danger btn-sm" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button> -->
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="ExamSubject.links" />
            </div>
        </div>
        
    </AuthenticatedLayout>
</template>