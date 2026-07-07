<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { useForm } from '@inertiajs/vue3';
import 'vue-multiselect/dist/vue-multiselect.min.css'

const props = defineProps({
    phonebook_group: Object,
});

const form = useForm({
    name: '',
    contact_no: '',
    phonebook_group_id: '',
});

const formatContact = (value) => {
    if (!value) return '';

    // Remove all spaces, dashes, parentheses, and plus signs
    value = value.replace(/[\s\-()]+/g, '');
    
    // Remove ALL '+' signs from anywhere in the string
    value = value.replace(/\+/g, '');

    // If starts with '92', keep as is
    if (value.startsWith('92')) {
        return value;
    } 
    // If starts with '0', replace with '92'
    else if (value.startsWith('0')) {
        return '92' + value.substring(1);
    }
    // If it's just a number without country code, add '92'
    else if (value.length > 0 && /^\d+$/.test(value)) {
        return '92' + value;
    }

    return value;
};

</script>

<template>

    <Head title="New Fee Type" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Add New Contact</h2>
        </template>
        <form @submit.prevent="form.post(route('phonebook.submit'))">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Add New Contect</div>
                        <div class="body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <InputLabel value="Name" /> <span class="text-danger font-12 position-absolute"> ★</span>
                                        <TextInput type="text" v-model="form.name" placeholder="Enter Name" class="form-control" required/>
                                        <InputError class="mt-2" :message="form.errors.name" />
                                    </div>
                                </div>
                               
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <InputLabel value="Contact Number" /> <span class="text-danger font-12 position-absolute">★</span>
                                        <TextInput type="text" v-model="form.contact_no" placeholder="Enter Contact Number" @input="form.contact_no = formatContact(form.contact_no)" class="form-control" required/>
                                        <InputError class="mt-2" :message="form.errors.contact_no" />
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <InputLabel value="PhoneBook Group" /> <span class="text-danger font-12 position-absolute">★</span>
                                        <select class="form-control" v-model="form.phonebook_group_id">
                                            <option disabled value="" selected>Select a group</option>
                                            <option v-for="phonebookgroup in phonebook_group" :key="phonebookgroup.id" :value="phonebookgroup.id">
                                                {{ phonebookgroup.name }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.phonebook_group" />
                                    </div>
                                </div>
                             
                                <div class="col-md-12">
                                    <div class="text-end">
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
