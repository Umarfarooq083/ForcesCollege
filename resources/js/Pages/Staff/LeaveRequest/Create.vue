<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    staffList: Array,
});

const form = useForm({
    staff_id: '',
    dates: [''],
    leave_type: '',
    reason: '',
});

const addDate = () => {
    form.dates.push('');
};

const removeDate = (index) => {
    form.dates.splice(index, 1);
};
</script>

<template>

    <Head title="Create Leave Request" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Create Leave Request</h2>
        </template>

        <form @submit.prevent="form.post(route('leave-request.submit'))">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Create Leave Request</div>
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

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <InputLabel value="Dates *" />
                                        <div v-for="(date, index) in form.dates" :key="index" class="mb-2">
                                            <div class="d-flex gap-2">
                                                <TextInput type="date" v-model="form.dates[index]" class="form-control"
                                                    required />
                                                <button type="button" @click="removeDate(index)"
                                                    class="btn btn-danger btn-sm" v-if="form.dates.length > 1">
                                                    Remove
                                                </button>
                                            </div>

                                            <InputError class="mt-1" :message="form.errors[`dates.${index}`]" />
                                        </div>
                                        <button type="button" @click="addDate" class="btn btn-primary btn-sm">+ Add
                                            Date</button>
                                        <InputError class="mt-2" :message="form.errors.dates" />
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
                                        <PrimaryButton>Submit</PrimaryButton>
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