<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    gazettedLeave: Object,
});

const form = useForm({
    id: props.gazettedLeave.id,
    title: props.gazettedLeave.title,
    date: props.gazettedLeave.date ? new Date(props.gazettedLeave.date).toISOString().split('T')[0] : '',
    status: props.gazettedLeave.status,
});
</script>

<template>
    <Head title="Edit Gazetted Leave" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Edit Gazetted Leave</h2>
        </template>

        <form @submit.prevent="form.put(route('gazettedleave.update'))">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Edit Gazetted Leave</div>
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