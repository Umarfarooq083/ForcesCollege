<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link,Head,router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import {ref, watch} from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    subject: Object,
});

const columns = [
    { label: 'ID' },
    { label: 'Subject Name' },
    { label: 'Class Name' },
    { label: 'Program' },
    { label: 'Program Level' },
    { label: 'Action' },
];

const search = ref('');
watch(search, debounce((value) => {
  router.get(route('subject.index'), { search: value }, {
    preserveState: true,
    replace: true,
  });
}, 800));

</script>
<template>

    <Head title="Subject" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Subject
            </h2>
        </template>
      
        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Subject</div>

                    <div class="d-flex align-items-center gap-2">
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            v-model="search"
                            placeholder="Search By Subject & Class Name"
                            style="width: 250px;"
                        />

                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('subject.create') > -1" :href="route('subject.create')" 
                            class="btn btn-success btn-sm"
                            title="Create New Subject"
                            data-tooltip="Create New Subject">
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
                            <tr v-for="list,index in subject.data">
                                <td>
                                    {{ (subject.current_page - 1) * subject.per_page + index + 1 }}
                                </td>
                                <td>
                                    {{ list.SubjectName }}
                                </td>
                                <td>
                                    {{ list.classes.ClassName }}
                                </td>
                                <td>
                                 {{ list?.classes?.program?.name}}
                               </td>
                                <td>
                                  {{ list?.program_level?.title }}
                                </td>
                                <td>
                                    <div class="action_btn">
                                         <Link v-if="$page.props.auth.user.user_permissions.indexOf('subject.edit') > -1" :href="route('subject.edit',{id:list.id})" method="get" type="button" 
                                            class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        
                    </table>
                </div>
                <Pagination :links="subject.links" />
            </div>
        </div>
        
    </AuthenticatedLayout>
</template>
<style>
.roles_class {
    background-color: green;
    padding: 5px 4px 5px 5px;
    border-radius: 20px;
    color: #fff;
}

.roles_not_selected {
    background-color: #e23636d4;
    padding: 5px 4px 5px 5px;
    border-radius: 20px;
    color: #fff;
}
</style>