<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import 'vue-multiselect/dist/vue-multiselect.min.css'
import { ref, computed } from 'vue';
import { useToast } from 'vue-toastification';

const toast = useToast();

const props = defineProps({
    ExamMarks: {
        type: Object,
        default: () => ({})
    },
});

const examName = ref(props.ExamMarks?.exam_subject?.exam_type?.ExamName)
const className = ref(props.ExamMarks?.exam_subject?.class?.ClassName)

const subjectTitle = computed(() => {
    const title = props.ExamMarks?.exam_subject?.Title || "";
    const subjectName = props.ExamMarks?.exam_subject?.subject?.SubjectName || "";
    return `${title} - ${subjectName}`.trim();
});

</script>

<template>
    <Head title="Create Exam Marks" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Create Exam Marks</h2>
        </template>
        
        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <form @submit.prevent="submitForm" class="space-y-6">
                    <!-- Main Card -->
                    <div class="overflow-hidden bg-white shadow-xl sm:rounded-lg">
                        <!-- Header -->
                        <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700 d-flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-semibold text-dark">Create Exam Marks</h3>                            
                            </div>
                            <div>
                                <a :href="route('marks.index')" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 transition"> Go Back </a>
                            </div>
                        </div>
                       
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
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                        </div>
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
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-4m-5 0H9m0 0H5m4 0V9a2 2 0 011-1h4a2 2 0 011 1v12m-6 0h6"/>
                                            </svg>
                                        </div>
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
                                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Students Marks Table -->
                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                                <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-gray-100">
                                    <h4 class="flex items-center text-lg font-semibold text-gray-800">
                                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                        Preview student exam marks
                                    </h4>
                                </div>
                                
                                <div class="overflow-x-auto">
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
                                            <!-- {{ props.ExamMarks }} -->
                                            <tbody>
                                                <tr v-for="marks in props.ExamMarks?.exam_marks_details" :key="marks">
                                                   <td class="text-center">
                                                        <strong>{{ marks?.student?.RollNumber || 'N/A' }}</strong>
                                                    </td>
                                                    <td>
                                                        {{ marks?.student?.FirstName }}  {{ marks?.student?.LastName }}
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="text-white badge bg-info">
                                                            <strong>{{ props.ExamMarks?.exam_subject?.MarksMax }}</strong>
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <strong>{{ marks?.Marks || 'N/A' }}</strong>
                                                    </td>
                                                    <td>
                                                        <strong>{{ marks?.Remarks || 'N/A' }}</strong>
                                                    </td>
                                                </tr>
                                                
                                                <!-- No students message -->
                                                <tr v-if="!props.ExamMarks?.exam_marks_details || props.ExamMarks?.exam_marks_details.length === 0">
                                                    <td colspan="5" class="text-center text-muted">
                                                        No students found for this class
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>