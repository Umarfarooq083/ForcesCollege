<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    leaveRequest: Object,
    staffList: Array,
});

const form = useForm({
    id: props.leaveRequest.id,
    staff_id: props.leaveRequest.staff_id,
    date: props.leaveRequest.date ? new Date(props.leaveRequest.date).toISOString().split('T')[0] : '',
    leave_type: props.leaveRequest.leave_type,
    reason: props.leaveRequest.reason,
});
</script>

<template>
    <Head title="Edit Leave Request" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Edit Leave Request</h2>
        </template>

        <form @submit.prevent="form.put(route('leave-request.update'))">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Edit Leave Request</div>
                        <div class="body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Staff *" />
                                        <select v-model="form.staff_id" class="form-control" required>
                                            <option value="">Select Staff</option>
                                            <option v-for="staff in staffList" :key="staff.id" :value="staff.id">
                                                {{ staff.FirstName }} {{ staff.LastName }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.staff_id" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Leave Type *" />
                                        <select v-model="form.leave_type" class="form-control" required>
                                            <option value="">Select Leave Type</option>
                                            <option value="casual">Casual Leave</option>
                                            <option value="medical">Medical Leave</option>
                                            <option value="maternity">Maternity Leave</option>
                                            <option value="emergency">Emergency Leave</option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.leave_type" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Date *" />
                                        <TextInput type="date" v-model="form.date" class="form-control" required />
                                        <InputError class="mt-2" :message="form.errors.date" />
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <InputLabel value="Reason" />
                                        <textarea v-model="form.reason" class="form-control" rows="3"></textarea>
                                        <InputError class="mt-2" :message="form.errors.reason" />
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="mb-3 text-end">
                                        <label class="w-100 d-inline-block">&nbsp;</label>
                                        <PrimaryButton>Update</PrimaryButton>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </AuthenticatedLayout>
</template>