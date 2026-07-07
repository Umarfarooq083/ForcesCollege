<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { useForm } from '@inertiajs/vue3';
import 'vue-multiselect/dist/vue-multiselect.min.css'

const form = useForm({
    name: '',
    description: '', 
});

</script>

<template>

    <Head title="Create Zone" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Create Zone</h2>
        </template>
       
        <form @submit.prevent="form.post(route('zone.submit'))" >
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Create Zone</div>
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
                                        <InputLabel  value="Description" />
                                        <TextInput  type="text" v-model="form.description" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.email" />
                                    </div>
                                </div>

                                 <div class="col-md-12 text-end mt-4">
                                    <PrimaryButton>Submit</PrimaryButton>
                                </div>

                            </div>
                            
                        </div>
                    </div>
                </div>
              
            </div>
        </form>
    </AuthenticatedLayout>
</template>
