<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { computed } from 'vue';

const props = defineProps({
    classList: Array,
    sectionList: Array,
    subjects: Array,
    readonlyMode: {
        type: Boolean,
        default: true,
    },
    home_work: {
        type: Object,
        default: () => ({
            id: null,
            classId: '',
            sectionId: '',
            subjectId: '',
            homeworkDate: '',
            submissionDate: '',
            description: '',
            attachDocumentPath: null,
        }),
    },
});

const fileExtension = computed(() => {
    const path = props?.home_work?.attachDocumentPath;
    return path ? path.split('.').pop() : '';
});

const form = useForm({
    id: props.home_work?.id ?? null,
    classId: props.home_work?.classId ?? '',
    sectionId: props.home_work?.sectionId ?? '',
    subjectId: props.home_work?.subjectId ?? '',
    homeworkDate: props.home_work?.homeworkDate ?? '',
    submissionDate: props.home_work?.submissionDate ?? '',
    Description: props.home_work?.description ?? '',
});
</script>

<template>
    <Head title="Homework Details" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Homework Details
            </h2>
        </template>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="header fw-bold fs-5 p-3 border-bottom">
                        Homework Information
                    </div>

                    <div class="body p-3">
                        <div class="row g-3">

                            <!-- Class -->
                            <div class="col-md-4">
                                <InputLabel value="Class" /> 
                                <select
                                    class="form-control"
                                    v-model="form.classId"
                                    disabled
                                >
                                    <option
                                        v-for="list in classList"
                                        :key="list.id"
                                        :value="list.id"
                                    >
                                        {{ list.ClassName }}
                                    </option>
                                </select>
                            </div>

                            <!-- Section -->
                            <div class="col-md-4">
                                <InputLabel value="Section" />
                                <select
                                    class="form-control"
                                    v-model="form.sectionId"
                                    disabled
                                >
                                    <option
                                        v-for="sec in sectionList"
                                        :key="sec.id"
                                        :value="sec.id"
                                    >
                                        {{ sec.SectionName }}
                                    </option>
                                </select>
                            </div>

                            <!-- Subject -->
                            <div class="col-md-4">
                                <InputLabel value="Subject" />
                                <select
                                    class="form-control"
                                    v-model="form.subjectId"
                                    disabled
                                >
                                    <option
                                        v-for="subject in subjects"
                                        :key="subject.id"
                                        :value="subject.id"
                                    >
                                        {{ subject.SubjectName }}
                                    </option>
                                </select>
                            </div>

                            <!-- Homework Date -->
                            <div class="col-md-4 mt-4">
                                <InputLabel value="Homework Date" />
                                <TextInput
                                    type="date"
                                    v-model="form.homeworkDate"
                                    class="form-control"
                                    readonly
                                />
                            </div>

                            <!-- Submission Date -->
                            <div class="col-md-4 mt-4">
                                <InputLabel value="Submission Date" />
                                <TextInput
                                    type="date"
                                    v-model="form.submissionDate"
                                    class="form-control"
                                    readonly
                                />
                            </div>

                            <!-- Attach Document -->
                            <div class="col-md-4 mt-4" v-if="fileExtension === 'pdf'">
                                <div v-if="props.home_work.attachDocumentPath" class="mt-3 text-center">
                                    <i  class="fa fa-file-pdf-o text-red-600 text-5xl"></i>
                                </div> 
                            </div>

                             <div class="col-md-4 mt-4" v-if="fileExtension === 'docx'">
                                <div v-if="props.home_work.attachDocumentPath" class="mt-3 text-center">
                                   <i class="fa fa-file-word-o text-red-600 text-5xl" aria-hidden="true"></i>
                                </div> 
                            </div>

                            <div class="col-md-4 mt-4" v-if="fileExtension != 'docx' && fileExtension != 'pdf'">
                                <InputLabel value="Attached Document" /> 
                                <div v-if="props.home_work.attachDocumentPath" class="mt-3 text-center">
                                     <img 
                                        :src="`/storage/${props.home_work.attachDocumentPath}`" 
                                        alt="Attached Document" 
                                        class="img-fluid rounded shadow-sm" 
                                        style="max-height: 100px; object-fit: contain;"
                                    />
                                </div> 
                            </div>

                            <!-- Description -->
                            <div class="col-md-12 mt-4">
                                <InputLabel value="Description" />
                                <textarea
                                    v-model="form.Description"
                                    class="form-control"
                                    rows="3"
                                    readonly
                                ></textarea>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style>
select:disabled,
input[readonly],
textarea[readonly] {
    background-color: #f8f9fa !important;
    color: #495057 !important;
    cursor: not-allowed;
}
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
