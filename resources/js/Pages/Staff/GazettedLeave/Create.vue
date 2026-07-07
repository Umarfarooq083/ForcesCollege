<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    title: '',
    date: '',
    status: true,
});
</script>

<template>
    <Head title="Create Gazetted Leave" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Create Gazetted Leave</h2>
        </template>

        <form @submit.prevent="form.post(route('gazettedleave.submit'))">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Create Gazetted Leave</div>
                        <div class="body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Title *" />
                                        <TextInput type="text" v-model="form.title" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.title" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Date *" />
                                        <TextInput type="date" v-model="form.date" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.date" />
                                    </div>
                                </div>

                                <!-- <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Status" />
                                        <select class="form-control" v-model="form.status">
                                            <option :value="true">Active</option>
                                            <option :value="false">Inactive</option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.status" />
                                    </div>
                                </div> -->

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