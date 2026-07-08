<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    programLevelData: Object,
    programs: Array,
});

const form = useForm({
    id: props.programLevelData.id,
    programm_id: props.programLevelData.programm_id,
    title: props.programLevelData.title,
    status: props.programLevelData.status,
});

</script>

<template>

    <Head title="Edit Program Level" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Edit Program Level</h2>
        </template>
  
        <form @submit.prevent="form.put(route('programlevel.update'))">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Edit Program Level</div>
                        <div class="body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Program *" />
                                        <select class="form-control" v-model="form.programm_id">
                                            <option selected disabled value="">Select a Program</option>
                                            <option v-for="program in programs" :key="program.id" :value="program.id">
                                                {{ program.name }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.programm_id" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Title *" />
                                        <TextInput type="text" v-model="form.title" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.title" />
                                    </div>
                                </div>

                                <!-- <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Status" />
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" v-model="form.status" id="statusCheck">
                                            <label class="form-check-label" for="statusCheck">Active</label>
                                        </div>
                                        <InputError class="mt-2" :message="form.errors.status" />
                                    </div>
                                </div> -->

                                <div class="col-md-6">
                                    
                                </div>

                                <div class="col-md-6">
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