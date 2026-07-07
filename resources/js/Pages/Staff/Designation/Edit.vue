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
    designations: Object,
});


const form = useForm({
    DesignationName: props.designations.DesignationName,
});

</script>

<template>

    <Head title="Edit Designation" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Edit Designation</h2>
        </template>
       
        <form @submit.prevent="form.put(route('designation.update', {id:designations.id}))">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Edit Designation</div>
                        <div class="body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Designation Name" /> <span class="text-danger font-12 position-absolute">★</span>
                                        <TextInput type="text" v-model="form.DesignationName" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.DesignationName" />
                                    </div>
                                </div>

                                 <div class="col-md-4 text-start">
                                    <label class="w-100 d-inline-block">&nbsp;</label>
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
