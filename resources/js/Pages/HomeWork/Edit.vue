<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { computed } from 'vue';

const props = defineProps({
    classList: Array,
    sectionList: Array,
    subjects: Array,
    home_work: {
        type: Object,
        default: () => ({
            id: null,
            classId: '',
            class: '',
            sectionId: '',
            section: '',
            subjectId: '',
            subject: '',
            homeworkDate: '',
            submissionDate: '',
            description: '',
            attachDocumentPath: null,
        }),
    },
});

// 🔹 Initialize Form
const form = useForm({
    id: props.home_work?.id ?? null,
    classId: props.home_work?.classId ?? '',
    class: props.home_work?.class ?? '',
    sectionId: props.home_work?.sectionId ?? '',
    section: props.home_work?.section ?? '',
    subjectId: props.home_work?.subjectId ?? '',
    subject: props.home_work?.subject ?? '',
    homeworkDate: props.home_work?.homeworkDate ?? '',
    submissionDate: props.home_work?.submissionDate ?? '',
    description: props.home_work?.description ?? '',
    attachDocumentPath: null,
});

// 🔹 Filter section and subjects based on selected class
const filteredSections = computed(() =>
    props.sectionList.filter(section => section.ClassId == form.classId)
);

const filteredSubjects = computed(() =>
    props.subjects.filter(subject => subject.ClassId == form.classId)
);

// 🔹 Class change
const selectedClass = (event) => {
    const selected = props.classList.find(c => c.id == event.target.value);
    form.class = selected ? selected.ClassName : '';
    form.sectionId = '';
    form.subjectId = '';
};

// 🔹 Section change
const onSectionChange = (event) => {
    const selected = filteredSections.value.find(s => s.id == event.target.value);
    form.section = selected ? selected.SectionName : '';
    form.subjectId = ''; // reset subject when section changes
};

// 🔹 Subject change
const onSubjectChange = (event) => {
    const selected = filteredSubjects.value.find(s => s.id == event.target.value);
    form.subject = selected ? selected.SubjectName : '';
};

// 🔹 File upload
const onFileChange = (event) => {
    form.attachDocumentPath = event.target.files[0] ?? null;
};

// 🔹 Handle Submit (for both Create & Update)
const submitForm = () => {
    const isUpdate = !!form.id;

    router.post(
        isUpdate ? route('homework.update', form.id) : route('homework.store'),
        form,
        {
            forceFormData: true,
            onSuccess: () => {
                form.reset('attachDocumentPath');
            },
        }
    );
};
</script>

<template>
    <Head :title="form.id ? 'Edit Homework' : 'Create Homework'" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ form.id ? 'Edit Homework' : 'Create Homework' }}
            </h2>
        </template>

        <form @submit.prevent="submitForm" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header fw-bold fs-5 p-3 border-bottom">
                            {{ form.id ? 'Edit Homework' : 'Add Homework' }}
                        </div>
                        <div class="body p-3">
                            <div class="row g-3">
                                
                                <!-- Class -->
                                <div class="col-md-4">
                                    <InputLabel value="Class" /> 
                                    <select
                                        class="form-control"
                                        v-model="form.classId"
                                        @change="selectedClass"
                                        required
                                    >
                                        <option disabled value="">Select Class</option>
                                        <option
                                            v-for="list in classList"
                                            :key="list.id"
                                            :value="list.id"
                                        >
                                            {{ list.ClassName }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.classId" />
                                </div>

                                <!-- Section -->
                                <div class="col-md-4">
                                    <InputLabel value="Section" />
                                    <select
                                        class="form-control"
                                        v-model="form.sectionId"
                                        @change="onSectionChange"
                                        :disabled="!form.classId"
                                        required
                                    >
                                        <option disabled value="">Select Section</option>
                                        <option
                                            v-for="sec in filteredSections"
                                            :key="sec.id"
                                            :value="sec.id"
                                        >
                                            {{ sec.SectionName }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.sectionId" />
                                </div>

                                <!-- Subject -->
                                <div class="col-md-4">
                                    <InputLabel value="Subject" />
                                    <select
                                        class="form-control"
                                        v-model="form.subjectId"
                                        @change="onSubjectChange"
                                        :disabled="!form.sectionId"
                                        required
                                    >
                                        <option disabled value="">Select Subject</option>
                                        <option
                                            v-for="subject in filteredSubjects"
                                            :key="subject.id"
                                            :value="subject.id"
                                        >
                                            {{ subject.SubjectName }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.subjectId" />
                                </div>

                                <!-- Homework Date -->
                                <div class="col-md-4 mt-4">
                                    <InputLabel value="Homework Date" />
                                    <TextInput
                                        type="date"
                                        v-model="form.homeworkDate"
                                        class="form-control"
                                        required
                                    />
                                    <InputError class="mt-2" :message="form.errors.homeworkDate" />
                                </div>

                                <!-- Submission Date -->
                                <div class="col-md-4 mt-4">
                                    <InputLabel value="Submission Date" />
                                    <TextInput
                                        type="date"
                                        v-model="form.submissionDate"
                                        class="form-control"
                                        required
                                    />
                                    <InputError class="mt-2" :message="form.errors.submissionDate" />
                                </div>

                                <!-- Attach Document -->
                                <div class="col-md-4 mt-4">
                                    <InputLabel value="Attach Document" />
                                    <input
                                        type="file"
                                        class="form-control"
                                        @change="onFileChange"
                                    />
                                    <div v-if="props.home_work.attachDocumentPath" class="mt-3">
                                        <div class="rounded text-center">
                                            <img 
                                                :src="`/storage/${props.home_work.attachDocumentPath}`" 
                                                alt="Attached Document" 
                                                class="img-fluid rounded" 
                                                style="max-height: 50px; object-fit: contain;"
                                            />
                                        </div>
                                    </div>
                                    <InputError class="mt-2" :message="form.errors.attachDocumentPath" />
                                </div>

                                <!-- Description -->
                                <div class="col-md-12 mt-4">
                                    <InputLabel value="Description" />
                                    <textarea
                                        v-model="form.description"
                                        class="form-control"
                                        rows="3"
                                        placeholder="Enter homework description..."
                                    ></textarea>
                                    <InputError class="mt-2" :message="form.errors.description" />
                                </div>

                                <!-- Submit Button -->
                                <div class="col-md-12 text-end mt-3">
                                    <PrimaryButton :disabled="form.processing">
                                        {{ form.id ? 'Update' : 'Submit' }}
                                    </PrimaryButton>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
