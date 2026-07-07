<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, Head, router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import { ref, watch } from 'vue';

const props = defineProps({
    contentUpload: Object,
    groups: Array,
    selectedGroup: [String, Number], 
});

const columns = [
    { label: 'Title' },
    { label: 'Type' },
    { label: 'Class' },
    { label: 'Subject' },
    { label: 'Group' },
    { label: 'Action' },
];

const activeTab = ref(props.selectedGroup || null);

watch(activeTab, (newGroup) => {
    router.get(route('uploads.specialistlist'), {
        group: newGroup
    }, {
        preserveState: true,
        replace: true,
        only: ['contentUpload', 'selectedGroup']
    });
});

// const setActive = (groupId) => {
//     activeTab.value = activeTab.value === groupId ? null : groupId;
// };

const setActive = (groupId) => {
    if (activeTab.value === groupId) {
        return; 
    }
    activeTab.value = groupId;
};



if (props.selectedGroup) {
    activeTab.value = props.selectedGroup;
}
</script>

<template>

    <Head title="Upload Content" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Upload Content
            </h2>
        </template>

        <div class="row">
            <div class="col-md-2">
                <div class="card">
                    <div class="header">
                        <div class="w-100 d-inline-flex justify-content-between align-items-center">
                            <div class="font-weight-bold">Groups</div>
                            <!-- <Link v-if="$page.props.auth.user.user_permissions.indexOf('content.create') > -1"
                                :href="route('content.create')" class="btn btn-success btn-sm" title="Create New User"
                                data-tooltip="Create New User">
                            <i class="fa fa-plus"></i>
                            </Link> -->
                        </div>
                    </div>
                    <div class="body p-0">
                        <div class="tab_container">
                            <!-- Show All Groups option -->
                            <a class="tab_content" :class="{ active: activeTab === null }" @click="setActive(null)">
                                <span>All Groups</span>
                            </a>

                            <!-- Dynamic groups from backend -->
                            <a v-for="group in groups" :key="group.id" class="tab_content"
                                :class="{ active: activeTab === group.id }" @click="setActive(group.id)">
                                <span>{{ group.name }}</span>
                                <Link v-if="$page.props.auth.user.user_permissions.indexOf('content.edit') > -1"
                                    :href="route('content.edit',{id:group.id})" method="get"  class="text-dark cstm_link"
                                    title="Edit Content Group" @click.stop>
                                <i class="fa fa-pencil"></i>
                                </Link>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-10">
                <div class="card">
                    <div class="header">
                        <div class="w-100 d-inline-flex justify-content-between align-items-center">
                            <div class="font-weight-bold">Upload Content</div>
                            <div>
                                <Link v-if="$page.props.auth.user.user_permissions.indexOf('uploads.create') > -1"
                                    :href="route('uploads.create')" class="btn btn-success btn-sm"
                                    title="Create New User" data-tooltip="Create New User">
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
                                    <tr v-if="contentUpload.data.length === 0">
                                        <td :colspan="columns.length" class="text-center py-4">
                                            No content found {{ activeTab ? 'for the selected group' : '' }}.
                                        </td>
                                    </tr>
                                    <tr v-for="content in contentUpload.data" :key="content.id">
                                        <td>
                                            {{ content.ContentTitle }}
                                        </td>
                                        <td>
                                            {{ content.ContentType }}
                                        </td>
                                        <td>
                                            {{ content?.classes?.ClassName }}
                                        </td>
                                        <td>
                                            {{ content?.subjects?.SubjectName }}
                                        </td>
                                        <td>
                                            {{ content?.content_group?.name }}
                                        </td>
                                        <td>
                                            <div class="action_btn" v-if="content.IsActive == 0">
                                                <a href="#" class="btn btn-info btn-sm">
                                                    <i class="fa fa-upload"></i> Uploading
                                                </a>
                                            </div>
                                            <div class="action_btn" v-else>
                                                <Link
                                                    v-if="$page.props.auth.user.user_permissions.indexOf('uploads.edit') > -1"
                                                    :href="route('uploads.edit', { id: content.id })"
                                                    method="get"
                                                    class="btn btn-info btn-sm"
                                                    title="Edit"
                                                >
                                                    <i class="fa fa-edit"></i>
                                                </Link>
                                                <Link
                                                    v-if="$page.props.auth.user.user_permissions.indexOf('uploads.delete') > -1"
                                                    :href="route('uploads.delete', { id: content.id })" method="delete"
                                                    type="button" class="btn btn-danger btn-sm" title="Delete">
                                                <i class="fa fa-trash"></i>
                                                </Link>
                                                <a v-if="$page.props.auth.user.user_permissions.indexOf('uploads.download') > -1"
                                                    :href="route('uploads.download', { id: content.id })"
                                                    class="btn btn-info btn-sm" title="Download">
                                                    <i class="fa fa-download"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <Pagination :links="contentUpload.links" />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
.tab_content {
    width: 100%;
    display: flex !important;
    justify-content: space-between;
    align-items: center;
    position: relative;
    padding: 15px;
    border-bottom: 1px solid var(--border-color);
    cursor: pointer;
}

.tab_content span {
    flex: 1;
}

.tab-btn {
    margin-left: auto;
    z-index: 2;
}

/* Prevent button click from triggering tab click */
.tab_content .tab-btn {
    pointer-events: auto;
}

.tab_content:last-child {
    border: 0;
}

.tab_content:hover {
    background: var(--primary-color);
    color: var(--white) !important;
}
.tab_content:hover .cstm_link{
    color: var(--white) !important;
}

.tab_content.active {
    background: var(--primary-color);
    color: var(--white);
}

.tab_container {
    width: 100%;
    display: inline-block;
    max-height: 1535px;
    overflow: auto;
    overflow-x: hidden;
}

.tab_content {
    width: 100%;
    display: inline-block;
    position: relative;
    padding: 15px;
    border-bottom: 1px solid var(--border-color);
    cursor: pointer;
}

.tab_content:last-child {
    border: 0;
}

.tab_content:hover {
    background: var(--primary-color);
    color: var(--white) !important;
}

.tab_content.active {
    background: var(--primary-color);
    color: var(--white);
}

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

.badge-info {
    background-color: #17a2b8;
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
}
</style>
