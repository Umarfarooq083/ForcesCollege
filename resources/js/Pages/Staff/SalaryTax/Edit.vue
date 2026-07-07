<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    salaryTax: Object,
    staffList: Object,
});

const form = useForm({
    id: props.salaryTax.id,
    staff_id: props.salaryTax.staff_id,
    amount: props.salaryTax.amount,
    reason: props.salaryTax.reason,
});
</script>

<template>
    <Head title="Edit Salary Tax" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Edit Salary Tax</h2>
        </template>

        <form @submit.prevent="form.put(route('salarytax.update'))">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Edit Salary Tax</div>
                        <div class="body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Staff *" />
                                        <select class="form-control" v-model="form.staff_id">
                                            <option selected disabled value="">Select a Staff</option>
                                            <option v-for="staff in staffList" :key="staff.id" :value="staff.id">
                                                {{ staff.FirstName }} {{ staff.LastName }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.staff_id" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Amount *" />
                                        <TextInput type="number" step="0.01" v-model="form.amount" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.amount" />
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