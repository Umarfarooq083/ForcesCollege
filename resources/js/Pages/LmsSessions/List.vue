<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import { useToast } from 'vue-toastification';
const toast = useToast();
import {ref, watch} from 'vue';
import debounce from 'lodash/debounce';

const props = defineProps({
    lmssessions: Array,
});

// Create a local copy of sessions to manage UI state
const localSessions = ref([...props.lmssessions]);

const sessionToDelete = ref(null);

function openDeleteModal(session) {
    sessionToDelete.value = session;
    $('#deleteSessionModal').modal('show');
}

function confirmDelete() {
    if (sessionToDelete.value) {
        router.delete(route('lmssessions.destroy', sessionToDelete.value.id), {
            onFinish: () => {
                $('#deleteSessionModal').modal('hide');
                sessionToDelete.value = null;
                router.reload();
            }
        });
    }
}

const sessionToToggle = ref(null);
const toggleTargetStatus = ref(null);

function openToggleModal(session) {
    sessionToToggle.value = session;
    toggleTargetStatus.value = !session.status;
    
    // Update the local session state for immediate UI feedback
    const sessionIndex = localSessions.value.findIndex(s => s.id === session.id);
    if (sessionIndex !== -1) {
        localSessions.value[sessionIndex].status = toggleTargetStatus.value;
    }
    
    $('#toggleConfirmModal').modal('show');
}

function cancelToggle() {
    if (sessionToToggle.value) {
        // Revert the local session state
        const sessionIndex = localSessions.value.findIndex(s => s.id === sessionToToggle.value.id);
        if (sessionIndex !== -1) {
            localSessions.value[sessionIndex].status = !toggleTargetStatus.value;
        }
    }
    sessionToToggle.value = null;
    toggleTargetStatus.value = null;
    $('#toggleConfirmModal').modal('hide');
}

function confirmToggle() {
    if (sessionToToggle.value !== null) {
        router.put(route('lmssessions.toggleStatus', sessionToToggle.value.id), {
            status: toggleTargetStatus.value
        }, {
            preserveScroll: true,
            onError: (errors) => {
                if (errors.status) {
                    toast.error(errors.status, {
                        timeout: 3000 
                    });
                }
                // Revert the local session state on error
                const sessionIndex = localSessions.value.findIndex(s => s.id === sessionToToggle.value.id);
                if (sessionIndex !== -1) {
                    localSessions.value[sessionIndex].status = !toggleTargetStatus.value;
                }
            },
            onFinish: () => {
                sessionToToggle.value = null;
                $('#toggleConfirmModal').modal('hide');
                // Refresh the local sessions from props
                localSessions.value = [...props.lmssessions];
            }
        });
    }
}

const columns = [
  { label: 'ID' },
  { label: 'Session' },
  { label: 'Start Date' },
  { label: 'End Date' },
  { label: 'Status' },
  { label: 'Zone' },
  { label: 'Action' },
];

const search = ref('');
watch(search, debounce((value) => {
  router.get(route('lmssessions.index'), { search: value }, {
    preserveState: true,
    replace: true,
  });
}, 800));

// ✅ This keeps your UI in sync when Inertia sends new props
watch(() => props.lmssessions, (newVal) => {
  localSessions.value = [...newVal];
});

</script>

<template>
    <Head title="Session" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Session
            </h2>
        </template>
   
        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Sessions List</div>

                    <div class="d-flex align-items-center gap-2">
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            v-model="search"
                            placeholder="Search By Session Name"
                            style="width: 220px;"
                        />

                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('lmssessions.create') > -1" :href="route('lmssessions.create')" 
                            class="btn btn-success btn-sm"
                            title="Create New Session"
                            data-tooltip="Create New Session">
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
                            <tr v-for="session,index in localSessions" :key="session.id">
                                <td>{{ index + 1 }}</td>
                                <td>{{ session.name }}</td>
                                <td>{{ session.start_date_formatted  }}</td>
                                <td>{{ session.end_date_formatted  }}</td>
                                <td>
                                     <span class="badge" :class="session.status ? 'badge-success' : 'badge-danger'">
                                        {{ session.status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{  session.zone?.name }}</td>
                                <td>
                                    <div class="action_btn">
                                       <div class="custom-control custom-switch">
                                        <input
                                            type="checkbox"
                                            class="custom-control-input"
                                            :id="'switch-' + session.id"
                                            :checked="session.status"
                                            @change="openToggleModal(session)"
                                            >
                                            <label class="custom-control-label cstm_switch" :for="'switch-' + session.id"></label>
                                        </div>
                                        <Link v-if="$page.props.auth.user.user_permissions.indexOf('lmssessions.edit') > -1" :href="route('lmssessions.edit', session.id)" class="btn btn-sm btn-info">
                                            <i class="fa fa-edit"></i>
                                        </Link>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal fade" id="deleteSessionModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteSessionModalLabel">Confirm Delete</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                   <div class="modal-body">
                        <p class="text-danger font-weight-bold text-center font-16">
                            You're about to delete the session shown below. Are you sure?
                        </p>
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0">
                                <tbody>
                                    <tr>
                                        <th scope="row" style="width: 30%">Name</th>
                                        <td>{{ sessionToDelete?.name }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Zone</th>
                                        <td>{{ sessionToDelete?.zone?.name }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Start Date</th>
                                        <td>{{ sessionToDelete?.start_date }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">End Date</th>
                                        <td>{{ sessionToDelete?.end_date }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button @click="confirmDelete" type="button" class="btn btn-danger">Yes, Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toggle Confirmation Modal -->
        <div class="modal fade" id="toggleConfirmModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirm Status Change</h5>
                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body text-left">
                        <p class="font-weight-bold font-18">
                            Are you sure you want to 
                            <span v-if="toggleTargetStatus">activate</span>
                            <span v-else>deactivate</span> this session?
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" @click="cancelToggle">Cancel</button>
                        <button @click="confirmToggle" type="button" class="btn btn-success">Yes, Proceed</button>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>