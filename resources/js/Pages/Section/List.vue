<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link,Head,router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import {ref, watch} from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    classesList: Object,
    sections: Object
});

const columns = [
    { label: 'Sr. No' },
    { label: 'Section Name' },
    { label: 'Class' },
    { label: 'Strength' },
    { label: 'Created By' },
    { label: 'Status' },
    { label: 'Action' },
];

const search = ref('');
watch(search, debounce((value) => {
  router.get(route('section.index'), { search: value }, {
    preserveState: true,
    replace: true,
  });
}, 800));
</script>
<template>

    <Head title="Section" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Section
            </h2>
        </template>
        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Section</div>

                    <div class="d-flex align-items-center gap-2">
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            v-model="search"
                            placeholder="Search By Section, Class & User Name"
                            style="width: 280px;"
                        />

                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('section.create') > -1" :href="route('section.create')" 
                            class="btn btn-success btn-sm" 
                            title="Create New Class"
                            data-tooltip="Create New Section">
                            <i class="fa fa-plus"></i>
                        </Link>
                    </div>
                </div>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table mb-0 table-hover c_list table-bordered">
                        <TableHeader :columns="columns" />
                        <tbody>
                           <tr v-for="(section, index) in sections.data" :key="section.id">
                                <td>
                                    {{ (sections.current_page - 1) * sections.per_page + index + 1 }}
                                </td>
                                <td>{{ section.SectionName }}</td>
                                <td>{{ section.class?.ClassName ?? '—' }}</td>
                                <td>{{ section.Strength }}</td>
                                <td class="text-capitalize">{{ section.user?.name }}</td>
                                <td>
                                    <span class="badge" :class="section.IsActive ? 'badge-success' : 'badge-danger'">
                                        {{ section.IsActive ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <!-- {{ section }} -->
                                <td>
                                    <div class="action_btn">
                                         <Link v-if="$page.props.auth.user.user_permissions.indexOf('section.edit') > -1" :href="route('section.edit',{id:section?.id})" method="get" type="button" 
                                            class="btn btn-info btn-sm" title="Edit"><i class="fa fa-edit"></i></Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="sections.links" />
            </div>
        </div>
        
    </AuthenticatedLayout>
</template>