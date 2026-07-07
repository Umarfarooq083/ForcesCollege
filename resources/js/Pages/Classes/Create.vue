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
    classtype: Object,
});
const form = useForm({
    ClassName: '',
    class_type_id: '',
    ClassOrder: 0,
});

</script>

<template>

    <Head title="Student Inquiry" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Create Campus</h2>
        </template>
        
        <form @submit.prevent="form.post(route('class.submit'))">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Create Class</div>
                        <div class="body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Class Name" /> <span class="text-danger font-12 position-absolute">★</span>
                                        <TextInput type="text" v-model="form.ClassName" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.ClassName" />
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Class Type" /> <span class="text-danger font-12 position-absolute">★</span>
                                        <select class="form-control" v-model="form.class_type_id">
                                            <option selected disabled value="">Select Class Type</option>
                                            <option v-for="classtypeList in classtype" :key="classtypeList.id" :value="classtypeList.id">
                                                {{ classtypeList.Name }}
                                            </option>
                                        </select>
                                         <InputError class="mt-2" :message="form.errors.class_type_id" />
                                    </div>
                                </div>

                                 <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Order" /> <span class="text-danger font-12 position-absolute">★</span>
                                        <TextInput type="number" v-model="form.ClassOrder" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.ClassOrder" />
                                    </div>
                                </div>

                                <div class="col-md-6">
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
