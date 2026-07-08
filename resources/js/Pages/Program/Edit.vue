<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    programData: Object,
});

const form = useForm({
    id: props.programData.id,
    name: props.programData.name,
    type: props.programData.type,
    duration: props.programData.duration,
});

const programTypeList = [
    { id: 'annual', name: 'Annual' },
    { id: 'semester', name: 'Semester' },
];

</script>

<template>

    <Head title="Edit Program" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Edit Program</h2>
        </template>
  
        <form @submit.prevent="form.put(route('program.update'))">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Edit Program</div>
                        <div class="body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Program Name *" />
                                        <TextInput type="text" v-model="form.name" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.name" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Program Type *" />
                                        <select class="form-control" v-model="form.type">
                                            <option selected disabled value="">Select a Type</option>
                                            <option v-for="typeList in programTypeList" :key="typeList.id" :value="typeList.id">
                                                {{ typeList.name }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.type" />
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Duration *" />
                                        <TextInput type="text" v-model="form.duration" class="form-control" placeholder="e.g., 2 years, 4 semesters" />
                                        <InputError class="mt-2" :message="form.errors.duration" />
                                    </div>
                                </div>

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