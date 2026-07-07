<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import 'vue-multiselect/dist/vue-multiselect.min.css'
import { ref, computed, watch, onMounted } from 'vue';
import { useToast } from 'vue-toastification';

const toast = useToast();

const props = defineProps({
    ExamMarks: {
        type: Object,
        default: () => ({})
    },
    Classes: {
        type: Object,
        default: () => ({}),
    },
    Students: {
        type: Array,
        default: () => ([]),
    },
});

const examName = ref(props.ExamMarks?.exam_subject?.exam_type?.ExamName);
const exam_id = ref(props.ExamMarks?.exam_subject?.exam_type?.id);
const className = ref(props.ExamMarks?.exam_subject?.class?.ClassName);
const class_id = ref(props.ExamMarks?.exam_subject?.class?.id);
const exam_subject_id = ref(props.ExamMarks?.exam_subject?.id);
const examMarksId = ref(props.ExamMarks?.id);
const maxMarks = ref(props.ExamMarks?.exam_subject?.MarksMax || 0);

const studentsMarks = ref({});

// Initialize studentsMarks from props
// onMounted(() => {
//     if (props.ExamMarks?.exam_marks_details) {
//         props.ExamMarks.exam_marks_details.forEach(mark => {
//             studentsMarks.value[mark.StudentId] = {
//                 StudentId: mark.StudentId,
//                 Marks: mark.Marks || '',
//                 Remarks: mark.Remarks || '',
//                 id: mark.id
//             };
//         });
//     }
// });

// Jab Students load hon, existing marks set karo
// watch(() => props.Students, (students) => {
//     if (students) {
//         students.forEach(stu => {
//             studentsMarks.value[stu.id] = {
//                 StudentId: stu.StudentId,  // yeh add karo
//                 Marks: stu.marks?.Marks ?? null,
//                 Remarks: stu.marks?.Remarks ?? null,
//             };
//         });
//     }
// }, { immediate: true });

watch(() => props.Students, (students) => {
    if (students) {
        students.forEach(stu => {
            studentsMarks.value[stu.StudentId] = {   // ✅ StudentId as key
                StudentId: stu.StudentId,
                Marks: stu.marks?.Marks ?? null,
                Remarks: stu.marks?.Remarks ?? null,
            };
        });
    }
}, { immediate: true });

const form = useForm({
    id: examMarksId,
    ExamId: exam_id,
    ClassId: class_id,
    SubjectId: exam_subject_id,
    TotalMarks: {
        MarksMax: maxMarks
    },
    StudentsData: [],
});

const subjectTitle = computed(() => {
    const title = props.ExamMarks?.exam_subject?.Title || "";
    const subjectName = props.ExamMarks?.exam_subject?.subject?.SubjectName || "";
    return `${title} - ${subjectName}`.trim();
});

// Computed property to check if form is ready for submission
const isFormReady = computed(() => {
    return form.ExamId && form.ClassId && form.SubjectId && Object.keys(studentsMarks.value).length > 0;
});

// Function to validate and update marks for individual student
const updateStudentMarks = (studentId, marks) => {
    if (!studentsMarks.value[studentId]) {
        studentsMarks.value[studentId] = { StudentId: studentId, Marks: '', Remarks: '' };
    }
    
    // Allow empty string
    if (marks === '' || marks === null || marks === undefined) {
        studentsMarks.value[studentId].Marks = '';
        return;
    }
    
    const numericMarks = parseFloat(marks);
    const max = maxMarks.value;
    
    // Validate marks range - silently restrict without alerts
    if (isNaN(numericMarks)) {
        studentsMarks.value[studentId].Marks = '';
        return;
    }
    
    if (numericMarks > max) {
        studentsMarks.value[studentId].Marks = max;
        toast.warning(`Marks cannot exceed ${max}. Value set to ${max}.`);
    } else if (numericMarks < 0) {
        studentsMarks.value[studentId].Marks = 0;
        toast.warning('Marks cannot be negative. Value set to 0.');
    } else {
        studentsMarks.value[studentId].Marks = numericMarks;
    }
};

// Update remarks
const updateStudentRemarks = (studentId, remarks) => {
    if (!studentsMarks.value[studentId]) {
        studentsMarks.value[studentId] = { StudentId: studentId, Marks: '', Remarks: '' };
    }
    studentsMarks.value[studentId].Remarks = remarks;
};

// Submit form function
const submitForm = () => {
    if (!isFormReady.value) {
        toast.error('Please fill all required fields');
        return;
    }

    // Validate at least one student has marks
    const hasMarks = Object.values(studentsMarks.value).some(student => 
        student.Marks !== '' && student.Marks !== null && student.Marks !== undefined
    );

    if (!hasMarks) {
        toast.warning('Please enter marks for at least one student');
        return;
    }

    // Submit ALL students data
    const studentsData = Object.values(studentsMarks.value).map(student => ({
        StudentId: student.StudentId,
        Marks: student.Marks === '' || student.Marks === null || student.Marks === undefined ? null : parseFloat(student.Marks),
        Remarks: student.Remarks || null
    }));

    form.StudentsData = studentsData;

    form.put(route('marks.update'), {
        onSuccess: () => {
            toast.success('Exam marks updated successfully!');
            form.reset();
        },
        onError: (errors) => {
            toast.error('Failed to update marks. Please check the form.');
            console.error('Form errors:', errors);
        }
    });
};

</script>

<template>
    <Head title="Update Exam Marks" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Update Exam Marks</h2>
        </template>
        
        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <form @submit.prevent="submitForm" class="space-y-6">
                    <!-- Main Card -->
                    <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">
                        <!-- Header -->
                        <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700 d-flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold text-dark">Update Exam Marks</h3>                            
                            </div>
                            <div>
                                <a :href="route('marks.index')" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition"> Go Back </a>
                            </div>
                        </div>
                       <!-- {{ Students }} -->
                        <!-- Form Fields -->
                        <div class="p-6">
                            <div class="grid grid-cols-1 gap-6 mb-8 md:grid-cols-3">
                                <!-- Exam Selection -->
                                <div class="space-y-2">
                                    <InputLabel value="Exam Type" class="text-sm font-medium text-gray-700" /> 
                                    <div class="relative">
                                        <TextInput 
                                            type="text"  
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                            v-model="examName" 
                                            disabled
                                        />
                                    </div>
                                </div>
                                
                                <!-- Class Selection -->
                                <div class="space-y-2">
                                    <InputLabel value="Class" class="text-sm font-medium text-gray-700" /> 
                                    <div class="relative">
                                        <TextInput 
                                            type="text"  
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                            v-model="className" 
                                            disabled
                                        />
                                    </div>
                                </div>
                                
                                <!-- Subject Selection -->
                                <div class="space-y-2">
                                    <InputLabel value="Subject" class="text-sm font-medium text-gray-700" /> 
                                    <div class="relative">
                                        <TextInput 
                                            type="text"  
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                            v-model="subjectTitle" 
                                            readonly
                                        />
                                    </div>
                                </div>
                            </div>
                            <!-- {{ Students }} -->
                            <!-- Students Marks Table -->
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
                                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                                    <h4 class="flex items-center text-lg font-semibold text-gray-800">
                                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                        Update Student Exam Marks
                                    </h4>
                                </div>
                                
                                <div class="overflow-x-auto">
                                    <table class="w-full border-collapse">
                                        <thead class="bg-gray-100 border-b border-gray-300">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Roll No</th>
                                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Student Name</th>
                                                <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Total Marks</th>
                                                <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700">Obtained Marks</th>
                                                <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700">Remarks</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="marks in props.Students" :key="marks.StudentId" class="border-b border-gray-200 hover:bg-gray-50">
                                                <td class="px-4 py-3 text-sm font-medium text-gray-800">
                                                    {{ marks?.student?.RollNumber || 'N/A' }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-700">
                                                    {{ marks?.student?.FirstName }} {{ marks?.student?.LastName }}
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <span class="inline-block px-3 py-1 text-sm font-semibold text-white bg-blue-500 rounded">
                                                        {{ props.ExamMarks?.exam_subject?.MarksMax }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-center">
                                                    <TextInput 
                                                        type="number" 
                                                        :modelValue="studentsMarks[marks.StudentId]?.Marks"
                                                        @update:modelValue="updateStudentMarks(marks.StudentId, $event)"
                                                        class="w-full px-2 py-1 border border-gray-300 rounded text-center" 
                                                        :min="0" 
                                                        :max="maxMarks"
                                                        step="0.01" 
                                                    />
                                                </td>
                                                <td class="px-4 py-3">
                                                    <TextInput 
                                                        type="text" 
                                                        :modelValue="studentsMarks[marks.StudentId]?.Remarks"
                                                        @update:modelValue="updateStudentRemarks(marks.StudentId, $event)"
                                                        class="w-full px-2 py-1 border border-gray-300 rounded" 
                                                        placeholder="Optional remarks"
                                                        maxlength="255"
                                                    />
                                                </td>
                                            </tr>
                                            
                                            <!-- No students message -->
                                            <tr v-if="!props.Students || props.Students.length === 0">
                                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                                    No students found for this exam
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Submit Button -->
                                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3">
                                    <PrimaryButton 
                                        type="submit" 
                                        :disabled="!isFormReady || form.processing"
                                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition"
                                    >
                                        <span v-if="form.processing" class="inline-block mr-2">
                                            <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                            </svg>
                                        </span>
                                        {{ form.processing ? 'Updating...' : 'Update Marks' }}  
                                    </PrimaryButton>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>