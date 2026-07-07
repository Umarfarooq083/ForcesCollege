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
    classes: Array,
    grade: Object,
});

const form = useForm({
    id: props.grade.id,
    GradeName: props.grade.GradeName,
    ClassId: props.grade.ClassId,
    PercentFrom: props.grade.PercentFrom,
    PercentUpt: props.grade.PercentUpt,
    Description: props.grade.Description,
});

</script>

<template>

    <Head title="New Exam" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">New Exam</h2>
        </template>
        <form @submit.prevent="form.put(route('marksgrade.update'))">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">New Exam</div>
                        <div class="body">
                            <div class="row">
                                <div class="col-md-3 mb-5">
                                    <div class="mb-3">
                                        <InputLabel value="Grade Name" /> <span class="text-danger font-12 position-absolute"> ★</span>
                                        <TextInput type="text" v-model="form.GradeName" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.GradeName" />
                                    </div>
                                </div>
                           

                                <div class="col-md-3 mb-5">
                                    <div class="mb-3">
                                        <InputLabel value="Class" /> <span class="text-danger font-12 position-absolute"> ★</span>

                                           <select v-model="form.ClassId" class="form-control">
                                            <option value="">Select Class</option>
                                            <option v-for="classList in classes" :key="classList.id" :value="classList.id">{{ classList.ClassName }}</option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.ClassId" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <InputLabel value="Percent From" /> <span class="text-danger font-12 position-absolute"> ★</span>
                                        <TextInput type="number" v-model="form.PercentFrom" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.PercentFrom" />
                                    </div>
                                </div>


                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <InputLabel value="Percent Upto" /> <span class="text-danger font-12 position-absolute"> ★</span>
                                        <TextInput type="number" v-model="form.PercentUpt" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.PercentUpt" />
                                    </div>
                                </div>


                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <InputLabel value="Description" /> <span class="text-danger font-12 position-absolute"> ★</span>
                                        <TextInput type="text" v-model="form.Description" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.Description" />
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
