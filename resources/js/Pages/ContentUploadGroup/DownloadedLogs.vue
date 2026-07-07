<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import Pagination from '@/Components/Pagination.vue';
import { ref, watch, computed } from 'vue';
import debounce from 'lodash/debounce';
import axios from 'axios';

const props = defineProps({
    DownloadedLogs: Object,
    Campuses: Array,
});

const columns = [
    { label: 'ID' },
    { label: 'Contant Title' },
    { label: 'User' },
    { label: 'Domain' },
    { label: 'Date' },
];

// ── Filters ──────────────────────────────────────────────────────────────────
const search = ref('');
const selectedCampus = ref('');
const dateFrom = ref('');
const dateTo = ref('');
const perPage = ref(props.DownloadedLogs?.per_page || 25);

// ── Selection ────────────────────────────────────────────────────────────────
const selectedIds = ref([]);
const selectAll = ref(false);

watch(() => props.DownloadedLogs?.data, (data) => {
    if (!data || !data.length) { selectedIds.value = []; selectAll.value = false; return; }
    const allIds = data.map(r => r.id);
    selectAll.value = allIds.length > 0 && allIds.every(id => selectedIds.value.includes(id));
}, { deep: true });

const toggleSelectAll = () => {
    if (selectAll.value) {
        selectedIds.value = (props.DownloadedLogs?.data || []).map(r => r.id);
    } else {
        selectedIds.value = [];
    }
};

// ── Selection count
const selectedCount = computed(() => selectedIds.value.length);
const hasSelection = computed(() => selectedCount.value > 0);

// ── Export ────────────────────────────────────────────────────────────────────
const exporting = ref(false);

const exportSelected = () => {
    exporting.value = true;
    axios.post(route('downloaded.logs.export'), {
        campus_wise: selectedCampus.value || undefined,
        title: search.value || undefined,
        date_from: dateFrom.value || undefined,
        date_to: dateTo.value || undefined,
        selected_ids: selectedIds.value,
    }, {
        responseType: 'blob',
    }).then((response) => {
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'downloaded-logs.xlsx');
        document.body.appendChild(link);
        link.click();
        link.remove();
        window.URL.revokeObjectURL(url);
    }).catch(() => {
        alert('Export failed. Please try again.');
    }).finally(() => {
        exporting.value = false;
    })
    ;
};

function clearFilters() {
    search.value = '';
    selectedCampus.value = '';
    dateFrom.value = '';
    dateTo.value = '';
    perPage.value = 25;
    selectedIds.value = [];
    selectAll.value = false;
}

const hasActiveFilters = computed(() =>
    search.value || selectedCampus.value || dateFrom.value || dateTo.value
);

// Debounced: title + campus + dateFrom + perPage
  const applyFilters = debounce(() => {
      router.get(route('downloaded.logs'), {
          title: search.value,
          campus_wise: selectedCampus.value || undefined,
          date_from: dateFrom.value || undefined,
          date_to: dateTo.value || undefined,
          per_page: perPage.value,
      }, {
          preserveState: true,
        replace: true,
        only: ['DownloadedLogs'],
    });
}, 500);

watch([search, selectedCampus, dateFrom, dateTo], applyFilters);

// Per-page change: reset to page 1 before reloading
  watch(perPage, (val) => {
      router.get(route('downloaded.logs'), {
          title: search.value,
          campus_wise: selectedCampus.value || undefined,
          date_from: dateFrom.value || undefined,
          date_to: dateTo.value || undefined,
          per_page: val,
      }, {
          preserveState: true,
          replace: true,
        only: ['DownloadedLogs'],
    });
});

// Date auto-adjustment helpers
function autoAdjustRange() {
    if (dateFrom.value && !dateTo.value) {
        dateTo.value = dateFrom.value;
    }
    if (dateTo.value && !dateFrom.value) {
        dateFrom.value = dateTo.value;
    }
}

// Add to your script section
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

const clearDates = () => {
    dateFrom.value = '';
    dateTo.value = '';
};

// Quick date shortcuts
const setToday = () => {
    const today = new Date().toISOString().split('T')[0];
    dateFrom.value = today;
    dateTo.value = today;
};

const setYesterday = () => {
    const yesterday = new Date();
    yesterday.setDate(yesterday.getDate() - 1);
    const dateStr = yesterday.toISOString().split('T')[0];
    dateFrom.value = dateStr;
    dateTo.value = dateStr;
};

const setLast7Days = () => {
    const end = new Date();
    const start = new Date();
    start.setDate(start.getDate() - 7);
    dateFrom.value = start.toISOString().split('T')[0];
    dateTo.value = end.toISOString().split('T')[0];
};

const setThisMonth = () => {
    const now = new Date();
    const start = new Date(now.getFullYear(), now.getMonth(), 1);
    const end = new Date(now.getFullYear(), now.getMonth() + 1, 0);
    dateFrom.value = start.toISOString().split('T')[0];
    dateTo.value = end.toISOString().split('T')[0];
};


watch(dateTo, autoAdjustRange);
watch(dateFrom, autoAdjustRange);
</script>
<template>

    <Head title="Content Downloaded Logs" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Content Downloaded Logs
            </h2>
        </template>

        <div class="card">
            <div class="header">
                <div class="filters-container">
                    <div class="filters-header">
                        <div class="title-section">
                            <i class="fa fa-download"></i>
                            <h3 class="mb-0">Content Downloaded Logs</h3>
                            <span class="logs-count" v-if="DownloadedLogs?.total">
                                ({{ DownloadedLogs.total }} entries)
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

                        <!-- Campus dropdown -->
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fa fa-university"></i> Campus
                            </label>
                            <div class="select-wrapper">
                                <select class="filter-select" v-model="selectedCampus">
                                    <option value="">All Campuses</option>
                                    <option v-for="campus in Campuses" :key="campus.id" :value="campus.tenant_id">
                                        {{ campus.SchoolName }}
                                    </option>
                                </select>
                                <i class="fa fa-chevron-down select-arrow"></i>
                            </div>
                        </div>

                        <!-- Date range -->
                        <div class="filter-group">
                            <label class="filter-label">
                                <i class="fa fa-calendar"></i> Date Range
                            </label>
                            <div class="date-range">
                                <div class="date-wrapper">
                                    <input type="date" class="filter-input date-input" v-model="dateFrom" />
                                    <span class="date-separator">to</span>
                                    <input type="date" class="filter-input date-input" v-model="dateTo" />
                                </div>
                            </div>
                        </div>

                        <!-- Per page -->
                       

                        <!-- Quick date shortcuts -->
                        <div class="filter-group shortcuts-group">
                            <label class="filter-label">
                                <i class="fa fa-bolt"></i> Quick Filters
                            </label>
                            <div class="shortcuts">
                                <button class="shortcut-btn" @click="setToday">Today</button>
                                <button class="shortcut-btn" @click="setYesterday">Yesterday</button>
                                <button class="shortcut-btn" @click="setLast7Days">Last 7 days</button>
                                <button class="shortcut-btn" @click="setThisMonth">This month</button>
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
                            <div v-if="selectedCampus" class="filter-badge">
                                <span>Campus: {{ getCampusName(selectedCampus) }}</span>
                                <i class="fa fa-times" @click="selectedCampus = ''"></i>
                            </div>
                            <div v-if="dateFrom || dateTo" class="filter-badge">
                                <span>Date: {{ formatDateRange }}</span>
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
                                <select class="filter-select-per-page"  v-model="perPage">
                                    <option v-for="n in [25,50,100,150,200]" :key="n" :value="n">{{ n }}</option>
                                </select>
                                <i class="fa fa-chevron-down select-arrow"></i>
                            </div>

                            <div>
                                <button type="button" class="btn btn-success btn-sm" :disabled="!hasSelection || exporting"
                                    @click="exportSelected">
                                    <span v-if="exporting" class="spinner-border spinner-border-sm me-1" role="status"></span>
                                    <i v-else class="fa fa-file-excel-o me-1"></i>
                                    {{ exporting ? 'Exporting…' : 'Export Selected' }}
                                </button>
                            </div>
                            
                        </div>

                        <!-- <div class="d-flex justify-content-between align-items-center py-2">
                            <span  class="text-muted small">  
                            </span>
                            <button type="button" class="btn btn-success btn-sm" :disabled="!hasSelection || exporting"
                                @click="exportSelected">
                                <span v-if="exporting" class="spinner-border spinner-border-sm me-1" role="status"></span>
                                <i v-else class="fa fa-file-excel-o me-1"></i>
                                {{ exporting ? 'Exporting…' : 'Export Selected' }}
                            </button>
                        </div> -->


                    <table class="table table-hover mb-0 c_list table-bordered">
                        <thead>
                            <tr>
                                <th width="40"><input type="checkbox" v-model="selectAll" @change="toggleSelectAll"
                                    title="Select all" /></th>
                                <th v-for="col in columns" :key="col.label">{{ col.label }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="content, index in (DownloadedLogs?.data || [])">
                                <td class="p-0 text-center align-middle" width="40">
                                    <input type="checkbox" :value="content.id" v-model="selectedIds"
                                        class="form-check-input m-0" />
                                </td>
                                <td>{{ index + 1 }}</td>
                                <td>{{ content.upload_content ? content.upload_content.ContentTitle : 'N/A' }}</td>
                                <td>{{ content.user ? content.user.name : 'N/A' }}</td>
                                <td>{{ content?.domainName || 'N/A' }}</td>
                                <td>{{ content.created_at ? new Date(content.created_at).toLocaleString() : 'N/A' }}</td>
                            </tr>
                            <tr v-if="!DownloadedLogs?.data?.length">
                                <td :colspan="columns.length + 1" class="text-center py-4">No logs found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <Pagination v-if="DownloadedLogs?.links?.length" :links="DownloadedLogs.links" />
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
