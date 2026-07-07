<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import { useForm } from '@inertiajs/vue3';
import 'vue-multiselect/dist/vue-multiselect.min.css'
import Checkbox from '@/Components/Checkbox.vue'

const props = defineProps({
    examTerms: Array,
    currentSession: Object
});

const form = useForm({
    ExamName: '',
    SessionId: props?.currentSession?.id,
    ExamTermId: '',
    ResultDeclarationDate: '',
    Description: '',
    IsPublishResult: false,
});

</script>

<template>

    <Head title="New Exam" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">New Exam</h2>
        </template>
        
        <form @submit.prevent="form.post(route('examtype.submit'))">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">New Exam</div>
                        <div class="body">
                            <div class="row">
                                <div class="col-md-3 mb-5">
                                    <div class="mb-3">
                                        <InputLabel value="Exam Name" /> <span class="text-danger font-12 position-absolute"> ★</span>
                                        <TextInput type="text" v-model="form.ExamName" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.ExamName" />
                                    </div>
                                </div>
                           

                                <div class="col-md-3 mb-5">
                                    <div class="mb-3">
                                        <InputLabel value="Session" /> <span class="text-danger font-12 position-absolute"> ★</span>
                                        <select v-model="form.SessionId" class="form-control">
                                            <option selected :key="currentSession?.id" :value="currentSession?.id">{{ currentSession?.start_date +'-'+ currentSession?.end_date }}</option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.SessionId" />
                                    </div>
                                </div>

                                <div class="col-md-3 mb-5">
                                    <div class="mb-3">
                                        <InputLabel value="Exam Term" /> <span class="text-danger font-12 position-absolute"> ★</span>
                                        <select v-model="form.ExamTermId" class="form-control">
                                            <option value="">Select Exam Term</option>
                                            <option v-for="term in examTerms" :key="term.id" :value="term.id">{{ term.ExamTermName }}</option>
                                        </select>
                                        <InputError class="mt-2" :message="form.errors.ExamTermId" />
                                    </div>
                                </div>


                                <div class="col-md-3 mb-5">
                                    <div class="mb-3">
                                        <InputLabel value="Result Date" /> <span class="text-danger font-12 position-absolute"> ★</span>
                                        <TextInput type="date" v-model="form.ResultDeclarationDate" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.ResultDeclarationDate" />
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <InputLabel value="Description" /> <span class="text-danger font-12 position-absolute"> </span>
                                        <TextInput type="text" v-model="form.Description" class="form-control" />
                                        <InputError class="mt-2" :message="form.errors.Description" />
                                    </div>
                                </div>

                               <div class="col-md-2" style="margin-top: 32px;" >
                                    <div class="mb-3">
                                         <label class="fancy-checkbox element-left">	
                                            <Checkbox v-model:checked="form.IsPublishResult"  />
                                            <span class="text-dark font-weight-bold">Is Publish Result</span>
                                        </label>
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
