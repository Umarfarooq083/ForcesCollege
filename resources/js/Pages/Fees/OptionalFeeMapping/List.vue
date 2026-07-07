<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link,Head, router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import {ref, watch} from 'vue';
import debounce from 'lodash/debounce';
const props = defineProps({
    optionalFeeMaster: Object,
});


const columns = [
    { label: 'Class' },
    { label: 'Section' },
    { label: 'Student' },
    { label: 'Fees Type' },
    { label: 'Campus Fee Master' },
    { label: 'From Month' },
    { label: 'To Month' },
    { label: 'Action' },
];

const search = ref('');
watch(search, debounce((value) => {
  router.get(route('optionalfee.list'), { search: value }, {
    preserveState: true,
    replace: true,
  });
}, 800));

</script>
<template>

    <Head title="Optional Fees Mapping" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Optional Fees Mapping
            </h2>
        </template>
       
        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Optional Fees Mapping</div>

                    <div class="d-flex align-items-center gap-2">
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            v-model="search"
                            placeholder="Search By Class, Section, Student and Type Name ..."
                            style="width: 350px;"
                        />

                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('optionalfee.create') > -1"  :href="route('optionalfee.create')"
                            class="btn btn-success btn-sm"
                            title="Create New Fee Option Master"
                            data-tooltip="Create New Fee Optional">
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
                           <tr v-for="feeMaster in optionalFeeMaster.data">
                                <td>
                                    {{ feeMaster?.class_rel?.ClassName }}
                                </td>
                                <td>
                                    {{ feeMaster?.section_rel?.SectionName }}
                                </td>
                                <td>
                                    {{ feeMaster?.student_rel?.FirstName }} {{ feeMaster?.student_rel?.LastName }}
                                </td>
                                <td>
                                   {{ feeMaster?.fee_type_rel?.FeeName }}
                                </td>
                                <td>
                                    {{ feeMaster?.campus_master_rel?.Amount }}
                                </td>
                                <td>
                                   {{ feeMaster?.FromMonth }}
                                </td>
                                <td>
                                    {{ feeMaster?.ToMonth }}
                                </td>
                                <td>
                                    <Link :href="route('optionalfee.destroy', {'optional_fee_id': feeMaster.id})"  method="delete" as="button" type="button" class="btn btn-danger btn-sm" title="Delete"><i class="fa fa-trash"></i></Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="optionalFeeMaster.links" />
            </div>
        </div>
        
    </AuthenticatedLayout>
</template>