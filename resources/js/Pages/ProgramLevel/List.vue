<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link,Head,router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import {ref, watch} from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    programLevels: Object,
    programs: Array,
});

const columns = [
    { label: 'ID' },
    { label: 'Program' },
    { label: 'Title' },
    { label: 'Status' },
    { label: 'Action' },
];

const search = ref('');
watch(search, debounce((value) => {
  router.get(route('programlevel.index'), { search: value }, {
    preserveState: true,
    replace: true,
  });
}, 800));

</script>
<template>

    <Head title="Program Level" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Program Level
            </h2>
        </template>
  
        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Program Levels</div>

                    <div class="d-flex align-items-center gap-2">
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            v-model="search"
                            placeholder="Search By Title"
                            style="width: 250px;"
                        />

                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('programlevel.create') > -1" :href="route('programlevel.create')" 
                            class="btn btn-success btn-sm"
                            title="Create New Program Level"
                            data-tooltip="Create New Program Level">
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
                            <tr v-for="list,index in programLevels.data">
                                <td>
                                    {{ (programLevels.current_page - 1) * programLevels.per_page + index + 1 }}
                                </td>
                                <td>
                                    {{ programs.find(p => p.id === list.programm_id)?.name || 'N/A' }}
                                </td>
                                <td>
                                    {{ list.title }}
                                </td>
                                <td>
                                    <span style="color: #fff;" class="badge" :class="list.status ? 'bg-success' : 'bg-secondary'">{{ list.status ? 'Active' : 'Inactive' }}</span>
                                </td>
                                
                                <td>
                                    <div class="action_btn">
                                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('programlevel.edit') > -1" :href="route('programlevel.edit',{id:list.id})" method="get" type="button" 
                                            class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></Link>
                                        <button v-if="$page.props.auth.user.user_permissions.indexOf('programlevel.delete') > -1" @click="deleteProgramLevel(list.id)" 
                                            class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        
                    </table>
                </div>
                <Pagination :links="programLevels.links" />
            </div>
        </div>
        
    </AuthenticatedLayout>
</template>

<script>
import { router } from '@inertiajs/vue3';

export default {
    methods: {
        deleteProgramLevel(id) {
            if (confirm('Are you sure you want to delete this program level?')) {
                router.delete(route('programlevel.delete', { id }), {
                    preserveState: true,
                    replace: true,
                });
            }
        }
    }
}
</script>

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