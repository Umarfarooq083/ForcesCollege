<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import Multiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.min.css'
import { ref, computed, watch } from 'vue';
import axios from 'axios';


const props = defineProps({
    Exams: Array,
    Classes: Object,
    Subjects: Array,
    Students: Array,
});

const form = useForm({
    ExamId: '',
    ClassId: '',
    ExamSubjectId: '',
    StudentIds: [],
});

// State
const subjects = ref([]);
const students = ref(Array.isArray(props.Students) ? props.Students : []);


// Watch ExamId → enable Class
watch(() => form.ExamId, (newVal) => {
    if (!newVal) {
        form.ClassId = '';
        form.ExamSubjectId = '';
        subjects.value = [];
        students.value = [];
    }
});

// Watch ClassId → fetch subjects from server
watch(() => form.ClassId, async (newVal) => {
    if (newVal) {
        try {
            const res = await axios.get(route('examstudent.subjects', {
                ExamId: form.ExamId,
                ClassId: newVal
            }));
            subjects.value = res.data.subjects;
            students.value = res.data.Students
        } catch (error) {
            console.error(error);
        }
    } else {
        subjects.value = [];
        students.value = [];
    }
});

const isSubmitDisabled = computed(() => {
    return form.StudentIds.length === 0;
});

// const filteredSubjects = computed(() => {
//     if (!form.ClassId) return [];
//     return props.Subjects.filter(
//         subject => subject.ClassId == form.ClassId
//     );
// });

const studentsWithSelectAll = computed(() => {
    if (students.value.length === 0) return [];
    return [{ id: '__all__', FirstName: 'Select', LastName: 'All' }, ...students.value];
});

const handleSelect = (selectedOption) => {
    if (selectedOption.id === '__all__') {
        form.StudentIds = [...students.value];
    }
};

const handleRemove = (removedOption) => {
    if (removedOption.id === '__all__') {
        form.StudentIds = [];
    }
};

</script>

<template>

    <Head title="New Exam Student" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Assign Students to Exam</h2>
        </template>

        <form @submit.prevent="form.post(route('examstudent.submit'))">
            <div class="row">
                <!-- Exam -->
                <div class="mb-3 col-md-3">
                    <InputLabel value="Exam" /> <span class="text-danger">★</span>
                    <select v-model="form.ExamId" class="form-control">
                        <option value="">Select Exam</option>
                        <option v-for="term in props.Exams" :key="term.id" :value="term.id">
                            {{ term.ExamName }}
                        </option>
                    </select>
                    <InputError :message="form.errors.ExamId" />
                </div>

                <!-- Class -->
                <div class="mb-3 col-md-3">
                    <InputLabel value="Class" /> <span class="text-danger">★</span>
                    <select v-model="form.ClassId" class="form-control" :disabled="!form.ExamId">
                        <option value="">Select Class</option>
                        <option v-for="c in props.Classes" :key="c.id" :value="c.id">
                            {{ c.ClassName }}
                        </option>
                    </select>
                    <InputError :message="form.errors.ClassId" />
                </div>

                <!-- Subject -->
                <div class="mb-3 col-md-3">
                    <InputLabel value="Exam Subject" /> <span class="text-danger">★</span>
                    <select v-model="form.ExamSubjectId" class="form-control" :disabled="!form.ClassId">
                        <option value="">Select Subject</option>
                        <option v-for="sub in subjects" :key="sub.id" :value="sub.id">
                            {{ sub.Title }} - {{ sub.subject.SubjectName }}
                        </option>
                    </select>
                    <InputError :message="form.errors.ExamSubjectId" />
                </div>

                <div class="col-md-3">
                    <div class="mb-3">
                        <InputLabel value="Students" /> <span class="text-danger">★</span>
                        <Multiselect v-model="form.StudentIds" :options="studentsWithSelectAll" :multiple="true"
                            track-by="id"
                            :custom-label="s => s.id === '__all__' ? '✔ Select All' : `${s.FirstName} ${s.LastName}`"
                            placeholder="Select Students" :disabled="!form.ExamSubjectId" @select="handleSelect"
                            @remove="handleRemove" />

                        <div v-if="form.errors && Object.keys(form.errors).length">
                            <ul class="mt-2 text-sm text-red-600 list-disc list-inside">
                                <li v-for="(error, index) in form.errors" :key="index">
                                    {{ error }}
                                </li>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="mt-3 text-end">
                <PrimaryButton :disabled="isSubmitDisabled">
                    Submit
                </PrimaryButton>
            </div>
        </form>
    </AuthenticatedLayout>
</template>
