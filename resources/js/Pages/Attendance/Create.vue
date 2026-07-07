<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, useForm, router } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TableHeader from '@/Components/TableHeader.vue';
import { computed, watch, reactive, onMounted, ref } from 'vue';

const props = defineProps({
    classesList: Array,
    sections: Array,
    students: Array,
    existingAttendance: Object,
    filters: Object,
});

const form = useForm({
    class_id: props.filters?.class_id || '',
    section_id: props.filters?.section_id || '',
    date: props.filters?.date || ''
});

const filteredSections = computed(() => {
    if (!form.class_id) return [];
    return props.sections.filter(
        (sec) => sec.ClassId === parseInt(form.class_id)
    );
});

const attendance_date = ref();
function applyFilter() {
    form.get(route('attendance.create'), {
        preserveState: true,
        preserveScroll: true,   
        only: ['students', 'filters', 'existingAttendance', 'AttendanceDate'],
        onSuccess: (page) => {
            attendance_date.value = page.props.AttendanceDate ?? form.date;
        },
    });
}

watch(() => form.class_id, () => {
    form.section_id = '';
});

const attendanceData = reactive({})
const today = new Date()
const todayDate = ref(today.toISOString().split('T')[0]) 

// Function to initialize attendance data
const initializeAttendanceData = () => {
    const studentList = Array.isArray(props.students) ? props.students : props.students.data || []
    studentList.forEach(student => {
        if (student.id) {
            if (props.existingAttendance && props.existingAttendance[student.id] !== undefined) {
                const attendanceType = props.existingAttendance[student.id];
                switch(attendanceType) {
                    case 'Absent':
                        attendanceData[student.id] = '0';
                        break;
                    case 'Present':
                        attendanceData[student.id] = '1';
                        break;
                    case 'Leave':
                        attendanceData[student.id] = '2';
                        break;
                    default:
                        attendanceData[student.id] = '1';
                }
            } else {
                attendanceData[student.id] = '1';
            }
        }
    })
}

// Watch for changes in students and existingAttendance
watch([() => props.students, () => props.existingAttendance], () => {
    Object.keys(attendanceData).forEach(key => {
        delete attendanceData[key];
    });
    initializeAttendanceData();
}, { immediate: true, deep: true })

onMounted(() => {
    initializeAttendanceData();
})

const submitForm = () => {
    const filteredAttendance = {}
    const studentList = Array.isArray(props.students) ? props.students : props.students.data || []
    studentList.forEach(student => {
        if (attendanceData[student.id] !== undefined) {
            filteredAttendance[student.id] = attendanceData[student.id]
        }
    })

    router.post('/attendance/submit', {
        attendance: filteredAttendance,
        class_id: form.class_id,
        section_id: form.section_id,
        date: form.date
    })
}

const columns = [
    { label: 'ID' },
    { label: 'Student Name' },
    { label: 'Class Name' },
    { label: 'Section Type' },
    { label: 'Date' },
    { label: 'Status' },
    { label: 'Attendance' },
];


</script>

<template>
    <Head title="Create Attendance" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Create Attendance</h2>
        </template>
        
        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <!-- Filter Form -->
                        <form @submit.prevent="applyFilter" class="mb-6">
                            <div class="mb-4 row">
                                <!-- Class Dropdown -->
                                <div class="col-md-3">
                                    <InputLabel value="Classes" />
                                    <select class="form-control" v-model="form.class_id" required>
                                        <option value="">Select Class</option>
                                        <option v-for="cls in classesList" :key="cls.id" :value="cls.id">
                                            {{ cls.ClassName }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Section Dropdown -->
                                <div class="col-md-3">
                                    <InputLabel value="Sections" />
                                    <select class="form-control" v-model="form.section_id" required>
                                        <option value="">Select Section</option>
                                        <option v-for="sec in filteredSections" :key="sec.id" :value="sec.id">
                                            {{ sec.SectionName }}
                                        </option>
                                    </select>
                                </div>
                                
                                <!-- Date Filter -->
                                <div class="col-md-3">
                                    <InputLabel value="Date" />
                                    <input type="date" class="form-control" v-model="form.date" :max="todayDate" required />
                                </div>

                                <!-- Filter Button -->
                                <div class="col-md-3 d-flex align-items-end">
                                    <PrimaryButton class="w-100" type="submit">Filter</PrimaryButton>
                                </div>
                            </div>
                        </form>
                        
                        <!-- Attendance Form -->
                        <form @submit.prevent="submitForm" v-if="students.length > 0">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0">Student Attendance</h5>
                                            <small class="text-muted">
                                                Date: {{ form.date }} | 
                                                Total Students: {{ students.length }}
                                            </small>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table mb-0 text-center table-hover table-bordered">
                                                    <TableHeader :columns="columns" />
                                                    <tbody>
                                                        <tr v-for="(student, index) in students" :key="student.id">
                                                            <td>{{ index + 1 }}</td>
                                                            <td>{{ student.FirstName }} {{ student.LastName }}</td>
                                                            <td>{{ student?.class?.ClassName || 'N/A' }}</td>
                                                            <td>{{ student?.section?.section_type?.name || 'N/A' }}</td>
                                                            <td>
                                                                <span class="text-white badge bg-success" v-if="Object.keys(existingAttendance).includes(String(student.id)) === true">
                                                                    {{ attendance_date ?? '' }}
                                                                </span>
                                                            </td>
                                                            <!-- <td>{{ form.date }}</td> -->
                                                            <td>
                                                                <span v-if="student.IsActive == 1" class="badge badge-success">
                                                                    Active
                                                                </span>
                                                                <span v-else class="badge badge-secondary">
                                                                    Inactive
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <select 
                                                                    class="form-control form-control-sm"
                                                                    v-model="attendanceData[student.id]"
                                                                    style="width: 120px;"
                                                                >
                                                                    <option value="1">Present</option>
                                                                    <option value="0">Absent</option>
                                                                    <option value="2">Leave</option>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4 row">
                                        <div class="col-md-12">
                                            <div class="text-end">
                                                <PrimaryButton type="submit" class="px-4">
                                                    Submit Attendance
                                                </PrimaryButton>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        
                        <!-- No Students Message -->
                        <div v-else-if="showStudents && students.length === 0" class="alert alert-info">
                            <h5>No Students Found</h5>
                            <p>No students found for the selected class and section.</p>
                        </div>
                        
                        <!-- Filter Instructions -->
                        <div v-else class="alert">
                           <h5>No Student Found</h5>
                            <!-- <p>Please select Class, Section, and Date to view students and mark attendance.</p> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.form-control-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>