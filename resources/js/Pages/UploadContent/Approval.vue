<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link, Head, router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import { ref, watch, computed } from 'vue';
import debounce from 'lodash/debounce';


const props = defineProps({
    contentUpload: Object,
    groups: Array,
    selectedGroup: [String, Number],
    Campuses: Array,
    classesList: Array,
});

const columns = [
    { label: 'Title' },
    { label: 'Type' },
    { label: 'Class' },
    { label: 'Subject' },
    { label: 'Group' },
    { label: 'Status' },
    { label: 'Action' },
];

const contentTypes = [
    'Assignments','Study Material','Syllabus','DLP','Exam Paper','Datesheet','Wlps',
    'Scheme of Studies','Vacation Work','Checkpoint','Worksheet','Timetable','Period Allocation',
    'Calendars','Circular','Other',
];

const activeTab = ref(props.selectedGroup || null);

watch(activeTab, (newGroup) => {
    router.get(route('uploads.approval'), {
        group: newGroup
    }, {
        preserveState: true,
        replace: true,
        only: ['contentUpload', 'selectedGroup']
    });
});

const setActive = (groupId) => {
    if (activeTab.value === groupId) {
        return; 
    }
    activeTab.value = groupId;
};


if (props.selectedGroup) {
    activeTab.value = props.selectedGroup;
}

const search = ref('');
const contentType = ref('');
const selectedClass = ref('');
const selectedSubject = ref('');
const selectedCampus = ref('');
const dateFrom = ref('');
const dateTo = ref('');
const perPage = ref(props.contentUpload?.per_page || 25);

const subjectsList = ref([]);

function fetchSubjects() {
    if (!selectedClass.value) {
        subjectsList.value = [];
        selectedSubject.value = '';
        return;
    }
    axios.get(route('class.subject.fetch'), { params: { class_id: selectedClass.value } })
        .then(res => {
            subjectsList.value = res.data;
            if (subjectsList.value.length === 0) selectedSubject.value = '';
        });
}

watch(selectedClass, fetchSubjects);

function clearFilters() {
    search.value = '';
    contentType.value = '';
    selectedClass.value = '';
    selectedSubject.value = '';
    selectedCampus.value = '';
    dateFrom.value = '';
    dateTo.value = '';
    perPage.value = 25;
    subjectsList.value = [];
    applyFilters();
}

const clearDates = () => {
    dateFrom.value = '';
    dateTo.value = '';
};

const hasActiveFilters = computed(() =>
    search.value || contentType.value || selectedClass.value || selectedSubject.value || selectedCampus.value || dateFrom.value || dateTo.value
);

const getCampusName = (tenantId) => {
    const campus = props.Campuses.find(c => c.tenant_id === tenantId);
    return campus ? campus.SchoolName : tenantId;
};

const formatDateRange = computed(() => {
    if (dateFrom.value && dateTo.value && dateFrom.value !== dateTo.value) {
        return `${dateFrom.value} - ${dateTo.value}`;
    } else if (dateFrom.value) {
        return dateFrom.value;
    } else if (dateTo.value) {
        return dateTo.value;
    }
    return '';
});

function autoAdjustRange() {
    if (dateFrom.value && !dateTo.value) {
        dateTo.value = dateFrom.value;
    }
    if (dateTo.value && !dateFrom.value) {
        dateFrom.value = dateTo.value;
    }
}

watch(dateTo, autoAdjustRange);
watch(dateFrom, autoAdjustRange);

const applyFilters = debounce(() => {
    router.get(route('uploads.approval'), {
        group: activeTab.value,
        title: search.value || undefined,
        content_type: contentType.value || undefined,
        class_id: selectedClass.value || undefined,
        subject_id: selectedSubject.value || undefined,
        tenant_id: selectedCampus.value || undefined,
        date_from: dateFrom.value || undefined,
        date_to: dateTo.value || undefined,
        per_page: perPage.value,
    }, {
        preserveState: true,
        replace: true,
        only: ['contentUpload', 'selectedGroup'],
    });
}, 500);

watch([search, contentType, selectedClass, selectedSubject, selectedCampus, dateFrom, dateTo], applyFilters);

watch(perPage, (val) => {
    router.get(route('uploads.approval'), {
        group: activeTab.value,
        title: search.value || undefined,
        content_type: contentType.value || undefined,
        class_id: selectedClass.value || undefined,
        subject_id: selectedSubject.value || undefined,
        tenant_id: selectedCampus.value || undefined,
        date_from: dateFrom.value || undefined,
        date_to: dateTo.value || undefined,
        per_page: val,
    }, {
        preserveState: true,
        replace: true,
        only: ['contentUpload', 'selectedGroup'],
    });
});
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
            <div style="margin-left: 15px;" v-if="$page.props.flash.receipt" class="alert alert-success">
                {{ $page.props.flash.receipt }}
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="header">
                        <div class="filters-container">
                            <div class="filters-header">
                                <div class="title-section">
                                    <h3 class="mb-0">Content Approval</h3>
                                    <span class="logs-count" v-if="contentUpload?.total">
                                        ({{ contentUpload.total }} entries)
                                    </span>
                                </div>

                                <button v-if="hasActiveFilters" type="button" class="clear-all-btn" @click="clearFilters">
                                    <i class="fa fa-eraser"></i> Clear all filters
                                </button>
                            </div>

                            <div class="filters-grid">
                                <!-- Search by title -->
                                <div class="filter-group">
                                    <label class="filter-label">
                                        <i class="fa fa-search"></i> Search
                                    </label>
                                    <div class="search-wrapper">
                                        <i class="fa fa-search search-icon"></i>
                                        <input type="text" class="filter-input search-input" v-model="search"
                                            placeholder="Search by title..." />
                                        <i v-if="search" class="fa fa-times-circle clear-icon" @click="search = ''"></i>
                                    </div>
                                </div>

                                <!-- Content type dropdown -->
                                <div class="filter-group">
                                    <label class="filter-label">
                                        <i class="fa fa-list-alt"></i> Type
                                    </label>
                                    <div class="select-wrapper">
                                        <select class="filter-select" v-model="contentType">
                                            <option value="">All Types</option>
                                            <option v-for="type in contentTypes" :key="type" :value="type">
                                                {{ type }}
                                            </option>
                                        </select>
                                        <i class="fa fa-chevron-down select-arrow"></i>
                                    </div>
                                </div>

                                <!-- Class dropdown -->
                                <div class="filter-group">
                                    <label class="filter-label">
                                        <i class="fa fa-graduation-cap"></i> Class
                                    </label>
                                    <div class="select-wrapper">
                                        <select class="filter-select" v-model="selectedClass">
                                            <option value="">All Classes</option>
                                            <option v-for="cls in classesList" :key="cls.id" :value="cls.id">
                                                {{ cls.ClassName }}
                                            </option>
                                        </select>
                                        <i class="fa fa-chevron-down select-arrow"></i>
                                    </div>
                                </div>

                                <!-- Subject dropdown -->
                                <!-- <div class="filter-group">
                                    <label class="filter-label">
                                        <i class="fa fa-book"></i> Subject
                                    </label>
                                    <div class="select-wrapper">
                                        <select class="filter-select" v-model="selectedSubject" :disabled="!selectedClass">
                                            <option value="">All Subjects</option>
                                            <option v-for="subj in subjectsList" :key="subj.id" :value="subj.id">
                                                {{ subj.SubjectName }}
                                            </option>
                                        </select>
                                        <i class="fa fa-chevron-down select-arrow"></i>
                                    </div>
                                </div> -->

                                <!-- Date range -->
                                <div class="filter-group">
                                    <label class="filter-label">
                                        <i class="fa fa-calendar"></i> Upload Date Range
                                    </label>
                                    <div class="date-range">
                                        <div class="date-wrapper">
                                            <input type="date" class="filter-input date-input" v-model="dateFrom" />
                                            <span class="date-separator">to</span>
                                            <input type="date" class="filter-input date-input" v-model="dateTo" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Active filters badges -->
                            <div class="active-filters" v-if="hasActiveFilters">
                                <span class="active-filters-label">Active filters:</span>
                                <div class="filter-badges">
                                    <div v-if="search" class="filter-badge">
                                        <span>Search: {{ search }}</span>
                                        <i class="fa fa-times" @click="search = ''"></i>
                                    </div>
                                    <div v-if="contentType" class="filter-badge">
                                        <span>Type: {{ contentType }}</span>
                                        <i class="fa fa-times" @click="contentType = ''"></i>
                                    </div>
                                    <div v-if="selectedClass" class="filter-badge">
                                        <span>Class: {{ classesList.find(c => c.id === selectedClass)?.ClassName }}</span>
                                        <i class="fa fa-times" @click="selectedClass = ''; selectedSubject = ''"></i>
                                    </div>
                                    <div v-if="selectedSubject" class="filter-badge">
                                        <span>Subject: {{ subjectsList.find(s => s.id === selectedSubject)?.SubjectName }}</span>
                                        <i class="fa fa-times" @click="selectedSubject = ''"></i>
                                    </div>
                                    <div v-if="selectedCampus" class="filter-badge">
                                        <span>Campus: {{ getCampusName(selectedCampus) }}</span>
                                        <i class="fa fa-times" @click="selectedCampus = ''"></i>
                                    </div>
                                    <div v-if="dateFrom || dateTo" class="filter-badge">
                                        <span>Upload Date: {{ formatDateRange }}</span>
                                        <i class="fa fa-times" @click="clearDates"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="body">
                        <div class="table-responsive">
                            <div class="filter-group-per-page">
                                <label class="filter-label">
                                    <i class="fa fa-list-ol"></i> Per Page
                                </label>
                                <div>
                                    <select class="filter-select-per-page" v-model="perPage">
                                        <option v-for="n in [25,50,100,150,200]" :key="n" :value="n">{{ n }}</option>
                                    </select>
                                    <i class="fa fa-chevron-down select-arrow"></i>
                                </div>
                            </div>

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
                                        <td style="color: #fff">
                                            <span
                                                :class="{
                                                    'badge bg-warning text-dark': content?.IsActive == 0,
                                                    'badge bg-dark': content?.IsActive == 1,
                                                    'badge bg-success': content?.IsActive == 2,
                                                    'badge bg-danger': content?.IsActive == 3,
                                                }"
                                            >
                                                {{
                                                    content?.IsActive == 0
                                                        ? 'Uploading'
                                                        : content?.IsActive == 1
                                                        ? 'Pending'
                                                        : content?.IsActive == 2
                                                        ? 'Approved'
                                                        : content?.IsActive == 3
                                                        ? 'Rejected'
                                                        : ''
                                                }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="action_btn" v-if="content.IsActive == 0">
                                                <a href="#" class="btn btn-info btn-sm">
                                                    <i class="fa fa-upload"></i> Uploading
                                                </a>
                                            </div>
                                            
                                            <div class="action_btn" v-else>
                                                <a v-if="$page.props.auth.user.user_permissions.indexOf('uploads.download') > -1"
                                                    :href="route('uploads.download', { id: content.id })"
                                                    class="btn btn-info btn-sm" title="Download">
                                                    <i class="fa fa-download"></i>
                                                </a>

                                                <span v-if="$page.props.auth.user.user_permissions.indexOf('uploads.approve') > -1">
                                                    <Link :href="route('uploads.approve', { id: content.id,status:2 })" method="post"
                                                        type="button" class="btn btn-success btn-sm" title="Approve">
                                                        <i class="fa fa-check"></i>
                                                    </Link>
                                                </span>
                                            
                                                    <span v-if="$page.props.auth.user.user_permissions.indexOf('uploads.approve') > -1">
                                                        <Link :href="route('uploads.approve', { id: content.id,status:3 })" method="post"
                                                            type="button" class="btn btn-danger btn-sm" title="Reject">
                                                            <i class="fa fa-times"></i>
                                                        </Link>
                                                    </span>

                                                    <Link
                                                        v-if="$page.props.auth.user.user_permissions.indexOf('uploads.delete') > -1"
                                                        :href="route('uploads.delete', { id: content.id })" method="delete"
                                                        type="button" class="btn btn-danger btn-sm" title="Delete">
                                                        <i class="fa fa-trash"></i>
                                                    </Link>
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

/* ── Filter styles (same as DownloadedLogs) ─────────────────────────────────── */
.filters-container {
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.filters-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

.title-section {
    display: flex;
    align-items: center;
    gap: 12px;
}

.title-section i {
    font-size: 20px;
    color: #4a90e2;
}

.title-section h3 {
    font-size: 18px;
    font-weight: 600;
    color: #333;
}

.logs-count {
    font-size: 13px;
    color: #666;
    background: #f5f5f5;
    padding: 2px 8px;
    border-radius: 20px;
}

.clear-all-btn {
    background: #f5f5f5;
    border: 1px solid #e0e0e0;
    padding: 6px 12px;
    border-radius: 6px;
    color: #666;
    font-size: 13px;
    cursor: pointer;
    transition: all 0.2s;
}

.clear-all-btn:hover {
    background: #e0e0e0;
    border-color: #ccc;
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.filter-group-per-page {
    display: flex;
    justify-content: right;
    gap: 8px;
}

.filter-label {
    font-size: 13px;
    font-weight: 500;
    color: #555;
    display: flex;
    align-items: center;
    gap: 6px;
}

.filter-label i {
    font-size: 12px;
    color: #4a90e2;
}

.search-wrapper {
    position: relative;
}

.search-icon {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
    font-size: 12px;
    pointer-events: none;
}

.search-input {
    padding-left: 32px !important;
}

.clear-icon {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
    font-size: 12px;
    cursor: pointer;
    transition: color 0.2s;
}

.clear-icon:hover {
    color: #e23636;
}

.filter-input {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    font-size: 13px;
    transition: all 0.2s;
}

.filter-input:focus {
    outline: none;
    border-color: #4a90e2;
    box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.1);
}

.select-wrapper {
    position: relative;
}

.filter-select {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    font-size: 13px;
    background: white;
    cursor: pointer;
    appearance: none;
}

.filter-select:disabled {
    background-color: #f5f5f5;
    cursor: not-allowed;
}

.filter-select-per-page {
    width: 100%;
    padding: 8px 110px 7px 8px;
    border: 1px solid #e0e0e0;
    border-radius: 6px;
    font-size: 13px;
    background: white;
    justify-content: left;
    cursor: pointer;
    margin-bottom: 10px;
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}

.select-arrow {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
    font-size: 12px;
    pointer-events: none;
}

.date-range {
    display: flex;
    align-items: center;
}

.date-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
}

.date-input {
    flex: 1;
}

.date-separator {
    color: #999;
    font-size: 12px;
}

.shortcuts {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.shortcut-btn {
    padding: 4px 8px;
    background: #f5f5f5;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    font-size: 11px;
    color: #666;
    cursor: pointer;
    transition: all 0.2s;
}

.shortcut-btn:hover {
    background: #e8e8e8;
    border-color: #ccc;
}

.active-filters {
    padding-top: 15px;
    border-top: 1px solid #f0f0f0;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.active-filters-label {
    font-size: 12px;
    color: #888;
    font-weight: 500;
}

.filter-badges {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.filter-badge {
    background: #e8f0fe;
    color: #4a90e2;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.filter-badge i {
    cursor: pointer;
    font-size: 10px;
    opacity: 0.7;
}

.filter-badge i:hover {
    opacity: 1;
}

@media (max-width: 768px) {
    .filters-grid {
        grid-template-columns: 1fr;
        gap: 15px;
    }

    .filters-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }

    .date-wrapper {
        flex-direction: column;
    }

    .date-separator {
        display: none;
    }
}
</style>