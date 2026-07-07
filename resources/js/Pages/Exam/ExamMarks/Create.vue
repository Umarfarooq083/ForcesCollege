<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import 'vue-multiselect/dist/vue-multiselect.min.css'
import { ref, watch, computed } from 'vue';
import axios from 'axios';
import { useToast } from 'vue-toastification';

const toast = useToast();

const props = defineProps({
    Exams: Array,
    Classes: Object,
    Students: Array,
});

const subjects = ref([]);
const studentsMarks = ref({});
const classes = ref(Array.isArray(props.Classes) ? props.Classes : []);
const students = ref(Array.isArray(props.Students) ? props.Students : []);

const form = useForm({
    ExamId: '',
    ClassId: '',
    SubjectId: '',
    TotalMarks: '',
    StudentsData: [],
});

// Initialize students marks when Students prop changes
const initializeStudentsMarks = () => {
    if (students.value && students.value.length > 0) {
        const marksData = {};
        students.value.forEach(student => {
            marksData[student.id] = {
                StudentId: student.id,
                Marks: '',
                Remarks: ''
            };
        });
        studentsMarks.value = marksData;
    }
};

// Watch for Students changes and initialize marks
watch(students, initializeStudentsMarks, { immediate: true });

// Watch ExamId → reset dependent fields
watch(() => form.ExamId, (newVal) => {
    if (!newVal) {
        form.ClassId = '';
        form.SubjectId = '';
        form.TotalMarks = '';
        subjects.value = [];
        studentsMarks.value = {};
    }
});

// Watch ExamId  → fetch classes from server
watch(() => form.ExamId, async (newVal) => {
    if (newVal) {
        try {
            const res = await axios.get(route('marks.data', { ExamId: newVal}));
            classes.value = [];
            classes.value = res.data;
        } catch (error) {
            console.error('Error fetching classes:', error);
            toast.error('Failed to fetch classes');
        }
    } else {
        classes.value = [];
        subjects.value = [];
        form.SubjectId = '';
        form.TotalMarks = '';
    }
});

// Watch ClassId → fetch subjects from server

watch(() => form.ClassId, async (newVal) => {
    if (newVal && form.ExamId) {
        try {
            const res = await axios.get(route('marks.data', { 
                ExamId: form.ExamId, 
                ClassId: newVal 
            }));
            
            // Now you can access both
            subjects.value = res.data.subjects;
            // students.value = res.data.Students;            
            form.SubjectId = '';
            form.TotalMarks = '';
        } catch (error) {
            console.error('Error fetching data:', error);
            toast.error('Failed to fetch subjects and students');
        }
    } else {
        subjects.value = [];
        students.value = [];
        form.SubjectId = '';
        form.TotalMarks = '';
    }
});

// Watch SubjectId → fetch total marks
watch(() => form.SubjectId, async (newVal) => {
    if (newVal && form.ExamId && form.ClassId) {
        try {
            const res = await axios.get(route('marks.data', {
                ExamId: form.ExamId,
                ClassId: form.ClassId,
                SubjectId: newVal
            }));
            
            // console.log(res.data);
            students.value = res.data.Students;

            if (res?.data?.original?.message) {
                toast.error(res.data.original.message, {
                    timeout: 3000,
                    position: "top-right"
                });
                form.SubjectId = '';
            } else {
                form.TotalMarks = res.data.subjects;
                initializeStudentsMarks();
            }

        } catch (error) {
            console.error('Error fetching total marks:', error);
            if (error.response?.data?.message) {
                alert(error.response.data.message);
            } else {
                alert("Failed to fetch total marks");
            }
        }
    } else {
        form.TotalMarks = '';
    }
});

// Function to validate and update marks for individual student
const updateStudentMarks = (studentId, marks) => {
    if (!studentsMarks.value[studentId]) return;
    
    // Allow empty string
    if (marks === '' || marks === null || marks === undefined) {
        studentsMarks.value[studentId].Marks = '';
        return;
    }
    
    const numericMarks = parseFloat(marks);
    const maxMarks = form.TotalMarks?.MarksMax || 0;
    
    // Validate marks range - silently restrict without alerts
    if (isNaN(numericMarks)) {
        studentsMarks.value[studentId].Marks = '';
        return;
    }
    
    if (numericMarks > maxMarks) {
        studentsMarks.value[studentId].Marks = maxMarks;
    } else if (numericMarks < 0) {
        studentsMarks.value[studentId].Marks = 0;
    } else {
        studentsMarks.value[studentId].Marks = numericMarks;
    }
};

// Function to update remarks for individual student
const updateStudentRemarks = (studentId, remarks) => {
    if (studentsMarks.value[studentId]) {
        studentsMarks.value[studentId].Remarks = remarks;
    }
};

// Computed property to check if form is ready for submission
const isFormReady = computed(() => {
    return form.ExamId && form.ClassId && form.SubjectId;
});

// Submit form function
const submitForm = () => {
    if (!isFormReady.value) {
        toast.error('Please fill all required fields');
        return;
    }

    // Submit ALL students data (including empty marks)
    const studentsData = Object.values(studentsMarks.value).map(student => ({
        StudentId: student.StudentId,
        Marks: student.Marks === '' || student.Marks === null || student.Marks === undefined ? null : parseFloat(student.Marks),
        Remarks: student.Remarks || null
    }));

    form.StudentsData = studentsData;

    form.post(route('marks.submit'), {
        onSuccess: () => {
            form.reset();
            studentsMarks.value = {};
            subjects.value = [];
        },
        onError: (errors) => {
            toast.error('Failed to save marks. Please check the form.');
            console.error('Form errors:', errors);
        }
    });
};
</script>

<template>
    <Head title="Create Exam Marks" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Create Exam Marks</h2>
        </template>
        
        <form @submit.prevent="submitForm">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="header">Create Exam Marks </div>
                        <div class="body"> 
                            <div class="row">
                                <!-- Exam Selection -->
                                <div class="mb-3 col-md-3">
                                    <InputLabel value="Exam" /> 
                                    <span class="text-danger">*</span>
                                    <select v-model="form.ExamId" class="form-control" required>
                                        <option value="">Select Exam</option>
                                        <option v-for="exam in props.Exams" :key="exam.id" :value="exam.id">
                                            {{ exam.ExamName }}
                                        </option>
                                    </select>
                                    <InputError :message="form.errors.ExamId" />
                                </div>
                             
                                <!-- Class Selection -->
                                <div class="mb-3 col-md-3">
                                    <InputLabel value="Class" /> 
                                    <span class="text-danger">*</span>
                                    <select 
                                        v-model="form.ClassId" 
                                        class="form-control" 
                                        :disabled="!form.ExamId"
                                        required
                                    >
                                        <option value="">Select Class</option>
                                        <option v-for="cls in classes" :key="cls.id" :value="cls.id">
                                            {{ cls.ClassName }}
                                        </option>
                                    </select>
                                    <InputError :message="form.errors.ClassId" />
                                </div>
                                   
                                <!-- Subject Selection -->
                                <div class="mb-3 col-md-3">
                                    <InputLabel value="Exam Subject" /> 
                                    <span class="text-danger">*</span>
                                    <select 
                                        v-model="form.SubjectId" 
                                        class="form-control" 
                                        :disabled="!form.ClassId || subjects.length === 0"
                                        required
                                    >   
                                        <option value="">Select Subject</option>
                                        <option v-for="subject in subjects" :key="subject.id" :value="subject.ExamSubject.id">
                                           {{ subject.ExamSubject.Title }} - {{ subject.ExamSubject.subject.SubjectName }}
                                        </option>
                                    </select>
                                    <InputError :message="form.errors.SubjectId" />
                                </div>
                            
                                <!-- Students Marks Table -->
                                <div v-if="isFormReady" class="mt-4 col-md-12">
                                    <div class="card">
                                        <div class="header">Add Exam Marks for Students</div>
                                        <div class="body table-responsive">
                                            <table class="table text-center table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Roll No</th>
                                                        <th>Student Name</th>
                                                        <th>Total Marks</th>
                                                        <th>Obtained Marks</th>
                                                        <th>Remarks</th>
                                                    </tr>
                                                </thead>
                                              
                                                <tbody>
                                                    <tr v-for="student in students" :key="student.id">
                                                        <td class="text-center">
                                                            <strong>{{ student?.id || 'N/A' }}</strong>
                                                        </td>
                                                        <td>
                                                            {{ student?.FirstName }} {{ student?.LastName }}
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-info">
                                                                {{ form.TotalMarks?.MarksMax || 0 }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <TextInput 
                                                                type="number" 
                                                                :modelValue="studentsMarks[student.id]?.Marks || ''"
                                                                @update:modelValue="updateStudentMarks(student.id, $event)"
                                                                class="form-control" 
                                                                :min="0" 
                                                                :max="form.TotalMarks?.MarksMax || 100" 
                                                                step="any"
                                                                placeholder="Enter marks"
                                                            />
                                                        </td>
                                                        <td>
                                                            <TextInput 
                                                                type="text" 
                                                                :modelValue="studentsMarks[student.id]?.Remarks || ''"
                                                                @update:modelValue="updateStudentRemarks(student.id, $event)"
                                                                class="form-control" 
                                                                placeholder="Optional remarks"
                                                                maxlength="255"
                                                            />
                                                        </td>
                                                    </tr>
                                                    
                                                    <!-- No students message -->
                                                    <tr v-if="!props.Students || props.Students.length === 0">
                                                        <td colspan="5" class="text-center text-muted">
                                                            No students found for this class
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            
                                            <!-- Submit Button -->
                                            <div class="mt-3 d-flex justify-content-end">
                                                <PrimaryButton 
                                                    type="submit" 
                                                    :disabled="form.processing || !isFormReady"
                                                    class="btn btn-primary"
                                                >
                                                    <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                                                    {{ form.processing ? 'Submitting...' : 'Save Marks' }}  
                                                </PrimaryButton>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Loading states -->
                                <div v-else-if="form.ExamId && form.ClassId && !form.SubjectId" class="text-center col-md-12">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Please select a subject to continue
                                    </div>
                                </div>

                                <div v-else-if="form.ExamId && !form.ClassId" class="text-center col-md-12">
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Please select a class to continue
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