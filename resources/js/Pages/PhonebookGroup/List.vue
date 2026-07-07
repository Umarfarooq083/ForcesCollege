<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';
import Pagination from '@/Components/Pagination.vue';
import { ref, watch } from 'vue';
import debounce from 'lodash/debounce';
import { useToast } from 'vue-toastification';
const toast = useToast();

const props = defineProps({
    phonebook_groups: Object,
});

const columns = [
    { label: 'Name' },
    { label: 'Action' },
];

const search = ref('');
watch(search, debounce((value) => {
  router.get(route('phonebookgroup.index'), { search: value }, {
    preserveState: true,
    replace: true,
  });
}, 800));

/* 🔹 MODAL STATE VARIABLES */
const showModal = ref(false);
const modalTitle = ref('Add Phonebook Group');
const form = ref({
    id: null,
    name: ''
});

/* 🔹 OPEN CREATE MODAL */
const openCreateModal = () => {
    modalTitle.value = 'Add Phonebook Group';
    form.value = { id: null, name: '' };
    showModal.value = true;
};

/* 🔹 OPEN EDIT MODAL */
const openEditModal = (group) => {
    modalTitle.value = 'Edit Phonebook Group';
    form.value = { id: group.id, name: group.name };
    showModal.value = true;
};

/* 🔹 SUBMIT FORM (CREATE OR UPDATE) */
const submitForm = () => {
    if (!form.value.name.trim()) {
        toast.error('Please enter group name');
        return;
    }

    if (form.value.id) {
        router.put(route('phonebookgroup.update'), form.value, {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
            },
        });
    } else {
        router.post(route('phonebookgroup.submit'), form.value, {
            preserveScroll: true,
            onSuccess: () => {
                showModal.value = false;
            },
        });
    }
};


// ================= DELETE CONFIRMATION MODAL =================
const showDeleteModal = ref(false)
const deleteId = ref(null)

function confirmDelete(id) {
  deleteId.value = id
  showDeleteModal.value = true
}

function deleteRecord() {
  if (!deleteId.value) return
  router.delete(route('phonebookgroup.delete', { id: deleteId.value }), {
    onSuccess: () => {
      showDeleteModal.value = false
    },
  })
}
</script>

<template>
    <Head title="Phonebook Group" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                PhoneBook Group
            </h2>
        </template>
        
        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">PhoneBook Groups</div>
                    <div class="d-flex align-items-center gap-2">
                        <input
                            type="text"
                            class="form-control form-control-sm"
                            v-model="search"
                            placeholder="Search By PhoneBook Group Name"
                            style="width: 250px;"
                        />

                        <button 
                            class="btn btn-success btn-sm"
                            @click="openCreateModal">
                            <i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="body">
                <div class="table-responsive">
                    <table class="table table-hover text-center mb-0 c_list table-bordered">
                        <TableHeader :columns="columns" />
                        <tbody>
                           <tr v-for="group in props.phonebook_groups.data" :key="group.id">
                                <td>{{ group.name }}</td>
                                <td>
                                    <div class="action_btn d-flex justify-content-center gap-2">
                                    <button    
                                        @click="openEditModal(group)" 
                                        class="btn btn-info btn-sm">
                                        <i class="fa fa-edit"></i>
                                    </button>

                                   <button v-if="$page.props.auth.user.user_permissions.indexOf('phonebookgroup.delete') >-1"
                                        @click="confirmDelete(group.id)" class="btn btn-danger btn-sm"
                                        title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination :links="phonebook_groups.links" />
            </div>
        </div>

        <!-- 🔹 CREATE / EDIT MODAL -->
        <div class="modal fade show" v-if="showModal" style="display:block; background-color:rgba(0,0,0,0.4);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ modalTitle }}</h5>
                        <button type="button" class="btn-close" @click="showModal = false"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Group Name</label>
                            <input type="text" class="form-control" v-model="form.name" placeholder="Enter group name" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary btn-sm" @click="showModal = false">Cancel</button>
                        <button class="btn btn-primary btn-sm" @click="submitForm">
                            {{ form.id ? 'Update' : 'Save' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

         <!-- ================= DELETE CONFIRMATION MODAL ================= -->
    <div
      v-if="showDeleteModal"
      class="modal fade show"
      style="display: block; background-color: rgba(0, 0, 0, 0.4)"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title">Confirm Deletion</h5>
            <button
              type="button"
              class="btn-close btn-close-white"
              @click="showDeleteModal = false"
            ></button>
          </div>
          <div class="modal-body text-center">
            <p class="fw-bold text-secondary mb-3">
              Are you sure you want to delete this record?
            </p>
            <div class="d-flex justify-content-center gap-3">
              <button
                type="button"
                class="btn btn-secondary btn-sm px-3"
                @click="showDeleteModal = false"
              >
                Cancel
              </button>
              <button
                type="button"
                class="btn btn-danger btn-sm px-3"
                @click="deleteRecord"
              >
                Yes, Delete
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
    </AuthenticatedLayout>
</template>
