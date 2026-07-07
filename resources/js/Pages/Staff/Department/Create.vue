<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { useForm } from '@inertiajs/vue3';
import 'vue-multiselect/dist/vue-multiselect.min.css'


// const props = defineProps({
//     classesList: Object,
// });
const form = useForm({
    DepartmentName: '',
    Code: '',
});

</script>

<template>

    <Head title="Create Department" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Create Department</h2>
        </template>
 
        <form @submit.prevent="form.post(route('department.submit'))">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Create Department</div>
                        <div class="body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Department Name *" />
                                        <TextInput type="text" v-model="form.DepartmentName" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.DepartmentName" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <InputLabel value="Code *" />
                                        <TextInput type="text" v-model="form.Code" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.Code" />
                                    </div>
                                </div>

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
