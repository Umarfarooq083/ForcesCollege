<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted, computed, ref, watch } from 'vue';
import axios from "axios";

let props = defineProps({
    ClassesStudents: Array,
    StudentsInquireis: Object,
    TeacherAttendance: Object,
    FeesData: Object,
});

const today = new Date().toISOString().slice(0, 10);
let selectedYear = ref(props.StudentsInquireis.year);
let selectedMonth = ref(new Date().toISOString().slice(0, 7));
const teacherAttendanceData = ref(props.TeacherAttendance.teachers_attendance || []);
let selectedDate = ref(today);
let selectedDateForTable = ref(today);
let studentAttendanceDate = ref(today);
let attendanceStatus = ref('');

let feesChart = null;
let attendanceChart = null;
let studentChart = null;

const totalStudents = computed(() => {
    return props.ClassesStudents.reduce((sum, item) => sum + item.students_count, 0);
});

// Chart render function same rahega
const renderMonthlyComparisonChart = (studentData, inquiryData) => {
    // console.log(studentData, inquiryData);
    if (!Array.isArray(studentData) || !Array.isArray(inquiryData)) return;

    const months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun",
        "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

    const studentCounts = ["Students", ...studentData];
    const inquiryCounts = ["Inquiries", ...inquiryData];

    if (window.monthlyComparisonChart) window.monthlyComparisonChart.destroy();

    window.monthlyComparisonChart = c3.generate({
        bindto: "#monthly-comparison",
        data: {
            columns: [studentCounts, inquiryCounts],
            type: 'line',
            colors: { Students: '#10b981', Inquiries: '#3b82f6' },
            labels: true
        },
        axis: {
            x: { type: 'category', categories: months },
            y: {
                label: 'Count',
                min: 0,
                padding: { top: 30 },
                tick: { format: d3.format('d') }
            }
        },
        grid: { y: { show: true }, x: { show: true } },
        point: { r: 4 },
        tooltip: {
            position: function (data, width, height, element) {
                return { top: 0, left: document.getElementById('monthly-comparison').offsetWidth - width - 10 };
            },
            format: {
                title: (d) => months[d],
                value: (value, ratio, id) => `${value} ${id}`
            }
        },
        padding: { top: 20, right: 40, bottom: 20, left: 60 }
    });
};

// paid and unpaid fee chart
const renderFeesChart = (data) => {

    if (!data || (!data.partial_paid && !data.fully_paid && !data.unpaid_fee)) {
        if (feesChart) feesChart.destroy();
        feesChart = c3.generate({
            bindto: "#feesChart",
            data: {
                columns: [["No Data", 1]],
                type: "pie",
                colors: { "No Data": "#d1d5db" }
            },
            legend: { show: false }
        });
        return;
    }

    if (feesChart) feesChart.destroy();

    const total = (data.partial_paid || 0) + (data.fully_paid || 0) + (data.unpaid_fee || 0);

    let columns;
    let colors;

    if (total === 0) {
        columns = [
            ["No Data", 1],
            ["__empty", 0.0001]
        ];
        colors = {
            "No Data": "#d1d5db",
            "__empty": "transparent"
        };
    } else {
        columns = [
            ["Partial Paid", data.partial_paid || 0],
            ["Fully Paid", data.fully_paid || 0],
            ["Unpaid Fee", data.unpaid_fee || 0],
        ];
        colors = {
            "Partial Paid": "#f59e0b",
            "Fully Paid": "#10b981",
            "Unpaid Fee": "#ef4444",
        };
    }

    feesChart = c3.generate({
        bindto: "#feesChart",
        data: {
            columns: columns,
            type: "pie",
            colors: colors
        },
        pie: {
            label: {
                format: (value, ratio, id) => {
                    if (id === "__empty") return "";
                    return id === "No Data" ? "No Data" : value;
                }
            }
        },
        legend: {
            hide: ["__empty"]
        },
        padding: {
            top: 20, right: 20, bottom: 20, left: 20,
        }
    });
};

// Function to render teacher attendance chart
const renderAttendanceChart = (data) => {
    if (attendanceChart) attendanceChart.destroy();

    attendanceChart = c3.generate({
        bindto: "#teacher_attendance",
        data: {
            columns: [
                ['Total Teachers', data.total],
                ['Present', data.present],
                ['Absent', data.absent],
                ['On Leave', data.leave],
            ],
            type: "bar",
            colors: {
                'Total Teachers': '#3b82f6',
                'Present': '#10b981',
                'Absent': '#ef4444',
                'On Leave': '#f59e0b',
            },
            labels: { format: (v) => v }
        },
        axis: {
            x: {
                type: 'category',
                categories: ['Total', 'Present', 'Absent', 'On Leave']
            },
            y: {
                label: 'Teachers Count',
                tick: {
                    format: d3.format('d'),
                    values: (() => {
                        const max = data.total || 10;
                        return Array.from({ length: max + 1 }, (_, i) => i);
                    })()
                },
                max: data.total,
                min: 0
            }
        },
        bar: { width: { ratio: 0.5 } },
        legend: { show: true, position: 'bottom' },
        grid: { y: { show: true } },
        padding: {
            top: 20,
            right: 120,
            bottom: 20,
            left: 60,
        },

        // ✅ Tooltip fix
        tooltip: {
            position: function (data, width, height, element) {
                const chartWidth = document.getElementById('teacher_attendance').offsetWidth;
                return {
                    top: 10,
                    left: chartWidth - width - chartWidth * 0.2 // 20% margin from right
                };
            },
            contents: function (d, defaultTitleFormat, defaultValueFormat, color) {
                let total = 0, present = 0, absent = 0, leave = 0;

                d.forEach(item => {
                    if (item.id === 'Total Teachers') total = item.value;
                    if (item.id === 'Present') present = item.value;
                    if (item.id === 'Absent') absent = item.value;
                    if (item.id === 'On Leave') leave = item.value;
                });

                return `
                    <div style="background:#000000; color:white; padding:8px 12px; border:1px solid #ddd; border-radius:6px; font-size:14px; line-height:1.4; box-shadow:0 2px 6px rgba(0,0,0,0.15)">
                        <div><strong>Total:</strong> ${total}</div>
                        <div><strong>Present:</strong> ${present}</div>
                        <div><strong>Absent:</strong> ${absent}</div>
                        <div><strong>On Leave:</strong> ${leave}</div>
                    </div>
                `;
            }
        }
    });
};

const renderStudentAttendanceChart = (data) => {
    if (!Array.isArray(data) || data.length === 0) {
        if (studentChart) studentChart.destroy();
        studentChart = c3.generate({
            bindto: '#std_classes',
            data: {
                columns: [['No Data', 1]],
                type: 'bar',
                colors: { 'No Data': '#d1d5db' },
                labels: true
            },
            axis: {
                x: { type: 'category', categories: [''] },
                y: { show: false }
            }, tooltip: { show: false },
            legend: { show: false }
        });
        document.getElementById('student-attendance-summary').innerHTML = '<p class="text-sm text-gray-500">No Data</p>';
        return;
    }

    // Prepare chart data
    const categories = data.map(item => item.class).concat(['Total']);
    const totalData = ['Total'].concat(data.map(item => item.total || 0));
    const presentData = ['Present'].concat(data.map(item => item.present || 0));
    const absentLeaveData = ['Absent+Leave'].concat(data.map(item => item.absent_leave || 0));

    // Overall totals
    const overallTotal = totalData[totalData.length - 1];
    const overallPresent = presentData[presentData.length - 1];
    const overallAbsentLeave = absentLeaveData[absentLeaveData.length - 1];

    const yMax = Math.max(
        ...data.map(item => item.total || 0),
        overallTotal
    );

    if (studentChart) studentChart.destroy();

    studentChart = c3.generate({
        bindto: '#std_classes',
        data: {
            columns: [totalData, presentData, absentLeaveData],
            type: 'bar',
            colors: {
                Total: '#3b82f6',
                Present: '#10b981',
                'Absent+Leave': '#ef4444'
            },
            labels: true
        },
        axis: {
            x: { type: 'category', categories },
            y: {
                label: 'Students Count',
                min: 0,
                max: yMax,
                tick: {
                    format: d3.format('d'),
                    values: Array.from({ length: yMax + 1 }, (_, i) => i)
                }
            }
        },
        bar: { width: { ratio: 0.5 } },
        grid: { y: { show: true } },

        // ✅ fixed tooltip
        tooltip: {
            position: function (data, width, height, element) {
                const chartWidth = document.getElementById('std_classes').offsetWidth;
                return {
                    top: 10,
                    left: chartWidth - width - chartWidth * 0.2
                };
            },
            contents: function (d, defaultTitleFormat, defaultValueFormat, color) {
                let total = 0, present = 0, absent = 0;

                d.forEach(item => {
                    if (item.id === 'Total') total = item.value;
                    if (item.id === 'Present') present = item.value;
                    if (item.id === 'Absent+Leave') absent = item.value;
                });

                return `
                        <div style="background:#000000; color:white; padding:8px 12px; border:1px solid #ddd; border-radius:6px; font-size:14px; line-height:1.4; box-shadow:0 2px 6px rgba(0,0,0,0.15)">
                            <div> <strong> Summary </strong> </div>
                            <div><strong> <span class="total custom-teacher-tool"> </span> Total:</strong> ${total}</div>
                            <div><strong><span class="present custom-teacher-tool"> </span> Present:</strong> ${present}</div>
                            <div><strong><span class="absent custom-teacher-tool"> </span> Absent:</strong> ${absent}</div>
                        </div>
                    `;
            }
        }
    });
};

onMounted(() => {
    const categories = props.ClassesStudents.map(item => item.ClassName).concat(['Total']);
    const studentCounts = ['Students'].concat(
        props.ClassesStudents.map(item => item.students_count)).concat([totalStudents.value]);
    renderMonthlyComparisonChart(props.StudentsInquireis.students, props.StudentsInquireis.inquiries);
    renderStudentAttendanceChart(props.ClassesStudents);
    renderFeesChart(props.FeesData);
    renderAttendanceChart(props.TeacherAttendance);

});

// ✅ Watch for month change (fees chart)
watch(selectedMonth, async (newMonth) => {
    try {
        const response = await axios.get(route("dashboard.feesData"), {
            params: { month: newMonth }
        });
        renderFeesChart(response.data);
    } catch (error) {
        console.error("Error fetching fees data:", error);
    }
});

// ✅ Watch for date change (teacher attendance)
watch(selectedDate, async (newDate) => {
    try {
        const response = await axios.get(route("dashboard.staff.attendance"), {
            params: { date: newDate }
        });
        renderAttendanceChart(response.data);
    } catch (error) {
        console.error("Error fetching teacher attendance:", error);
    }
});


// status watcher
watch([attendanceStatus, selectedDateForTable], async ([status, date]) => {
    try {
        const response = await axios.get(route("dashboard.staff.table.filter"), {
            params: { status, date }
        });
        teacherAttendanceData.value = response.data.teachers_attendance;
    } catch (error) {
        console.error("Error fetching teacher attendance:", error);
    }
});

// ✅ Watch for date change (student attendance)
watch(studentAttendanceDate, async (newDate) => {
    try {
        const response = await axios.get(route("dashboard.student.attendance"), {
            params: { date: newDate }
        });
        renderStudentAttendanceChart(response.data);
    } catch (error) {
        console.error("Error fetching student attendance:", error);
    }
});


// Watch for year change
watch(selectedYear, async (newYear) => {
    try {
        const response = await axios.get(route("dashboard.students.inquiry"), {
            params: { year: newYear }
        });
        renderMonthlyComparisonChart(response.data.students, response.data.inquiries);
    } catch (error) {
        console.error("Error fetching student and inquiries:", error);
    }
});

</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-weight-bold text-dark">Dashboard</h2>
        </template>
        
        <div class="row">
            <div class="mb-4 col-md-12">
                <div class="card">
                    <div class="header">
                        <div class="w-100 d-inline-flex justify-content-between align-items-center">
                            <h5 class="mb-0 card-title">Students by Classes</h5>
                            <div class="d-flex align-items-center">
                                <label for="attendance-date" class="mb-0 mr-2 text-sm">Date:</label>
                                <input id="attendance-date" type="date" v-model="studentAttendanceDate" :max="today"
                                    class="form-control form-control-sm" style="width: auto;" />
                            </div>
                        </div>
                    </div>
                    <div class="body position-relative">
                        <div id="std_classes" style="height: 280px"></div>
                        <div id="student-attendance-summary" style="margin-right: 20%;" class="absolute top-2 right-2">
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-4 col-md-12">
                <div class="card">
                    <div class="header">
                        <div class="w-100 d-inline-flex justify-content-between align-items-center">
                            <h5 class="mb-0 card-title">Monthly Students vs Inquiries</h5>
                            <select v-model="selectedYear" class="col-md-2 w-auto form-select form-control">
                                <option v-for="y in [2018, 2019, 2020, 2021, 2022,2023, 2024, 2025,2026,2027]" :key="y" :value="y">
                                    {{ y }}
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="body position-relative">
                        <div id="monthly-comparison" style="height: 300px;"></div>
                    </div>
                </div>
            </div>

            <div class="mb-4 col-md-6">
                <div class="card">
                    <div class="header">
                        <div class="w-100 d-inline-flex justify-content-between align-items-center">
                            <h5 class="mb-0 card-title">Teacher Attendance</h5>
                            <div class="d-flex align-items-center">
                                <label for="attendance-date" class="mb-0 mr-2 text-sm">Date:</label>
                                <input id="attendance-date" type="date" v-model="selectedDate" :max="today"
                                    class="form-control form-control-sm" style="width: auto;" />
                            </div>
                        </div>
                    </div>
                    <div class="body position-relative">
                        <div id="teacher_attendance" style="height: 280px"></div>
                    </div>
                </div>
            </div>

            <div class="mb-4 col-md-6">
                <div class="card">
                    <div class="header">
                        <div class="w-100 d-inline-flex justify-content-between align-items-center">
                            <h5 class="mb-0 card-title">Monthly Fees Status</h5>
                            <div class="d-flex align-items-center">
                                <label for="fees-month" class="mb-0 mr-2 text-sm">Month:</label>
                                <input id="fees-month" type="month" v-model="selectedMonth"
                                    class="form-control form-control-sm" style="width: auto;" />
                            </div>
                        </div>
                    </div>
                    <div class="body position-relative">
                        <div id="feesChart" style="height: 280px"></div>
                    </div>
                </div>
            </div>

            <div class="mb-4 col-md-12">
                <div class="border-0 shadow-sm card">
                    <div class="text-dark card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Teacher Attendance</h5>
                        <div class="gap-2 d-flex align-items-center">
                            <select class="w-auto form-select form-select-sm" v-model="attendanceStatus">
                                <option value="" selected>Select All</option>
                                <option value="Present">Present</option>
                                <option value="Absent">Absent</option>
                                <option value="Leave">Leave</option>
                            </select>
                            <div class="gap-2 d-flex align-items-center">
                                <label for="attendance-date" class="mb-0 small">Date:</label>
                                <input id="attendance-date" type="date" v-model="selectedDateForTable" :max="today"
                                    class="form-control form-control-sm" />
                            </div>
                        </div>
                    </div>

                    <div class="p-0 card-body">
                        <!-- 👇 y-axis scroll added -->
                        <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                            <table class="table mb-0 text-center align-middle table-hover table-striped">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Designation</th>
                                        <th>Department</th>
                                        <th>Attendance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(attendance, index) in teacherAttendanceData" :key="attendance.id">
                                        <td>{{ index + 1 }}</td>
                                        <td>{{ attendance.staff.FirstName }} {{ attendance.staff.LastName }}</td>
                                        <td>{{ attendance.staff?.designation_rel?.DesignationName }}</td>
                                        <td>{{ attendance.staff.department_rel.DepartmentName }}</td>
                                        <td>
                                            <span class="text-white badge bg-primary" v-if="attendance.Attendance == 'Present'">Present</span>
                                            <span class="text-white badge bg-danger" v-else-if="attendance.Attendance == 'Absent'">Absent</span>
                                            <span class="text-white badge bg-warning" v-else-if="attendance.Attendance == 'Leave'">Leave</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </AuthenticatedLayout>
</template>


<style scoped>
.card {
    height: 100%;
}
</style>