<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, Head, router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    homework_list: Object,
});

const columns = [
    { label: 'Class' },
    { label: 'Section' },
    { label: 'Subject' },
    { label: 'Homework Date' },
    { label: 'Submission Date' },
    // { label: 'Group' },
    { label: 'Actions' },
];

// 🔍 Optional Search (You can remove if not needed)
const search = ref('');
watch(search, debounce((value) => {
    router.get(route('homework.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
}, 800));
</script>

<template>
    <Head title="Homework List" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Homework List
            </h2>
        </template>

        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Homework List</div>

                    <div class="d-flex align-items-center gap-2">
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            v-model="search"
                            placeholder="Search by Class, Section or Subject"
                            style="width: 295px;"
                        />

                        <Link
                            v-if="$page.props.auth.user.user_permissions.indexOf('homework.create') > -1"
                            :href="route('homework.create')"
                            class="btn btn-success btn-sm"
                            title="Create Homework"
                        >
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
                            <tr v-for="hw in homework_list.data" :key="hw.id">
                                <td>{{ hw?.class_rel?.ClassName }}</td>
                                <td>{{ hw?.section_rel?.SectionName }}</td>
                                <td>{{ hw?.subject_rel?.SubjectName }}</td>
                                <td>{{ hw.homeworkDate ? new Date(hw.homeworkDate).toLocaleDateString() : '-' }}</td>
                                <td>{{ hw.submissionDate ? new Date(hw.submissionDate).toLocaleDateString() : '-' }}</td>
                                <!-- <td>{{ hw.homeworkGroup ?? '-' }}</td> -->
                                <td>
                                    <div class="action_btn">
                                        <Link
                                            v-if="$page.props.auth.user.user_permissions.indexOf('homework.edit') > -1"
                                            :href="route('homework.edit', { id: hw.id })"
                                            class="btn btn-info btn-sm"
                                            title="Edit"
                                        >
                                            <i class="fa fa-edit"></i>
                                        </Link>

                                        <Link
                                            v-if="$page.props.auth.user.user_permissions.indexOf('homework.delete') > -1"
                                            :href="route('homework.delete', { id: hw.id })"
                                            method="delete"
                                            class="btn btn-danger btn-sm"
                                            title="Delete"
                                        >
                                            <i class="fa fa-trash"></i>
                                        </Link>

                                        <Link
                                            v-if="$page.props.auth.user.user_permissions.indexOf('homework.show') > -1"
                                            :href="route('homework.show', { id: hw.id })"
                                            method="get"
                                            class="btn btn-secondary btn-sm"
                                            title="View Details"
                                        >
                                            <i class="fa fa-eye"></i>
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <Pagination :links="homework_list.links" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
