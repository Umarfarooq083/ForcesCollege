<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link,Head,router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import {ref, watch} from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    programs: Object,
});

const columns = [
    { label: 'ID' },
    { label: 'Program Name' },
    { label: 'Type' },
    { label: 'Duration' },
    { label: 'Action' },
];

const search = ref('');
watch(search, debounce((value) => {
  router.get(route('program.index'), { search: value }, {
    preserveState: true,
    replace: true,
  });
}, 800));

</script>
<template>

    <Head title="Program" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Program
            </h2>
        </template>
  
        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Program</div>

                    <div class="d-flex align-items-center gap-2">
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            v-model="search"
                            placeholder="Search By Program Name"
                            style="width: 250px;"
                        />

                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('program.create') > -1" :href="route('program.create')" 
                            class="btn btn-success btn-sm"
                            title="Create New Program"
                            data-tooltip="Create New Program">
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
                            <tr v-for="list,index in programs.data">
                                <td>
                                    {{ (programs.current_page - 1) * programs.per_page + index + 1 }}
                                </td>
                                <td>
                                    {{ list.name }}
                                </td>
                                <td>
                                    {{ list.type }}
                                </td>
                                <td>
                                    {{ list.duration }}
                                </td>
                                
                                <td>
                                    <div class="action_btn">
                                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('program.edit') > -1" :href="route('program.edit',{id:list.id})" method="get" type="button" 
                                            class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        
                    </table>
                </div>
                <Pagination :links="programs.links" />
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