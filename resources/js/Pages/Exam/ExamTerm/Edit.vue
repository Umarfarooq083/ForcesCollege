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
    examTerm: Object,
});

const submitForm = () => {
    if (!form.ExamTermName) {
        form.setError('ExamTermName', 'Exam Term Name is required');
        return;
    }
    form.put(route('examterm.update'));
};

const form = useForm({
    ExamTermName: props.examTerm.ExamTermName,
    id: props.examTerm.id,
});

</script>

<template>

    <Head title="New Fee Type" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">New Exam Term</h2>
        </template>
        <form @submit.prevent="submitForm">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">New Exam Term</div>
                        <div class="body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <InputLabel value="Exam Term Name" /> <span class="text-danger font-12 position-absolute"> ★</span>
                                        <TextInput type="text" v-model="form.ExamTermName" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.ExamTermName" />
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
