<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import 'vue-multiselect/dist/vue-multiselect.min.css'

const min = 0;
const step = 1;

const props = defineProps({
    setting: Object,
});

const form = useForm({
    name: props?.setting?.name,
    key: props?.setting?.key, 
    value: props?.setting?.value,
});

</script>

<template>

    <Head title="Create Zone" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Edit Zone</h2>
        </template>
       
        <form @submit.prevent="form.put(route('setting.update'))" >
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">
                            Edit Site Setting
                            <InputError class="mt-2" :message="form.errors.key" />
                        </div>
                        <div class="body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Name" />
                                        <TextInput type="text" v-model="form.name" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.name" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel  value="Value" />
                                        <TextInput  type="number" :min="min" :step="step" v-model="form.value" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.value" />
                                    </div>
                                </div>

                                 <div class="mt-4 col-md-12 text-end">
                                    <PrimaryButton>Update</PrimaryButton>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
