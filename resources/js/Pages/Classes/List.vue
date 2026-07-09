<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link,Head,router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import {ref, watch} from 'vue';
import debounce from 'lodash/debounce';
import Pagination from '@/Components/Pagination.vue';


const props = defineProps({
    classes: Object,
});

const toggleStatus = (classlist) => {
    console.log(classlist.id)
    router.put(route('class.status', classlist.id), {
        status: !classlist.IsActive
    }, {
        preserveScroll: true,
    });
};

const columns = [
    { label: 'ID' },
    { label: 'Class Name' },
    { label: 'Program' },
    { label: 'Class Order' },
    { label: 'Created By' },
    { label: 'Status' },
    { label: 'Action' },
];

const search = ref('');
watch(search, debounce((value) => {
  router.get(route('class.index'), { search: value }, {
    preserveState: true,
    replace: true,
  });
}, 800));

</script>
<template>

    <Head title="Classes" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Classes
            </h2>
        </template>

        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Classes</div>


                    <div class="d-flex align-items-center gap-2">
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            v-model="search"
                            placeholder="Search By Class Name, Class Type & Created By"
                            style="width: 350px;"
                        />


                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('class.create') > -1" :href="route('class.create')" 
                            class="btn btn-success btn-sm" 
                            title="Create New Class"
                            data-tooltip="Create New Class">
                            <i class="fa fa-plus"></i>
                        </Link>
                    </div>

                   
                </div>
            </div>
         
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 c_list table-bordered">
                        <TableHeader :columns="columns" />
                        <!-- {{ classes.data }} -->
                        <tbody>
<tr v-for="classlist ,index in classes.data" >
                                <td>
                                    {{ (classes.current_page - 1) * classes.per_page + index + 1 }}
                                </td>
 <td>
                                    {{ classlist.ClassName }}
                                </td>
                                <td>
                                    {{ classlist?.program?.name || '-' }}
                                </td>
                                   <td>
                                    {{ classlist?.ClassOrder }}
                                </td>
                                <td>
                                    {{ classlist?.user?.name }}
                                </td>
                                <td>
                                    <span v-if="classlist.IsActive == 1">
                                        Active
                                    </span>
                                        <span v-else>
                                         InActive
                                    </span>
                                </td>
                               
                                <td>
                                    <div class="action_btn">
                                       <div class="custom-control custom-switch">
                                        <input
                                            type="checkbox"
                                            class="custom-control-input"
                                            :id="'switch-' + classlist.id"
                                            :checked="classlist.IsActive"
                                            @change="toggleStatus(classlist)"
                                            >
                                            <label class="custom-control-label cstm_switch" :for="'switch-' + classlist.id"></label>
                                        </div>
                                    </div>

                                    <div class="action_btn">
                                        <Link :href="route('class.edit', { id: classlist.id })" method="get" type="button"
                                            class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></Link>
                                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('class.delete') > -1" :href="route('class.delete', { id: classlist.id })" method="delete"  class="btn btn-danger btn-sm"
                                        title="Delete"><i class="fa fa-trash-o"></i></Link>
                                    </div>

                                </td>
                            </tr>
                        </tbody>
                        
                    </table>
                </div>
                <Pagination :links="classes.links" />
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