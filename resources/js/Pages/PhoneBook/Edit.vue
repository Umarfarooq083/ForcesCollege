<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    phonebook_group: Array,
    phonebook_data: Object,
});

const form = useForm({
    id: props.phonebook_data?.id || '',
    name: props.phonebook_data?.name || '',
    contact_no: props.phonebook_data?.contact_no || '',
    phonebook_group_id: props.phonebook_data?.phonebook_group_id || '',
});

/* 🔹 Format Contact Number (same as create.vue) */
const formatContact = (value) => {
    if (!value) return '';

    value = value.replace(/[\s\-()]+/g, '');
    value = value.replace(/\+/g, '');

    if (value.startsWith('92')) return value;
    else if (value.startsWith('0')) return '92' + value.substring(1);
    else if (value.length > 0 && /^\d+$/.test(value)) return '92' + value;

    return value;
};

/* 🔹 Submit Update Form */
const submitForm = () => {
    form.put(route('phonebook.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // optionally show success message
        },
    });
};
</script>

<template>
    <Head title="Edit Contact" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Edit Contact</h2>
        </template>

        <form @submit.prevent="submitForm">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Edit Contact</div>
                        <div class="body">
                            <div class="row">
                                <!-- Name -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <InputLabel value="Name" />
                                        <span class="text-danger font-12 position-absolute">★</span>
                                        <TextInput
                                            type="text"
                                            v-model="form.name"
                                            placeholder="Enter Name"
                                            class="form-control"
                                            required
                                        />
                                        <InputError class="mt-2" :message="form.errors.name" />
                                    </div>
                                </div>

                                <!-- Contact Number -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <InputLabel value="Contact Number" />
                                        <span class="text-danger font-12 position-absolute">★</span>
                                        <TextInput
                                            type="text"
                                            v-model="form.contact_no"
                                            placeholder="Enter Contact Number"
                                            @input="form.contact_no = formatContact(form.contact_no)"
                                            class="form-control"
                                            required
                                        />
                                        <InputError class="mt-2" :message="form.errors.contact_no" />
                                    </div>
                                </div>

                                <!-- Group -->
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <InputLabel value="PhoneBook Group" />
                                        <span class="text-danger font-12 position-absolute">★</span>
                                        <select class="form-control" v-model="form.phonebook_group_id">
                                            <option disabled value="">Select a group</option>
                                            <option
                                                v-for="group in phonebook_group"
                                                :key="group.id"
                                                :value="group.id"
                                            >
                                                {{ group.name }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.phonebook_group_id" />
                                    </div>
                                </div>

                                <!-- Submit -->
                                <div class="col-md-12">
                                    <div class="text-end">
                                        <label class="w-100 d-inline-block">&nbsp;</label>
                                        <PrimaryButton :disabled="form.processing">Update</PrimaryButton>
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
