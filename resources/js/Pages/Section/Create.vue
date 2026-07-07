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
    classesList: Object,
    sectionTypes : Object
});

const sessionList = [
    { id: 1, name: '2024-2025' },
    { id: 2, name: '2026-2027' },
];

const form = useForm({
    SectionName: '',
    ClassId: '',
    SectionType: '',
    Strength: '25',
});

</script>

<template>

    <Head title="Create Section" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Create Section</h2>
        </template>
      
        <form @submit.prevent="form.post(route('section.submit'))">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Create Section</div>
                        <div class="body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <InputLabel value="Section Name" /> <span class="text-danger font-12 position-absolute">★</span>
                                        <TextInput type="text" v-model="form.SectionName" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.SectionName" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <InputLabel value="Class" /> <span class="text-danger font-12 position-absolute">★</span>
                                        <select class="form-control" v-model="form.ClassId">
                                            <option selected disabled value="">Select a Class</option>
                                            <option v-for="classList in classesList" :key="classList.id" :value="classList.id">
                                                {{ classList.ClassName }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.ClassId" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <InputLabel value="Section Type" /> <span class="text-danger font-12 position-absolute">★</span>
                                        <select class="form-control" v-model="form.SectionType">
                                            <option selected disabled value="">Select Section Type</option>
                                            <option v-for="type in sectionTypes" :key="type.id" :value="type.id">
                                                {{ type.name }}
                                            </option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.SectionType" />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <InputLabel value="Strength" /> <span class="text-danger font-12 position-absolute">★</span>
                                        <TextInput  type="number" v-model="form.Strength" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.Strength" />
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
