<script setup>
import { ref, watch } from 'vue'
import { Link, Head, router } from '@inertiajs/vue3'
import debounce from 'lodash/debounce'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import TableHeader from '@/Components/TableHeader.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  phonebook_list: Object,
})

const columns = [
  { label: 'Name' },
  { label: 'Contact' },
  { label: 'Group' },
  { label: 'Action' },
]

const search = ref('')
watch(
  search,
  debounce((value) => {
    router.get(
      route('phonebook.index'),
      { search: value },
      { preserveState: true, replace: true }
    )
  }, 800)
)

// ================= SHOW MODAL =================
const showModal = ref(false)
const showData = ref({})

function showPhonebook(phonebook) {
  showData.value = phonebook
  showModal.value = true
}

// ================= DELETE CONFIRMATION MODAL =================
const showDeleteModal = ref(false)
const deleteId = ref(null)

function confirmDelete(id) {
  deleteId.value = id
  showDeleteModal.value = true
}

function deleteRecord() {
  if (!deleteId.value) return
  router.delete(route('phonebook.delete', { id: deleteId.value }), {
    onSuccess: () => {
      showDeleteModal.value = false
    },
  })
}
</script>

<template>
  <Head title="Phone Book" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Phone Book
      </h2>
    </template>

    <div class="card">
      <div class="header">
        <div
          class="w-100 d-inline-flex justify-content-between align-items-center"
        >
          <div class="font-weight-bold">Phone Book</div>

          <div class="d-flex align-items-center gap-2">
            <input
              type="text"
              class="form-control form-control-sm"
              v-model="search"
              placeholder="Search by name or contact"
              style="width: 295px"
            />

            <Link
              v-if="
                $page.props.auth.user.user_permissions.indexOf('phonebook.create') >
                -1
              "
              :href="route('phonebook.create')"
              class="btn btn-success btn-sm"
              title="Create New"
            >
              <i class="fa fa-plus"></i>
            </Link>
          </div>
        </div>
      </div>

      <div class="body">
        <div class="table-responsive">
          <table
            class="table table-hover text-center mb-0 c_list table-bordered"
          >
            <TableHeader :columns="columns" />
            <tbody>
              <tr
                v-for="phonebook in props.phonebook_list.data"
                :key="phonebook.id"
              >
                <td>{{ phonebook?.name }}</td>
                <td>{{ phonebook?.contact_no }}</td>
                <td>{{ phonebook?.phonebookgroup?.name }}</td>
                <td>
                  <div
                    class="action_btn d-flex justify-content-center gap-2"
                  >
                    <Link
                      v-if="
                        $page.props.auth.user.user_permissions.indexOf('phonebook.edit') >
                        -1
                      "
                      :href="route('phonebook.edit', { id: phonebook.id })"
                      class="btn btn-info btn-sm"
                      title="Edit"
                    >
                      <i class="fa fa-edit"></i>
                    </Link>

                    <button
                      v-if="
                        $page.props.auth.user.user_permissions.indexOf('phonebook.show') >
                        -1
                      "
                      @click="showPhonebook(phonebook)"
                      class="btn btn-success btn-sm"
                      title="Show"
                    >
                      <i class="fa fa-eye"></i>
                    </button>

                    <button
                      v-if="
                        $page.props.auth.user.user_permissions.indexOf('phonebook.delete') >
                        -1
                      "
                      @click="confirmDelete(phonebook.id)"
                      class="btn btn-danger btn-sm"
                      title="Delete"
                    >
                      <i class="fa fa-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <Pagination :links="phonebook_list.links" />
      </div>
    </div>

    <!-- ================= SHOW MODAL ================= -->
    <div
      v-if="showModal"
      class="modal fade show"
      style="display: block; background-color: rgba(0, 0, 0, 0.4)"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title">Phonebook Details</h5>
            <button
              type="button"
              class="btn-close btn-close-white"
              @click="showModal = false"
            ></button>
          </div>
          <div class="modal-body">
            <p><strong>Name:</strong> {{ showData?.name }}</p>
            <p><strong>Contact:</strong> {{ showData?.contact_no }}</p>
            <p><strong>Group:</strong> {{ showData?.phonebookgroup?.name }}</p>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary btn-sm"
              @click="showModal = false"
            >
              Close
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
