<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link,Head,router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import {ref, watch} from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    grades: Object,
});


const columns = [
    { label: 'Name' },
    { label: 'Class' },
    { label: 'Percent From' },
    { label: 'Percent Upt' },
    { label: 'Action' },
];

const search = ref('');
watch(search, debounce((value) => {
  router.get(route('marksgrade.list'), { search: value }, {
    preserveState: true,
    replace: true,
  });
}, 800));
</script>
<template>

    <Head title="Exam Grade" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Exam Grade   
            </h2>
        </template>

        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Exam Grade</div>
                    <div class="d-flex align-items-center gap-2">
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            v-model="search"
                            placeholder="Search By Grade Name & Class"
                            style="width: 250px;"
                        />

                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('marksgrade.create') > -1" :href="route('marksgrade.create')" class="btn btn-success btn-sm" title="Create New Exam"
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
                           <tr v-for="value in grades.data" :key="value.id">
                                <td>{{ value.GradeName }}</td>
                                <td>{{ value.class_rel.ClassName }}</td>
                                <td>{{ value.PercentFrom }}</td>
                                <td>{{ value.PercentUpt }}</td>
                                <td>
                                    <div class="action_btn">
                                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('marksgrade.edit') > -1" :href="route('marksgrade.edit',{id:value.id})" method="get" type="button" 
                                                class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></Link>
                                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('marksgrade.delete') > -1" :href="route('marksgrade.delete',{id:value.id})" method="delete" type="button" class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash"></i></Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="grades.links" />
            </div>
        </div>
        
    </AuthenticatedLayout>
</template>