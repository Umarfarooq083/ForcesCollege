<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Checkbox from '@/Components/Checkbox.vue';
import TableHeader from '@/Components/TableHeader.vue';
import { watch, reactive, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    departments: Array,
    staffs: Array,
    existingAttendance: Object,
    filters: Object,
    AttendanceDate: String,
    officeStartTime: String,
});

const form = useForm({
    all_staff: props.filters?.all_staff || false,
    department_id: props.filters?.department_id || '',
    date: props.filters?.date || ''
});

const attendance_date = ref();
function applyFilter() {
    if (isSunday(form.date)) {
        alert('Sunday dates are not allowed for staff attendance.');
        form.date = '';
        return;
    }
    form.get(route('staff.attendance.list'), {
        preserveState: true,
        preserveScroll: true,
        only: ['staffs', 'filters', 'existingAttendance', 'AttendanceDate', 'officeStartTime'],
        onSuccess: (page) => {
            attendance_date.value = page.props.AttendanceDate ?? form.date;
            if (page.props.officeStartTime) officeStartTime.value = page.props.officeStartTime;
        },
    });
}

const handleDateChange = () => {
    if (isSunday(form.date)) {
        alert('Sunday dates are not allowed for staff attendance.');
        form.date = '';
    }
};

const attendanceData = reactive({});
const startTimeData = reactive({});
const endTimeData = reactive({});
const lateMinutesData = reactive({});
const toDateInputValue = (date) => {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
};
const todayDate = ref(toDateInputValue(new Date()));
const officeStartTime = ref(props.officeStartTime || '09:00');
const currentTime = ref(new Date());
let currentTimeTimer = null;

const isSunday = (dateString) => {
    if (!dateString) return false;
    const date = new Date(dateString);
    return date.getDay() === 0;
};

const parseTimeToMinutes = (time) => {
    if (!time) return null;

    const normalizedTime = String(time).trim();
    const twelveHourMatch = normalizedTime.match(/^(\d{1,2}):(\d{2})\s*(AM|PM)$/i);

    if (twelveHourMatch) {
        let hours = Number(twelveHourMatch[1]);
        const minutes = Number(twelveHourMatch[2]);
        const period = twelveHourMatch[3].toUpperCase();

        if (period === 'PM' && hours !== 12) hours += 12;
        if (period === 'AM' && hours === 12) hours = 0;

        return hours * 60 + minutes;
    }

    const twentyFourHourMatch = normalizedTime.match(/^(\d{1,2}):(\d{2})/);
    if (!twentyFourHourMatch) return null;

    return Number(twentyFourHourMatch[1]) * 60 + Number(twentyFourHourMatch[2]);
};

const getCurrentTimeMinutes = () => {
    const now = currentTime.value;

    return now.getHours() * 60 + now.getMinutes();
};

const calculateLateMinutes = (startTime) => {
    const startMinutes = parseTimeToMinutes(startTime);

    if (startMinutes === null) return 0;

    return Math.max(getCurrentTimeMinutes() - startMinutes, 0);
};

const recalculateLateMinutes = () => {
    Object.keys(startTimeData).forEach(staffId => {
        lateMinutesData[staffId] = attendanceData[staffId] === '1'
            ? calculateLateMinutes(startTimeData[staffId])
            : 0;
    });
};

// Initialize attendance data from existingAttendance
const initializeAttendanceData = () => {
    if (isSunday(form.date)) {
        return;
    }
    const staffList = Array.isArray(props.staffs) ? props.staffs : props.staffs?.data || [];

    staffList.forEach(staff => {
        if (staff.id) {
            if (props.existingAttendance && props.existingAttendance[staff.id] !== undefined) {
                const existingData = props.existingAttendance[staff.id];
                const attendanceType = typeof existingData === 'object' ? existingData.attendance : existingData;
                switch (attendanceType) {
                    case 'Absent':
                        attendanceData[staff.id] = '0';
                        break;
                    case 'Present':
                        attendanceData[staff.id] = '1';
                        break;
                    case 'Leave':
                        attendanceData[staff.id] = '2';
                        break;
                    default:
                        attendanceData[staff.id] = '1';
                }
                if (typeof existingData === 'object' && attendanceType === 'Present') {
                    startTimeData[staff.id] = existingData.start_time || '';
                    endTimeData[staff.id] = existingData.end_time || '';
                    lateMinutesData[staff.id] = existingData.late_minutes || 0;
                } else {
                    startTimeData[staff.id] = '';
                    endTimeData[staff.id] = '';
                    lateMinutesData[staff.id] = 0;
                }
            } else {
                attendanceData[staff.id] = '1';
                startTimeData[staff.id] = officeStartTime.value;
                endTimeData[staff.id] = '';
                lateMinutesData[staff.id] = 0;
            }
        }
    });
};

watch([() => props.staffs, () => props.existingAttendance], () => {
    Object.keys(attendanceData).forEach(key => {
        delete attendanceData[key];
    });
    Object.keys(startTimeData).forEach(key => {
        delete startTimeData[key];
    });
    Object.keys(endTimeData).forEach(key => {
        delete endTimeData[key];
    });
    Object.keys(lateMinutesData).forEach(key => {
        delete lateMinutesData[key];
    });
    initializeAttendanceData();
}, { immediate: true });

watch([startTimeData, attendanceData, currentTime], recalculateLateMinutes, { deep: true });

watch(attendanceData, (newVal, oldVal) => {
        Object.keys(newVal).forEach(staffId => {
            if (oldVal && oldVal[staffId] && oldVal[staffId] !== newVal[staffId]) {
                const wasPresent = String(oldVal[staffId]) === '1';
                const isPresent = String(newVal[staffId]) === '1';
                if (!isPresent) {
                    startTimeData[staffId] = '';
                    endTimeData[staffId] = '';
                } else if (!wasPresent) {
                    startTimeData[staffId] = officeStartTime.value;
                    endTimeData[staffId] = '';
                }
            }
        });
    }, { deep: true });

onMounted(() => {
    initializeAttendanceData();
    currentTimeTimer = setInterval(() => {
        currentTime.value = new Date();
    }, 60000);
});

onUnmounted(() => {
    if (currentTimeTimer) clearInterval(currentTimeTimer);
});

const submitForm = () => {
    if (isSunday(form.date)) {
        alert('Sunday dates are not allowed for staff attendance.');
        return;
    }
    const filteredAttendance = {};
    const staffList = Array.isArray(props.staffs) ? props.staffs : props.staffs?.data || [];

    staffList.forEach(staff => {
        if (attendanceData[staff.id] !== undefined) {
            filteredAttendance[staff.id] = {
                status: attendanceData[staff.id],
                start_time: startTimeData[staff.id] || null,
                end_time: endTimeData[staff.id] || null,
            };
        }
    });

    router.post(route('staff.attendance.submit'), {
        attendance: filteredAttendance,
        department_id: form.department_id,
        date: form.date,
        all_staff: form.all_staff,
    }, {
        onSuccess: () => {
            applyFilter();
        }
    });
};

const columns = [
    { label: 'ID' },
    { label: 'Staff Name' },
    { label: 'Department Name' },
    { label: 'Date' },
    { label: 'Status' },
    { label: 'Attendance' },
    { label: 'Start Time' },
    { label: 'End Time' },
    { label: 'Late Minutes' },
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
                                <!-- All Staff Checkbox -->
                                <div class="pl-5 mt-4 col-md-3 d-flex align-items-center">
                                    <label for="allStaff" class="fancy-checkbox element-left">
                                        <Checkbox name="remember" id="allStaff" v-model:checked="form.all_staff" />
                                        <span class="text-dark font-weight-bold">All Staff</span>
                                    </label>
                                </div>

                                <!-- Department Dropdown -->
                                <div class="col-md-3">
                                    <InputLabel value="Departments" />
                                    <select class="form-control" v-model="form.department_id"
                                        :disabled="form.all_staff">
                                        <option value="">Select Department</option>
                                        <option v-for="dep in departments" :key="dep.id" :value="dep.id">
                                            {{ dep.DepartmentName }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Date Filter -->
                                <div class="col-md-3">
                                    <InputLabel value="Date" />
                                    <input type="date" class="form-control" v-model="form.date" :max="todayDate"
                                        required @change="handleDateChange" />
                                </div>

                                <!-- Filter Button -->
                                <div class="col-md-3 d-flex align-items-end">
                                    <PrimaryButton class="w-100" type="submit">Filter</PrimaryButton>
                                </div>
                            </div>
                        </form>

                        <!-- Attendance Form -->
                        <form @submit.prevent="submitForm" v-if="staffs.length > 0">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Staff Attendance</h5>
                                    <small class="text-muted">
                                        Date: {{ form.date }} | Total Staff: {{ staffs.length }}
                                    </small>
                                </div>

                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table mb-0 text-center table-hover table-bordered">
                                            <TableHeader :columns="columns" />
                                            <tbody>
                                                <tr v-for="(staff, index) in staffs" :key="staff.id">
                                                    <td>{{ index + 1 }}</td>
                                                    <td>{{ staff?.FirstName }} {{ staff?.LastName }}</td>
                                                    <td>{{ staff?.department_rel?.DepartmentName || 'N/A' }}</td>
                                                    <td>
                                                        <span class="text-white badge bg-success"
                                                            v-if="Object.keys(existingAttendance).includes(String(staff.id)) === true">
                                                            {{ attendance_date ?? '' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <span v-if="staff.IsActive == 1"
                                                            class="text-white badge bg-success">Active</span>
                                                        <span v-else
                                                            class="text-white badge bg-secondary">Inactive</span>
                                                    </td>
                                                    <td>
                                                        <select class="form-control form-control-sm"
                                                            v-model="attendanceData[staff.id]" style="width: 120px;">
                                                            <option value="1">Present</option>
                                                            <option value="0">Absent</option>
                                                            <option value="2">Leave</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="time" class="form-control form-control-sm"
                                                            v-model="startTimeData[staff.id]" style="width: 130px;"
                                                            :required="attendanceData[staff.id] === '1'"
                                                            :disabled="attendanceData[staff.id] !== '1'" />
                                                    </td>
                                                    <td>
                                                        <input type="time" class="form-control form-control-sm"
                                                            v-model="endTimeData[staff.id]" style="width: 130px;"
                                                            :required="attendanceData[staff.id] === '1'"
                                                            :disabled="attendanceData[staff.id] !== '1'" />
                                                    </td>
                                                    <td>
                                                        <span v-if="lateMinutesData[staff.id] > 0" class="text-danger">
                                                            {{ lateMinutesData[staff.id] }} min
                                                        </span>
                                                        <span v-else class="text-muted">-</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 text-end">
                                <PrimaryButton type="submit" class="px-4">
                                    Submit Attendance
                                </PrimaryButton>
                            </div>
                        </form>

                        <!-- No Staff Message -->
                        <div v-else class="mt-4 alert">
                            <h5>No Staff Found Select Filter</h5>
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
