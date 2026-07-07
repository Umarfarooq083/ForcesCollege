<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import 'vue-multiselect/dist/vue-multiselect.min.css';
import { computed, ref } from 'vue';

const props = defineProps({
    classList: Object,
    sectionList: Array,
    subjects: Array,
});

const studentList = ref([]);

// 🔹 Form setup
const form = useForm({
    classId: '',
    class: '',
    sectionId: '',
    section: '',
    subjectId: '',
    subject: '',
    homeworkDate: '',
    submissionDate: '',
    description: '',
    attachDocumentPath: null, // ✅ file object (not string)
});

// 🔹 Filter sections based on class
const filteredSections = computed(() => {
    return props.sectionList.filter(section => section.ClassId === form.classId);
});

// 🔹 Filter subjects based on class
const filteredSubjects = computed(() => {
    return props.subjects.filter(subject => subject.ClassId == form.classId);
});

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
};

// 🔹 Subject change
const onSubjectChange = (event) => {
    const selected = filteredSubjects.value.find(s => s.id == event.target.value);
    form.subject = selected ? selected.SubjectName : '';
};

// ✅ Handle file input (actual file object)
const onFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        form.attachDocumentPath = file;
    } else {
        form.attachDocumentPath = null;
    }
};

// ✅ Submit function (can be used for create or update)
const submitForm = () => {
    form.post(route('homework.submit'), {
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Create Homework" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Create Homework</h2>
        </template>

        <form @submit.prevent="submitForm">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header fw-bold fs-5 p-3 border-bottom">Create Student Homework</div>
                        <div class="body p-3">
                            <div class="row g-3">

                                <!-- Class -->
                                <div class="col-md-4">
                                    <InputLabel value="Class" /> 
                                    <span class="text-danger font-12 position-absolute">★</span>
                                    <select class="form-control" v-model="form.classId" @change="selectedClass($event)" required>
                                        <option selected disabled value="">Select a Class</option>
                                        <option v-for="list in classList" :key="list.id" :value="list.id">
                                            {{ list.ClassName }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.classId" />
                                </div>

                                <!-- Section -->
                                <div class="col-md-4">
                                    <InputLabel value="Section" /> 
                                    <span class="text-danger font-12 position-absolute">★</span>
                                    <select 
                                        class="form-control" 
                                        v-model="form.sectionId" 
                                        @change="onSectionChange($event)" 
                                        :disabled="!form.classId"
                                        required
                                    >
                                        <option selected disabled value="">Select a Section</option>
                                        <option v-for="sec in filteredSections" :key="sec.id" :value="sec.id">
                                            {{ sec.SectionName }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.SectionId" />
                                </div>

                                <!-- Subject -->
                                <div class="col-md-4">
                                    <InputLabel value="Subject" /> 
                                    <span class="text-danger font-12 position-absolute">★</span>
                                    <select 
                                        class="form-control" 
                                        v-model="form.subjectId" 
                                        @change="onSubjectChange($event)"
                                        :disabled="!form.sectionId"
                                        required
                                    >
                                        <option selected disabled value="">Select Subject</option>
                                        <option v-for="subject in filteredSubjects" :key="subject.id" :value="subject.id">
                                            {{ subject.SubjectName }}
                                        </option>
                                    </select>
                                    <InputError class="mt-2" :message="form.errors.SubjectId" />
                                </div>

                                <!-- Homework Date -->
                                <div class="col-md-4 mt-4">
                                    <InputLabel value="Homework Date" /> 
                                    <span class="text-danger font-12 position-absolute">★</span>
                                    <TextInput type="date" v-model="form.homeworkDate" class="form-control" required />
                                    <InputError class="mt-2" :message="form.errors.homeworkDate" />
                                </div>

                                <!-- Submission Date -->
                                <div class="col-md-4 mt-4">
                                    <InputLabel value="Submission Date" /> 
                                    <span class="text-danger font-12 position-absolute">★</span>
                                    <TextInput type="date" v-model="form.submissionDate" class="form-control" required />
                                    <InputError class="mt-2" :message="form.errors.submissionDate" />
                                </div>

                                <!-- Attach Document -->
                                <div class="col-md-4 mt-4">
                                    <InputLabel value="Attach Document" />
                                    <input type="file" class="form-control" @change="onFileChange" />
                                    <InputError class="mt-2" :message="form.errors.attachDocumentPath" />
                                </div>

                                <!-- Description -->
                                <div class="col-md-12 mt-4">
                                    <InputLabel value="Description" />
                                    <textarea v-model="form.description" class="form-control" rows="3" placeholder="Enter homework description..."></textarea>
                                    <InputError class="mt-2" :message="form.errors.description" />
                                </div>

                                <!-- Submit Button -->
                                <div class="col-md-12 text-end mt-3">
                                    <PrimaryButton :disabled="form.processing">Submit</PrimaryButton>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
