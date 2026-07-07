<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    tree_permission: Object,
    role_id: Object,
    assignedPermissions: Array,
});

const form = useForm({
    permission: props.assignedPermissions,
    role_id: props.role_id,
});

// Function to toggle all permissions in one module
const toggleAll = (permissions, event) => {
    const checked = event.target.checked;
    const ids = permissions.map(p => p.id);

    if (checked) {
        // Add all permissions of this module if not already selected
        ids.forEach(id => {
            if (!form.permission.includes(id)) {
                form.permission.push(id);
            }
        });
    } else {
        // Remove all permissions of this module
        form.permission = form.permission.filter(id => !ids.includes(id));
    }
};

// Function to check if all permissions in this module are already selected
const isAllSelected = (permissions) => {
    return permissions.every(p => form.permission.includes(p.id));
};
</script>

<template>
    <Head title="Campus Permission" />
    <AuthenticatedLayout>
        <div class="card mt-5">
            <div class="card-body">
                <h4 class="card-title mb-4">Role Permissions</h4>
                <form @submit.prevent="form.post(route('role.permission.submit'))">
                    <input type="hidden" v-model="form.role_id" />

                     <!-- style="    
    padding: 45px 25px 45px 20px;
    box-shadow: 0px 0px 7px 0px;
    border-radius: 4px;
    margin-bottom: 60px;" -->
                    <div class="form-group" v-for="(permissionNameList, index) in tree_permission" :key="index">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="font-weight-bold mb-0">{{ permissionNameList.module_name }}</label>
                            <!-- Select All Checkbox -->
                            <div class="form-check">
                                <input class="form-check-input"
                                    type="checkbox"
                                    :id="'selectAll' + index"
                                    :checked="isAllSelected(permissionNameList.names)"
                                    @change="toggleAll(permissionNameList.names, $event)" />
                                <label class="form-check-label" :for="'selectAll' + index">
                                    Select All
                                </label>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-3">
                            <div class="form-check form-check-inline"
                                v-for="(permissionlist, key) in permissionNameList.names"
                                :key="key">
                                <input class="form-check-input"
                                    v-model="form.permission"
                                    type="checkbox"
                                    :id="'permissionCheckbox' + index + '-' + key"
                                    :value="permissionlist.id" />
                                <label class="form-check-label" :for="'permissionCheckbox' + index + '-' + key">
                                    {{ permissionlist.name }}
                                </label>
                            </div>
                        </div>
                        <hr />
                    </div>

                    <!-- Submit -->
                    <div class="text-end mt-4">
                        <PrimaryButton>Submit</PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.card-title {
    font-size: 1.5rem;
    font-weight: 600;
}
</style>
