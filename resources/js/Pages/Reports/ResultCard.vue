<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, Head } from '@inertiajs/vue3';
import 'vue-multiselect/dist/vue-multiselect.min.css'
import {  computed } from 'vue';
import { useToast } from 'vue-toastification';

const toast = useToast();

const props = defineProps({
    ExamStudents: {
        type: Object,
        default: () => ({})
    },
    Session: {
        type: Object,
        default: () => ({})
    },
    Grads: {
        type: Object,
        default: () => ({})
    },
    ResultData: {
        type: Array,
        default: () => []
    },
    ExamTypes: {
        type: Array,
        default: () => []
    },
    AllTerms: {
        type: Boolean,
        default: false
    }
});

const form = useForm({
    examtermid: '',
    student: props.ExamStudents,
    session: props.Session,
    examMarks: props.ExamStudents?.student?.exam_marks_details,
    classid: '',
    studentid: '',
});

const getPercentage = (obtained, max) => {
    if (!max || max === 0) return 0;
    return Math.round((obtained / max) * 100);
};

const getGrade = (percentage) => {
    if (!props.Grads?.length) return '-';
    const grade = props.Grads.find(
        (g) => percentage >= g.PercentFrom && percentage <= g.PercentUpt
    );
    return grade ? grade.GradeName : 'F';
};

// Totals for old format
const getTotalMax = (examMarks) => {
    if (!examMarks) return 0;
    return examMarks.reduce((sum, marks) => sum + (marks.exam_marks?.exam_subject?.MarksMax ?? 0), 0);
};

const getTotalObtained = (examMarks) => {
    if (!examMarks) return 0;
    return examMarks.reduce((sum, marks) => sum + (marks.Marks ?? 0), 0);
};

// Computed properties for summary section - works with both formats
const summaryTotalMarks = computed(() => {
    if (props.ResultData && props.ResultData.length > 0) {
        const totalRow = props.ResultData.find(row => row.Subject === 'Total');
        return totalRow ? totalRow['Total Marks'] : 0;
    }
    return getTotalMax(form.examMarks);
});

const summaryObtainedMarks = computed(() => {
    if (props.ResultData && props.ResultData.length > 0) {
        const totalRow = props.ResultData.find(row => row.Subject === 'Total');
        return totalRow ? totalRow['Obtained Marks'] : 0;
    }
    return getTotalObtained(form.examMarks);
});

const summaryPercentage = computed(() => {
    if (props.ResultData && props.ResultData.length > 0) {
        const totalRow = props.ResultData.find(row => row.Subject === 'Total');
        if (totalRow && totalRow.Percentage) {
            return totalRow.Percentage.replace('%', '');
        }
    }
    return getPercentage(summaryObtainedMarks.value, summaryTotalMarks.value);
});

const summaryGrade = computed(() => {
    if (props.ResultData && props.ResultData.length > 0) {
        const totalRow = props.ResultData.find(row => row.Subject === 'Total');
        return totalRow ? totalRow.Grade : '-';
    }
    return getGrade(summaryPercentage.value);
});

</script>

<template>

    <Head title="Create Exam Marks" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Result Card</h2>
        </template>

        <div class="container">
            <div class="mt-5 col-md-12">
                <div class="mb-5 row align-items-center">
                    <div class="text-center logo_wrapper">
                        <img src="/assets/images/Logo.jpeg" width="100px" alt="School Logo">
                    </div>
                    <div class="text-left header_content">
                        <div class="pl-5 maintxt font-weight-bold">FORCES SCHOOL AND COLLEGE SYSTEM</div>
                        <div class="assementtxt font24 pl100">Assessment Report Card</div>
                        <div class="sessiontxt font24 font-weight-bold pl100">Session:<span
                                class="ml-2 text-center borderBot font-weight-light session_txt">{{
                                form.session?.start_date }}
                                - {{ form.session?.end_date }}</span></div>
                    </div>
                </div>

                <div class="mb-5 std_details_wrapper">
                    <div class="mb-5 form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="std_heading font-weight-bold">Student Name:</div>
                                <div class="text-center std-text stdname_txt"> <span>{{
                                    props.ExamStudents?.student?.FirstName
                                        }} - {{ props.ExamStudents?.student?.LastName }}</span></div>
                            </div>
                            <div class="col-md-6">
                                <div class="std_heading font-weight-bold">Father's Name:</div>
                                <div class="text-center std-text stdfather_txt"> <span></span>{{
                                    props.ExamStudents?.student?.FatherName }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-5 form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="std_heading font-weight-bold">Class: </div>
                                <div class="text-center std-text stdclass_txt"> <span> {{
                                    props.ExamStudents?.subject?.class?.ClassName }}</span></div>
                            </div>
                            <div class="col-md-6">
                                <div class="std_heading font-weight-bold">Class Teacher: </div>
                                <div class="text-center std-text stdteacher_txt"> <span>{{
                                    props.ExamStudents?.subject?.class?.assigned_teacher?.staff_rel?.FirstName
                                        }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-5 form-group">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="std_heading font-weight-bold">Attendance: </div>
                                <div class="text-center attendance d-flex justify-content-around"> <span
                                        class="borderBot stdobtain_txt"></span> <span>of</span> <span
                                        class="borderBot stdtotal_txt"></span> </div>
                            </div>
                            <div class="col-md-4">
                                <div class="std_heading font-weight-bold">Position: </div>
                                <div class="text-center std-text stdposition_txt"> <span></span></div>
                            </div>
                            <div class="col-md-4">
                                <div class="std_heading font-weight-bold">Date: </div>
                                <div class="text-center std-text stddate_txt">
                                    <span>
                                        {{ new
                                            Date(props.ExamStudents?.subject?.exam_type?.created_at).toLocaleDateString('en-GB',
                                                {
                                                    day: '2-digit', month: 'long', year: 'numeric'
                                        }) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <div class="mb-5 subject_tbl">
                    <table class="table table-bordered">
                        <thead class="thead-green">
                            <tr>
                                <th>Subject</th>

                                <template v-if="AllTerms">
                                    <template v-for="exam in ExamTypes" :key="exam.id">
                                        <th colspan="2" class="text-center">{{ exam.ExamName }}</th>
                                    </template>
                                </template>

                                <th>Total Marks</th>
                                <th>Obtained Marks</th>
                                <th>Percentage</th>
                                <th>Grade</th>
                            </tr>

                            <!-- Sub-header for checkpoint columns -->
                            <tr v-if="AllTerms">
                                <th></th>
                                <template v-for="exam in ExamTypes" :key="exam.id">
                                    <th>Total</th>
                                    <th>Obt</th>
                                </template>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody style="font-size:16px">
                            <tr v-for="(row, index) in ResultData" :key="index">
                                <td class="column_style" :class="{ 'thead-green text-white': row.Subject === 'Total' }">{{ row.Subject }}</td>
                                
                                <template v-if="AllTerms">
                                    <template v-for="exam in ExamTypes" :key="exam.id">
                                        <td class="column_style">{{ row[exam.ExamName + ' Total'] || 0 }}</td>
                                        <td class="column_style">{{ row[exam.ExamName + ' Obt'] || 0 }}</td>
                                    </template>
                                </template>

                                <td class="column_style">{{ row['Total Marks'] }}</td>
                                <td class="column_style">{{ row['Obtained Marks'] }}</td>
                                <td class="column_style">{{ row.Percentage }}</td>
                                <td class="column_style">{{ row.Grade }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>



                <div class="mb-5 std_details_wrapper marks_section">
                    <div class="mb-5 form-group">
                    <div class="mb-4 row">
                        <div class="col-md-3">
                            <div class="std_heading font-weight-bold">Total Marks: </div>
                            <div class="text-center std-text totalmarks_txt">
                                <span>{{ summaryTotalMarks }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="std_heading font-weight-bold">Obtained Marks: </div>
                            <div class="text-center std-text obtainedmarks_txt">
                                <span>{{ summaryObtainedMarks }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="std_heading font-weight-bold">Percentage: </div>
                            <div class="text-center std-text percntage_txt">
                                <span>{{ summaryPercentage }}%</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="std_heading font-weight-bold">Grade: </div>
                            <div class="text-center std-text grade_txt">
                                <span>{{ summaryGrade }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                    
                    <div class="mb-5 form-group">
                        <div class="mb-5 row">
                            <div class="col-md-12">
                                <div class="std_heading ctr_txt font-weight-bold">Class Teacher’s Remarks: </div>
                                <div class="text-left std-text remark_txt tacherremarks_txt"> <span></span></div>
                            </div>
                        </div>
                        <div class="borderBot"></div>
                    </div>
                    <div class="mb-5 form-group">
                        <div class="mb-5 row">
                            <div class="col-md-12">
                                <div class="std_heading ctr_txt font-weight-bold">Principal’s Remarks (if any): </div>
                                <div class="text-left std-text remark_txt princpleremarks_txt"> <span></span></div>
                            </div>
                        </div>
                        <div class="borderBot"></div>
                    </div>
                </div>

                
                <div class="mb-5 text-center distinct_chart">
                    <h4 class="font-weight-bold borderBot">Distinct Learning Programs Grading Chart</h4>
                </div>
                <div class="mb-2 tafheem_wraper">
                    <div class="chartTbl_wrapper">
                        <div class="chart_tbl">
                            <table class="table table-bordered" id="tafheem_tbl">
                                <thead class="thead-green">
                                    <tr>
                                        <th>Tafheem-e-Deen</th>
                                        <th>A</th>
                                        <th>B</th>
                                        <th>C</th>
                                    </tr>
                                </thead>
                                <tbody class="text-left">
                                    <tr>
                                        <td>Memorization of Asma-ul-Husna</td>
                                        <td><span class="tbl_checkbox"></span></td>
                                        <td><span class="tbl_checkbox"></span></td>
                                        <td><span class="tbl_checkbox"></span></td>
                                    </tr>
                                    <tr>
                                        <td>Memorization of Dua(s)</td>
                                        <td><span class="tbl_checkbox"></span></td>
                                        <td><span class="tbl_checkbox"></span></td>
                                        <td><span class="tbl_checkbox"></span></td>
                                    </tr>
                                    <tr>
                                        <td>Answer to Questions</td>
                                        <td><span class="tbl_checkbox"></span></td>
                                        <td><span class="tbl_checkbox"></span></td>
                                        <td><span class="tbl_checkbox"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="chart_grade">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td>Excellent</td>
                                        <td>A</td>
                                    </tr>
                                    <tr>
                                        <td>Good</td>
                                        <td>B</td>
                                    </tr>
                                    <tr>
                                        <td>Fair</td>
                                        <td>C</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="mb-2 development_wraper">
                    <div class="chartTbl_wrapper">
                        <div class="chart_tbl">
                            <table class="table table-bordered" id="development_tbl">
                                <thead class="thead-green">
                                    <tr>
                                        <th>Personality Development</th>
                                        <th>A</th>
                                        <th>B</th>
                                        <th>C</th>
                                    </tr>
                                </thead>
                                <tbody class="text-left">
                                    <tr>
                                        <td>Follow directions</td>
                                        <td><span class="tbl_checkbox"></span></td>
                                        <td><span class="tbl_checkbox"></span></td>
                                        <td><span class="tbl_checkbox"></span></td>
                                    </tr>
                                    <tr>
                                        <td>Practices what they’ve learnt</td>
                                        <td><span class="tbl_checkbox"></span></td>
                                        <td><span class="tbl_checkbox"></span></td>
                                        <td><span class="tbl_checkbox"></span></td>
                                    </tr>
                                    <tr>
                                        <td>Social Interaction with peers and others</td>
                                        <td><span class="tbl_checkbox"></span></td>
                                        <td><span class="tbl_checkbox"></span></td>
                                        <td><span class="tbl_checkbox"></span></td>
                                    </tr>
                                    <tr>
                                        <td>Participation in activities</td>
                                        <td><span class="tbl_checkbox"></span></td>
                                        <td><span class="tbl_checkbox"></span></td>
                                        <td><span class="tbl_checkbox"></span></td>
                                    </tr>
                                    <tr>
                                        <td>Reflects the learning</td>
                                        <td><span class="tbl_checkbox"></span></td>
                                        <td><span class="tbl_checkbox"></span></td>
                                        <td><span class="tbl_checkbox"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="chart_grade">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td>Excellent</td>
                                        <td>A</td>
                                    </tr>
                                    <tr>
                                        <td>Good</td>
                                        <td>B</td>
                                    </tr>
                                    <tr>
                                        <td>Fair</td>
                                        <td>C</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="mb-2 moral_wraper">
                    <div class="chartTbl_wrapper">
                        <div class="chart_tbl">
                            <table class="table table-bordered" id="moral_tbl">
                                <thead class="thead-green">
                                    <tr>
                                        <th>Moral Science</th>
                                        <th>A</th>
                                        <th>B</th>
                                        <th>C</th>
                                    </tr>
                                </thead>
                                <tbody class="text-left">
                                    <tr>
                                        <td>Follow directions</td>
                                        <td><span class="tbl_checkbox"></span></td>
                                        <td><span class="tbl_checkbox"></span></td>
                                        <td><span class="tbl_checkbox"></span></td>
                                    </tr>
                                    <tr>
                                        <td>Works independently</td>
                                        <td><span class="tbl_checkbox"></span></td>
                                        <td><span class="tbl_checkbox"></span></td>
                                        <td><span class="tbl_checkbox"></span></td>
                                    </tr>
                                    <tr>
                                        <td>Practices what they’ve learnt</td>
                                        <td><span class="tbl_checkbox"></span></td>
                                        <td><span class="tbl_checkbox"></span></td>
                                        <td><span class="tbl_checkbox"></span></td>
                                    </tr>
                                    <tr>
                                        <td>Reflects the learning</td>
                                        <td><span class="tbl_checkbox"></span></td>
                                        <td><span class="tbl_checkbox"></span></td>
                                        <td><span class="tbl_checkbox"></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="chart_grade">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td>Excellent</td>
                                        <td>A</td>
                                    </tr>
                                    <tr>
                                        <td>Good</td>
                                        <td>B</td>
                                    </tr>
                                    <tr>
                                        <td>Fair</td>
                                        <td>C</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="mb-2 grade_wraper">
                    <div class="chartTbl_wrapper">
                        <div class="chart_tbl">
                            <table class="table table-bordered" id="grade_tbl">
                                <thead>
                                    <tr>
                                        <th>Percentage</th>
                                        <th>Grade</th>
                                        <th>Grade Equivalent</th>
                                    </tr>
                                </thead>
                                <tbody class="text-left">
                                    <tr v-for="Grad in Grads">
                                        <td>{{ Grad?.PercentFrom }} - {{ Grad?.PercentUpt }}</td>
                                        <td> {{ Grad?.GradeName }}</td>
                                        <td> {{ Grad?.Description ?? 'N/A' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="signature_wrapper" style="margin-top:60px">
                    <div class="row">
                        <div class="text-center col-md-6">
                            <div class="mb-2 signature_border"></div>
                            <div class="signature_txt">Class Teacher’s Signature</div>
                        </div>
                        <div class="text-center col-md-6">
                            <div class="mb-2 signature_border"></div>
                            <div class="signature_txt">Principal’s Signature</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


<style scoped>

.column_style {
    width:100px;
}

body {
    font-family: Cambria, Cochin, Georgia, Times, 'Times New Roman', serif;
    color: #385623;
}

.logo_wrapper {
    width: 20%;
}

.header_content {
    width: 75%;
}

.maintxt {
    font-size: 30px;
}

.font24 {
    font-size: 24px;
}

.pl100 {
    padding-left: 100px;
}

.std_heading {
    width: 25%;
}

.std-text {
    width: 75%;
    max-width: 100%;
    overflow: hidden;
    border-bottom: 1px solid #385623;
}

.std_details_wrapper .col-md-4,
.std_details_wrapper .col-md-6 {
    display: inline-flex;
}

.attendance {
    width: 100%;
}

.sessiontxt .borderBot {
    display: inline-block;
    width: 50%;
}

.borderBot {
    border-bottom: 1px solid #385623;
    display: inline-block;
}

.std_details_wrapper .borderBot {
    width: 42%;
}

.thead-green {
    background: #385623;
    color: #ffffff
}

.table-bordered thead td,
.table-bordered thead th {
    color: #ffffff
}

.table-bordered td,
.table-bordered th {
    text-align: center;
    border: 2px solid #385623;
    font-weight: 700;
    color: #385623;
}

.table thead th {
    border-bottom: 2px solid #385623;
}

.marks_section .std_heading {
    width: 50%;
}

.marks_section .col-md-12,
.marks_section .col-md-3 {
    display: inline-flex;
}

.marks_section .std-text {
    width: 50%;
}

.marks_section .ctr_txt {
    width: 25%;
}

.marks_section .remark_txt {
    width: 80%;
}

.marks_section .borderBot {
    border-bottom: 1px solid #385623;
    width: 100%;
    display: inline-block;
}

.distinct_chart .borderBot {
    border-bottom-width: 3px;
}

.distinct_chart {
    position: relative;
}

.distinct_chart:before {
    content: '';
    display: block;
    position: absolute;
    right: 0;
    top: 0;
    width: 0;
    height: 0;
    border-style: solid;
    border-width: 0 100px 50px 0;
    border-color: transparent #385623 transparent transparent;
}

.distinct_chart:after {
    content: '';
    display: block;
    position: absolute;
    left: 0;
    top: 0;
    width: 0;
    height: 0;
    border-style: solid;
    border-width: 50px 100px 0 0;
    border-color: #385623 transparent transparent transparent;
}

.chartTbl_wrapper {
    justify-content: space-between;
    width: 100%;
    display: flex;
    align-items: center;
}

.chart_tbl {
    width: 75%;
}

.chart_grade {
    width: 20%;
}

.grade_wraper .chart_tbl {
    width: 95%;
}

#grade_tbl.table-bordered td:first-child,
#grade_tbl.table-bordered td:last-child,
#moral_tbl.table-bordered td:first-child,
#development_tbl.table-bordered td:first-child,
#tafheem_tbl.table-bordered td:first-child {
    text-align: left;
}

span.tbl_checkbox {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 1px solid #385623;
}

#grade_tbl.table-bordered thead th {
    color: #385623;
}

.signature_txt {
    font-weight: 700;
    font-size: 18px;
    text-align: center;
}

.signature_txt::before {
    content: '';
    display: block;
}

.signature_border {
    border-bottom: 1px solid #385623;
    width: 70%;
    text-align: center;
    display: inline-block;
}

.color-white {
    color: #fff !important;
}

.mb-5 {
    margin-bottom: 2.5rem !important;
}

.table td,
.table th {
    padding: 0.5rem;
}

@media print {
    body {
        -webkit-print-color-adjust: exact;
        page-break-after: always;
    }

    .thead-green {
        background-color: #385623 !important;
    }

    .distinct_chart {
        margin-top: 1em !important;
        print-color-adjust: exact;
    }

    .table thead th {
        border-bottom: 2px solid #385623 !important;
        print-color-adjust: exact;
    }

    .table thead {
        font-size: 14px !important;
        print-color-adjust: exact;
    }

    .table-bordered td,
    .table-bordered th {
        text-align: center;
        border: 2px solid #385623 !important;
        font-weight: 700;
        color: #385623 !important;
        print-color-adjust: exact;
    }

    thead.thead-green {
        background: #385623 !important;
        color: #ffffff !important;
        print-color-adjust: exact;
    }

    .table thead th {
        background: #385623 !important;
        color: #ffffff !important;
        print-color-adjust: exact;
    }

    td.color-white {
        background: #385623 !important;
        color: #ffffff !important;
        print-color-adjust: exact;
    }

    .std_details_wrapper.marks_section {
        page-break-after: always;
    }

    .marks_section .std_heading {
        width: 70% !important;
        print-color-adjust: exact;
    }

    #grade_tbl thead th {
        background: #fff !important;
        color: #385623 !important;
        print-color-adjust: exact;
    }

    .marks_section .ctr_txt {
        width: 25% !important;
        print-color-adjust: exact;
    }

    .subject_tbl {
        min-height: 600px !important;
        print-color-adjust: exact;
    }

    .subject_tbl .table td,
    .subject_tbl .table th {
        padding: 1rem;
        print-color-adjust: exact;
    }

    .signature_wrapper {
        page-break-after: avoid;
    }
}
</style>