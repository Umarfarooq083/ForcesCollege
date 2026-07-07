<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { useForm } from '@inertiajs/vue3';
import 'vue-multiselect/dist/vue-multiselect.min.css'
import Multiselect from 'vue-multiselect'
import { computed } from 'vue';

const props = defineProps({
    classes: Array,
});

const form = useForm({
    GradeName: '',
    ClassId: [],
    PercentFrom: '',
    PercentUpt: '',
    Description: '',
});

const ClassesOption = computed(() => {
    return props.classes.map(type => ({
        id: type.id,
        name: type.ClassName
    }));
});

</script>

<template>

    <Head title="New Exam" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">New Exam</h2>
        </template>
        <form @submit.prevent="form.post(route('marksgrade.submit'))">
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
                                            <Multiselect
                                                :options="ClassesOption"
                                                v-model="form.ClassId"
                                                :multiple="true"
                                                :track-by="'id'"
                                                :label="'name'"
                                                placeholder="Select roles"
                                            />
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
                                        <InputLabel value="Description" /> <span class="text-danger font-12 position-absolute"> </span>
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
