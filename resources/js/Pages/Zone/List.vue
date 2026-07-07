<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import TableHeader from '@/Components/TableHeader.vue';

const props = defineProps({
    zones: Array
})

const toggleStatus = (zone) => {
    const previousStatus = zone.status;   // keep old value
    const newStatus = !previousStatus;    // intended new value
    zone.status = newStatus;              // optimistically update

    router.put(route('zone.toggleStatus', zone.id), {
        status: newStatus
    }, {
        preserveScroll: true,
        onError: (errors) => {
            if (errors.activeSessionFound) {
                alert(errors.activeSessionFound);
            }
            // rollback
            zone.status = previousStatus;
        }
    });
};


const columns = [
  { label: 'ID' },
  { label: 'Name' },
  { label: 'Description' },
  { label: 'Status' },
  { label: 'Action' },
];
</script>
<template>
    <Head title="Zone" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Zone
            </h2>
        </template>
   
        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Zone List</div>
                    <Link v-if="$page.props.auth.user.user_permissions.indexOf('zone.create') > -1" :href="route('zone.create')" class="btn btn-success btn-sm" title="Create New Zone">
                        <i class="fa fa-plus"></i>
                    </Link>
                </div>
            </div>
            <div class="body">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 c_list table-bordered">
                        <TableHeader :columns="columns" />
                        <tbody>
                            <tr v-for="zone in zones" :key="zone.id">
                                <td>{{ zone.id }}</td>
                                <td>{{ zone.name }}</td>
                                <td>{{ zone.description ?? '—' }}</td>
                                <td>
                                    <span class="badge" :class="zone.status ? 'badge-success' : 'badge-danger'">
                                        {{ zone.status ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action_btn">
                                       <div class="custom-control custom-switch">
                                        <input
                                            type="checkbox"
                                            class="custom-control-input"
                                            :id="'switch-' + zone.id"
                                            :checked="zone.status"
                                            @change="toggleStatus(zone)"
                                            >
                                            <label class="custom-control-label cstm_switch" :for="'switch-' + zone.id"></label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
