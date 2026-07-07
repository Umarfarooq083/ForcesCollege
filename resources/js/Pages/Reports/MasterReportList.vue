<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm, Head } from '@inertiajs/vue3';
import { computed } from 'vue';
import { ref } from 'vue';
import axios from 'axios';

const loadingAdmission = ref(false);
const admissionInqueryValidationError = ref(null);
const StudentLedgerValidationError = ref(null);
const examsData = ref([]);
const examsClass = ref([]);

const props = defineProps({
    sections: Object,
    classes: Object,
    student: Object,
    examTermData: Object,
    lmsSessionData: Object,
});

const form = useForm({
    student_detail_report: {
        ClassId: '',
        SectionId: '',
        gender: '',
        rollno: '',
    },
    student_admission: {
        ClassId: '',
        SectionId: '',
        gender: '',
        rollno: '',
        start_date: '',
        end_date: '',
    },
    student_admission_inquiry: {
        ClassId: '',
        gender: '',
        start_date: '',
        end_date: '',
    },
    student_unpaid_fee: {
        ClassId: '',
        SectionId: '',
        student_id: '',
        month: '',
        year: '',
    },
    student_ledger_report: {
        ClassId: '',
        SectionId: '',
        student_id: '',
    },
    assessment_wise_report: {
        exam_term_id: '',
        session_id: '',
        exam_id: '',
        ClassId: '',
    },

    term_wise_report: {
        exam_term_id: '',
        session_id: '',
        exam_id: '',
        ClassId: '',
    },

    student_attendance_report: {
        SectionId: '',
        ClassId: '',
        month: '',
        year: '',
    },

    staff_attendance_report: {
        month: '',
        year: '',
    },
    fee_summary_head_wise_report: {
        month: '',
        year: '',
    },

    parent_profession_report: {
        profission: '',
    },
    student_withdraw_report: {
        ClassId: '',
        SectionId: '',
    },
    school_fee_ledger_report: {
        ClassId: '',
        SectionId: '',
        year: '',
        from_month: '',
        to_month: '',
        from_date: '',
        to_date: '',
    },

});

const gendarList = [
    { id: 1, name: 'Male' },
    { id: 2, name: 'Female' },
];

const SchoolFeeLedgerReport = () => {
    loadingAdmission.value = true;

    const data = form.school_fee_ledger_report;

    if (!data.from_date || !data.to_date) {
        alert('Year, From date and To date are required');
        loadingAdmission.value = false;
        return;
    }

    if (parseInt(data.from_date) > parseInt(data.to_date)) {
        alert('From date cannot be greater than To date');
        loadingAdmission.value = false;
        return;
    }

    const start_date = `${String(data.from_date).padStart(2, '0')}`;
    const end_date = `${String(data.to_date).padStart(2, '0')}`;

    // const start_date = `${data.year}-${String(data.from_date).padStart(2, '0')}-01`;
    // const end_date = `${data.year}-${String(data.to_date).padStart(2, '0')}-31`;

    axios({
        url: route('schoolfeeledger.report'),
        method: 'GET',
        params: {
            ClassId: data.ClassId,
            SectionId: data.SectionId,
            start_date: start_date,
            end_date: end_date,
        },
        responseType: 'blob',
    })
    // .then((response) => {
    //     const fileURL = window.URL.createObjectURL(
    //         new Blob([response.data], { type: "application/pdf" })
    //     );
    //     window.open(fileURL, "_blank");
    // })
    .then((response) => {
        const fileURL = window.URL.createObjectURL(
            new Blob([response.data], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" })
        );
        const link = document.createElement('a');
        link.href = fileURL;
        link.setAttribute('download', 'school-fee-ledger-report.xlsx'); // ✅ download hoga
        document.body.appendChild(link);
        link.click();
        link.remove();
    })
    .finally(() => {
        loadingAdmission.value = false;
    });
};

const filteredSections = computed(() => {
    return props.sections.filter(section => section.ClassId === form.student_detail_report.ClassId);
});

const filteredSectionsForAdmisison = computed(() => {
    return props.sections.filter(section => section.ClassId === form.student_admission.ClassId);
});

const filteredSectionsForUnpaidFee = computed(() => {
    return props.sections.filter(section => section.ClassId === form.student_unpaid_fee.ClassId);
});

const filteredSectionsForAttendanceReport = computed(() => {
    return props.sections.filter(section => section.ClassId === form.student_attendance_report.ClassId);
});

const filteredSectionsForWithdrawReport = computed(() => {
    return props.sections.filter(section => section.ClassId === form.student_withdraw_report.ClassId);
});

const filteredStudentSectionLedger = computed(() => {
    return props.sections.filter(section => section.ClassId === form.student_ledger_report.ClassId);
});

const filteredSchoolFeeSectionLedger = computed(() => {
    return props.sections.filter(section => section.ClassId === form.school_fee_ledger_report.ClassId);
});

const filteredStudent = computed(() => {

    return props.student.filter(student =>
        student.ClassId === form.student_unpaid_fee.ClassId &&
        student.SectionId === form.student_unpaid_fee.SectionId
    );
});

const filteredStudentLedger = computed(() => {

    return props.student.filter(student =>
        student.ClassId === form.student_ledger_report.ClassId &&
        student.SectionId === form.student_ledger_report.SectionId
    );
});

const studentDetailReport = () => {
    loadingAdmission.value = true;
    axios({
        url: route('studentdetail.reports'),
        method: 'GET',
        params: form.student_detail_report,
        responseType: 'blob',
    }).then((response) => {
        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', 'student-report.xlsx');
        document.body.appendChild(link);
        link.click();
    }).finally(() => {
        loadingAdmission.value = false;
    });
};

const studentAdmissionReport = () => {
    loadingAdmission.value = true;

    axios({
        url: route('studentadmission.report'),
        method: 'GET',
        params: form.student_admission,
        responseType: 'blob',
    })
        .then((response) => {
            const fileURL = window.URL.createObjectURL(
                new Blob([response.data], { type: "application/pdf" })
            );
            window.open(fileURL, "_blank");
        })
        .catch((error) => {
            console.error("PDF Error ", error);
        })
        .finally(() => {
            loadingAdmission.value = false;
        });
};

const studentAdmissionInquiryReport = () => {
    loadingAdmission.value = true;

    axios({
        url: route('studentadmissioninquiry.report'),
        method: 'GET',
        params: form.student_admission_inquiry,
        responseType: 'blob',
    })
        .then((response) => {
            const fileURL = window.URL.createObjectURL(
                new Blob([response.data], { type: "application/pdf" })
            );
            window.open(fileURL, "_blank");
        })
        .catch((error) => {
            admissionInqueryValidationError.value = "Please select at least one.";
        })
        .finally(() => {
            loadingAdmission.value = false;
        });
};

const studentUnPaidFee = () => {
    loadingAdmission.value = true;
    if (form.student_unpaid_fee.month == '' || form.student_unpaid_fee.year == '') {
        alert('Month And Year is required');
        loadingAdmission.value = false;
        return;
    }
    axios({
        url: route('studentunpaid.fee'),
        method: 'GET',
        params: form.student_unpaid_fee,
        responseType: 'blob',
    })
        .then((response) => {
            const fileURL = window.URL.createObjectURL(
                new Blob([response.data], { type: "application/pdf" })
            );
            window.open(fileURL, "_blank");
        })
        .catch((error) => {
            //  admissionInqueryValidationError.value = "Please select at least one.";
        })
        .finally(() => {
            loadingAdmission.value = false;
        });
};

const studentSummaryReport = () => {
    loadingAdmission.value = true;

    axios({
        url: route('studentsummary.report'),
        method: 'GET',
        responseType: 'blob',
    })
        .then((response) => {
            const fileURL = window.URL.createObjectURL(
                new Blob([response.data], { type: "application/pdf" })
            );
            window.open(fileURL, "_blank");
        })
        .catch((error) => {
            //  admissionInqueryValidationError.value = "Please select at least one.";
        })
        .finally(() => {
            loadingAdmission.value = false;
        });
};

const ContentFeedbackReport = () => {
    loadingAdmission.value = true;

    axios({
        url: route('contentfeedback.report'),
        method: 'GET',
        responseType: 'blob',
    })
        .then((response) => {
            const fileURL = window.URL.createObjectURL(
                new Blob([response.data], { type: "application/pdf" })
            );
            window.open(fileURL, "_blank");
        })
        .catch((error) => {
            //  admissionInqueryValidationError.value = "Please select at least one.";
        })
        .finally(() => {
            loadingAdmission.value = false;
        });
};

const getEmployeeRecord = () => {
    loadingAdmission.value = true;

    axios({
        url: route('employee.report'),
        method: 'GET',
        responseType: 'blob',
    })
        .then((response) => {
            const fileURL = window.URL.createObjectURL(
                new Blob([response.data], { type: "application/pdf" })
            );
            window.open(fileURL, "_blank");
        })
        .catch((error) => {
            //  admissionInqueryValidationError.value = "Please select at least one.";
        })
        .finally(() => {
            loadingAdmission.value = false;
        });
};

const studentLedgerSubmit = () => {
    loadingAdmission.value = true;
    if (!form.student_ledger_report.student_id) {
        StudentLedgerValidationError.value = "Student is required.";
        loadingAdmission.value = false;
        return;
    }
    axios({
        url: route('studentledger.report'),
        method: 'GET',
        params: form.student_ledger_report,
        responseType: 'blob',
    })
        .then((response) => {
            const fileURL = window.URL.createObjectURL(
                new Blob([response.data], { type: "application/pdf" })
            );
            window.open(fileURL, "_blank");
        })
        .catch((error) => {
            // StudentLedgerValidationError.value = "Please select at least Student.";
        })
        .finally(() => {
            loadingAdmission.value = false;
            StudentLedgerValidationError.value = null;
        });

};

const SelectSession = async (event) => {
    form.assessment_wise_report.ClassId = '';
    form.assessment_wise_report.ClassId = '';
    examsClass.value = [];
    if (!form.assessment_wise_report.exam_term_id) {
        alert('term id is required');
    }
    const response = await axios.get(route('assesmentexam.report', { exam_term_id: form.assessment_wise_report.exam_term_id, session_id: event }));
    examsData.value = response.data;

}

const SelectTermSession = async (event) => {
    form.term_wise_report.ClassId = '';
    examsClass.value = [];
    if (!form.term_wise_report.exam_term_id) {
        alert('term id is required');
    }
    const response = await axios.get(route('assesmentexam.report', { exam_term_id: form.term_wise_report.exam_term_id, session_id: event }));
    examsData.value = response.data;

}

const clearSession = () => {
    form.assessment_wise_report.session_id = '';
    form.assessment_wise_report.exam_id = '';
    form.assessment_wise_report.ClassId = '';
}

const fetchExamClass = async (event) => {
    if (!form.assessment_wise_report.exam_id) {
        alert('Exam id is required');
    }
    const response = await axios.get(route('assesmentexam.class', { exam_id: event, session_id: form.assessment_wise_report.session_id }));
    examsClass.value = response.data;

}

const fetchTermExamClass = async (event) => {
    if (!form.term_wise_report.exam_id) {
        alert('Exam id is required');
    }
    const response = await axios.get(route('assesmentexam.class', { exam_id: event, session_id: form.term_wise_report.session_id }));
    examsClass.value = response.data;

}

const fetchAssesmentWiseReport = () => {
    loadingAdmission.value = true;

    axios({
        url: route('assesmentreport.fetch'),
        method: 'GET',
        params: form.assessment_wise_report,
        responseType: 'blob',
    })
        .then((response) => {
            const fileURL = window.URL.createObjectURL(
                new Blob([response.data], { type: "application/pdf" })
            );
            window.open(fileURL, "_blank");
        })
        .catch((error) => {
            admissionInqueryValidationError.value = "Please select at least one.";
        })
        .finally(() => {
            loadingAdmission.value = false;
        });
};

const fetchTermWiseReport = () => {
    loadingAdmission.value = true;

    axios({
        url: route('termwise.fetch'),
        method: 'GET',
        params: form.term_wise_report,
        responseType: 'blob',
    })
        .then((response) => {
            const fileURL = window.URL.createObjectURL(
                new Blob([response.data], { type: "application/pdf" })
            );
            window.open(fileURL, "_blank");
        })
        .catch((error) => {
            admissionInqueryValidationError.value = "Please select at least one.";
        })
        .finally(() => {
            loadingAdmission.value = false;
        });
};

const ParentProfessionReport = () => {
    loadingAdmission.value = true;

    axios({
        url: route('parentprofession.report'),
        method: 'GET',
        params: form.parent_profession_report,
        responseType: 'blob',
    })
        .then((response) => {
            const fileURL = window.URL.createObjectURL(
                new Blob([response.data], { type: "application/pdf" })
            );
            window.open(fileURL, "_blank");
        })
        .catch((error) => {

        })
        .finally(() => {
            loadingAdmission.value = false;
        });
};

const StudentAttendenceReport = () => {
    loadingAdmission.value = true;
    if (!form.student_attendance_report.ClassId || form.student_attendance_report.SectionId == '' || form.student_attendance_report.month == '' || form.student_attendance_report.year == '') {
        alert('All Fields are required');
        loadingAdmission.value = false;
        return;
    }

    axios.get(route('studentattendance.report'), { params: form.student_attendance_report })
        .then((response) => {
            if (response.data.success) {
                const htmlBlob = new Blob([response.data.studentAtt], { type: "text/html" });
                const url = URL.createObjectURL(htmlBlob);
                window.open(url, "_blank");
            }
        })
        .catch((error) => {
            console.error(error);
        })
        .finally(() => {
            loadingAdmission.value = false;
        });
};

const StaffAttendanceReport = () => {
    loadingAdmission.value = true;
    if (form.staff_attendance_report.month == '' || form.staff_attendance_report.year == '') {
        alert('All Fields are required');
        loadingAdmission.value = false;
        return;
    }

    axios.get(route('staffattendance.report'), { params: form.staff_attendance_report })
        .then((response) => {
            if (response.data.success) {
                const htmlBlob = new Blob([response.data.staffAtt], { type: "text/html" });
                const url = URL.createObjectURL(htmlBlob);
                window.open(url, "_blank");
            }
        })
        .catch((error) => {
            console.error(error);
        })
        .finally(() => {
            loadingAdmission.value = false;
        });
};

const GetFeeSummaryHeadWise = () => {
    loadingAdmission.value = true;
    if (form.fee_summary_head_wise_report.month == '' || form.fee_summary_head_wise_report.year == '') {
        alert('All Fields are required');
        loadingAdmission.value = false;
        return;
    }

    axios({
        url: route('feesummaryheadwise.report'),
        method: 'GET',
        params: form.fee_summary_head_wise_report,
        responseType: 'blob',
    })
        .then((response) => {
            const fileURL = window.URL.createObjectURL(
                new Blob([response.data], { type: "application/pdf" })
            );
            window.open(fileURL, "_blank");
        })
        .catch((error) => {
            //  admissionInqueryValidationError.value = "Please select at least one.";
        })
        .finally(() => {
            loadingAdmission.value = false;
        });
};

const studentWithdrawReport = () => {
    if (!form.student_withdraw_report.ClassId) {
        alert('Please select a class');
        return;
    }

    loadingAdmission.value = true;
    axios({
        url: route('student.withdraw.report.fetch'),
        method: 'GET',
        params: form.student_withdraw_report,
        responseType: 'blob',
    })
        .then((response) => {
            const fileURL = window.URL.createObjectURL(
                new Blob([response.data], { type: "application/pdf" })
            );
            window.open(fileURL, "_blank");
        })
        .catch((error) => {
            console.error("PDF Error", error);
        })
        .finally(() => {
            loadingAdmission.value = false;
        });
};



// if you want to download pdf then uncomment this code and comment this function studentAdmissionReport

// const studentAdmissionReport = () => {
//     loadingAdmission.value = true;

//     axios({
//         url: route('studentadmission.report'),
//         method: 'GET',
//         params: form.student_admission,
//         responseType: 'blob',
//     })
//         .then((response) => {
//             const url = window.URL.createObjectURL(new Blob([response.data], { type: 'application/pdf' }));
//             const link = document.createElement('a');
//             link.href = url;
//             link.setAttribute('download', 'student-admission.pdf'); // <- triggers download
//             document.body.appendChild(link);
//             link.click();
//             document.body.removeChild(link);
//         })
//         .catch((error) => {
//             console.error('PDF Error', error);
//         })
//         .finally(() => {
//             loadingAdmission.value = false;
//         });
// };


</script>

<template>

    <Head title="Report List" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Reports Dashboard</h2>
        </template>

        <div class="container-fluid py-4">
            <div class="mb-4">
                <h3 class="h4 font-weight-bold text-dark mb-1">
                    <i class="fa fa-chart-bar text-primary mr-2"></i> Reports Dashboard
                </h3>
            </div>

            <h4>Students Detail Reports:</h4>
            <div class="row">
            <!-- Student Detail Report -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card cstm_shadow border-0 h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fa fa-graduation-cap text-primary fa-2x mr-3"></i>
                            <h5 class="card-title mb-0">Student Detail Report</h5>
                        </div>
                        <p class="text-muted small mb-3">
                            Filter by class, section, and date range.
                        </p>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <select class="form-control" v-model="form.student_detail_report.ClassId">
                                        <option value="">Select Class</option>
                                        <option v-for="classList in classes" :key="classList.id"
                                            :value="classList.id">{{
                                                classList.ClassName }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <select class="form-control" v-model="form.student_detail_report.SectionId">
                                        <option value="">Select Section</option>
                                        <option v-for="section in filteredSections" :key="section.id"
                                            :value="section.id">{{
                                                section?.SectionName }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <select class="form-control" v-model="form.student_detail_report.gender">
                                        <option selected disabled value="">Select a gender</option>
                                        <option v-for="gen in gendarList" :key="gen.name" :value="gen.name">
                                            {{ gen.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" v-model="form.student_detail_report.rollno"
                                        class="form-control" placeholder="Roll No" />
                                </div>
                            </div>

                            <div class="col-md-12">
                                <button class="btn btn-primary btn-block mt-auto" :disabled="loadingAdmission"
                                    @click="studentDetailReport">
                                    <span v-if="!loadingAdmission">
                                        <i class="fa fa-chart-line mr-1"></i> Get Student Details
                                    </span>

                                    <span v-else>
                                        <i class="fa fa-spinner fa-spin mr-1"></i> Loading...
                                    </span>
                                </button>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Fee Student Admission Report -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card cstm_shadow border-0 h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fa fa-university text-dark fa-2x mr-3"></i>
                            <h5 class="card-title mb-0">Student Admission</h5>
                        </div>
                        <p class="text-muted small mb-3">
                            Check unpaid or head-wise fee reports.
                        </p>
                        <div class="row">


                            <div class="col-md-6">
                                <div class="form-group">
                                    <select class="form-control" v-model="form.student_admission.ClassId">
                                        <option value="">Select Class</option>
                                        <option v-for="classList in classes" :key="classList.id"
                                            :value="classList.id">{{
                                                classList.ClassName }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <select class="form-control" v-model="form.student_admission.SectionId">
                                        <option value="">Select Section</option>
                                        <option v-for="section in filteredSectionsForAdmisison" :key="section.id"
                                            :value="section.id">{{ section?.SectionName }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <select class="form-control" v-model="form.student_admission.gender">
                                        <option selected disabled value="">Select a gender</option>
                                        <option v-for="gen in gendarList" :key="gen.name" :value="gen.name">
                                            {{ gen.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" v-model="form.student_admission.rollno" class="form-control"
                                        placeholder="Roll No" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="date" class="form-control"
                                        v-model="form.student_admission.start_date" placeholder="Start Date" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="date" class="form-control"
                                        v-model="form.student_admission.end_date" placeholder="End Date" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button class="btn btn-primary btn-block mt-auto" :disabled="loadingAdmission"
                                    @click="studentAdmissionReport">

                                    <span v-if="!loadingAdmission">
                                        <i class="fa fa-chart-line mr-1"></i> Get Student Admission
                                    </span>

                                    <span v-else>
                                        <i class="fa fa-spinner fa-spin mr-1"></i> Loading...
                                    </span>

                                </button>

                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Admission Enquiry Report -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card cstm_shadow border-0 h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fa fa-file-text text-warning fa-2x mr-3"></i>
                            <h5 class="card-title mb-0">Admission Enquiry</h5>
                        </div>
                        <p class="text-muted small mb-3">
                            Check unpaid or head-wise fee reports.
                        </p>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <select class="form-control" v-model="form.student_admission_inquiry.ClassId">
                                        <option value="">Select Class</option>
                                        <option v-for="classList in classes" :key="classList.id"
                                            :value="classList.id">{{
                                            classList.ClassName }}</option>
                                    </select>
                                    <p v-if="admissionInqueryValidationError" class="text-danger">
                                        {{ admissionInqueryValidationError }}
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <select class="form-control" v-model="form.student_admission_inquiry.gender">
                                        <option selected disabled value="">Select a gender</option>
                                        <option v-for="gen in gendarList" :key="gen.name" :value="gen.name">
                                            {{ gen.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="date" class="form-control"
                                        v-model="form.student_admission_inquiry.start_date" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="date" class="form-control"
                                        v-model="form.student_admission_inquiry.end_date" />
                                </div>
                            </div>
                            <div class="col-md-12">
                                <button class="btn btn-primary btn-block mt-auto" :disabled="loadingAdmission"
                                    @click="studentAdmissionInquiryReport">
                                    <span v-if="!loadingAdmission">
                                        <i class="fa fa-chart-line mr-1"></i> Get Admission Enquiry
                                    </span>
                                    <span v-else>
                                        <i class="fa fa-spinner fa-spin mr-1"></i> Loading...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Fee Summary Report -->
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card cstm_shadow border-0 h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fa fa-credit-card-alt text-warning fa-2x mr-3"></i>
                            <h5 class="card-title mb-0">Export Student Summary</h5>
                        </div>
                        <p class="text-muted small mb-3">
                            Student fee summary report.
                        </p>
                        <div class="row">
                            <div class="col-md-12">
                                <button class="btn btn-primary btn-block mt-auto" :disabled="loadingAdmission"
                                    @click="studentSummaryReport">

                                    <span v-if="!loadingAdmission">
                                        <i class="fa fa-chart-line mr-1"></i> Get Student Fee Summary
                                    </span>

                                    <span v-else>
                                        <i class="fa fa-spinner fa-spin mr-1"></i> Loading...
                                    </span>

                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

<!-- Content Feedback Report -->
             <div class="col-md-6 col-lg-4 mb-4">
                 <div class="card cstm_shadow border-0 h-100">
                     <div class="card-body d-flex flex-column">
                         <div class="d-flex align-items-center mb-3">
                             <i class="fa fa-credit-card-alt text-warning fa-2x mr-3"></i>
                             <h5 class="card-title mb-0">Content Feedback</h5>
                         </div>
                         <p class="text-muted small mb-3">
                             Content Feedback report.
                         </p>
                         <div class="row">
                             <div class="col-md-12">
                                 <button class="btn btn-primary btn-block mt-auto" :disabled="loadingAdmission"
                                     @click="ContentFeedbackReport">

                                     <span v-if="!loadingAdmission">
                                         <i class="fa fa-chart-line mr-1"></i> Get Content Feedback
                                     </span>

                                     <span v-else>
                                         <i class="fa fa-spinner fa-spin mr-1"></i> Loading...
                                     </span>

                                 </button>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>

             <!-- Student Withdraw Report -->
             <div class="col-md-6 col-lg-4 mb-4">
                 <div class="card cstm_shadow border-0 h-100">
                     <div class="card-body d-flex flex-column">
                         <div class="d-flex align-items-center mb-3">
                             <i class="fa fa-user-times text-danger fa-2x mr-3"></i>
                             <h5 class="card-title mb-0">Student Withdraw Report</h5>
                         </div>
                         <p class="text-muted small mb-3">
                             Generate report for withdrawn students by class/section.
                         </p>
                         <div class="row">
                             <div class="col-md-6">
                                 <div class="form-group">
                                     <select class="form-control" v-model="form.student_withdraw_report.ClassId">
                                         <option value="">Select Class</option>
                                         <option v-for="classList in classes" :key="classList.id"
                                             :value="classList.id">{{
                                                 classList.ClassName }}</option>
                                     </select>
                                 </div>
                             </div>
                             <div class="col-md-6">
                                 <div class="form-group">
                                     <select class="form-control" v-model="form.student_withdraw_report.SectionId">
                                         <option value="">Select Section</option>
                                         <option v-for="section in filteredSectionsForWithdrawReport" :key="section.id"
                                             :value="section.id">{{ section?.SectionName }}</option>
                                     </select>
                                 </div>
                             </div>

                             <div class="col-md-12">
                                 <button class="btn btn-primary btn-block mt-auto" :disabled="loadingAdmission"
                                     @click="studentWithdrawReport">

                                     <span v-if="!loadingAdmission">
                                         <i class="fa fa-chart-line mr-1"></i> Generate Report
                                     </span>

                                     <span v-else>
                                         <i class="fa fa-spinner fa-spin mr-1"></i> Loading...
                                     </span>

                                 </button>

                             </div>
                         </div>
                     </div>
                 </div>
             </div>

             </div>

            

            <div class="row">
                <!-- Attendance Report -->
                <!-- <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card cstm_shadow border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fa fa-calendar text-success fa-2x mr-3"></i>
                                <h5 class="card-title mb-0">Attendance Report</h5>
                            </div>
                            <p class="text-muted small mb-3">
                                Generate student or staff attendance report.
                            </p>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="Class" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="Section" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select class="form-control">
                                            <option value="">Select Gender</option>
                                            <option>Male</option>
                                            <option>Female</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="date" class="form-control" placeholder="Start Date" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="date" class="form-control" placeholder="End Date" />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button class="btn btn-primary btn-block mt-auto">
                                        <i class="fa fa-chart-line mr-1"></i> Get Attendance Report
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->

                <!-- Exam Report -->
                <!-- <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card cstm_shadow border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fa fa-list text-info fa-2x mr-3"></i>
                                <h5 class="card-title mb-0">Exam Report</h5>
                            </div>
                            <p class="text-muted small mb-3">
                                Generate reports by term, session, or exam type.
                            </p>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="Class" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="text" class="form-control" placeholder="Section" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select class="form-control">
                                            <option value="">Select Gender</option>
                                            <option>Male</option>
                                            <option>Female</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="date" class="form-control" placeholder="Start Date" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="date" class="form-control" placeholder="End Date" />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button class="btn btn-primary btn-block mt-auto">
                                        <i class="fa fa-chart-line mr-1"></i> Get Exam Report
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->

            </div>

            <h4>Fee:</h4>
            <div class="row">

                <!-- Student UnPaid Fee Report -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card cstm_shadow border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fa fa-money text-dark fa-2x mr-3"></i>
                                <h5 class="card-title mb-0">Student UnPaid Fee</h5>
                            </div>
                            <p class="text-muted small mb-3">
                                Check unpaid or head-wise fee reports.
                            </p>
                            <div class="row">


                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select class="form-control" v-model="form.student_unpaid_fee.ClassId">
                                            <option value="">Select Class</option>
                                            <option v-for="classList in classes" :key="classList.id"
                                                :value="classList.id">{{
                                                    classList.ClassName }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select class="form-control" v-model="form.student_unpaid_fee.SectionId">
                                            <option value="">Select Section</option>
                                            <option v-for="section in filteredSectionsForUnpaidFee" :key="section.id"
                                                :value="section.id">{{ section?.SectionName }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select class="form-control" v-model="form.student_unpaid_fee.student_id">
                                            <option selected disabled value="">Select a student</option>
                                            <option v-for="studentList in filteredStudent" :key="studentList.name"
                                                :value="studentList.id">
                                                {{ studentList?.FirstName }} {{ studentList?.LastName }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select class="form-control" v-model="form.student_unpaid_fee.month">
                                            <option value="1">January</option>
                                            <option value="2">February</option>
                                            <option value="3">March</option>
                                            <option value="4">April</option>
                                            <option value="5">May</option>
                                            <option value="6">June</option>
                                            <option value="7">July</option>
                                            <option value="8">August</option>
                                            <option value="9">September</option>
                                            <option value="10">October</option>
                                            <option value="11">November</option>
                                            <option value="12">December</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select class="form-control" v-model="form.student_unpaid_fee.year" required>
                                            <option value="">Select a year</option>
                                            <option value="2022">2022</option>
                                            <option value="2023">2023</option>
                                            <option value="2024">2024</option>
                                            <option value="2025">2025</option>
                                            <option value="2026">2026</option>
                                            <option value="2027">2027</option>
                                            <option value="2028">2028</option>
                                            <option value="2029">2029</option>
                                            <option value="2030">2030</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <button class="btn btn-primary btn-block mt-auto" :disabled="loadingAdmission"
                                        @click="studentUnPaidFee">

                                        <span v-if="!loadingAdmission">
                                            <i class="fa fa-chart-line mr-1"></i> Get UnPiad Student
                                        </span>

                                        <span v-else>
                                            <i class="fa fa-spinner fa-spin mr-1"></i> Loading...
                                        </span>

                                    </button>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Student Ledger Report -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card cstm_shadow border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fa fa-money text-dark fa-2x mr-3"></i>
                                <h5 class="card-title mb-0">Get Student Ledger</h5>
                            </div>
                            <p class="text-muted small mb-3">
                                Check Student Ledger reports.
                            </p>
                            <div class="row">


                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select class="form-control" v-model="form.student_ledger_report.ClassId">
                                            <option value="">Select Class</option>
                                            <option v-for="classList in classes" :key="classList.id"
                                                :value="classList.id">{{
                                                classList.ClassName }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select class="form-control" v-model="form.student_ledger_report.SectionId">
                                            <option value="">Select Section</option>
                                            <option v-for="section in filteredStudentSectionLedger" :key="section.id"
                                                :value="section.id">{{ section?.SectionName }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select class="form-control" v-model="form.student_ledger_report.student_id">
                                            <option selected disabled value="">Select a student</option>
                                            <option v-for="studentList in filteredStudentLedger" :key="studentList.name"
                                                :value="studentList.id">
                                                {{ studentList?.FirstName }} {{ studentList?.LastName }}
                                            </option>
                                        </select>

                                        <p v-if="StudentLedgerValidationError" class="text-danger">
                                            {{ StudentLedgerValidationError }}
                                        </p>

                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <button class="btn btn-primary btn-block mt-auto" :disabled="loadingAdmission"
                                        @click="studentLedgerSubmit">

                                        <span v-if="!loadingAdmission">
                                            <i class="fa fa-chart-line mr-1"></i> Get Student Ledger
                                        </span>

                                        <span v-else>
                                            <i class="fa fa-spinner fa-spin mr-1"></i> Loading...
                                        </span>

                                    </button>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- School Fee Ledger Report -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card cstm_shadow border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fa fa-money text-dark fa-2x mr-3"></i>
                                <h5 class="card-title mb-0">School Fee Ledger Report</h5>
                            </div>
                            <p class="text-muted small mb-3">
                                Check School Fee Ledger Report.
                            </p>
                            <div class="row">


                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select class="form-control" v-model="form.school_fee_ledger_report.ClassId">
                                            <option value="">Select Class</option>
                                            <option v-for="classList in classes" :key="classList.id"
                                                :value="classList.id">{{
                                                classList.ClassName }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select class="form-control" v-model="form.school_fee_ledger_report.SectionId">
                                            <option value="">Select Section</option>
                                            <option v-for="section in filteredSchoolFeeSectionLedger" :key="section.id"
                                                :value="section.id">{{ section?.SectionName }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <input type="date" v-model="form.school_fee_ledger_report.from_date" class="form-control" placeholder="From Date" />
                                    <!-- <div class="form-group">
                                        <select class="form-control" v-model="form.school_fee_ledger_report.from_month">
                                            <option value="">Select From  Month</option>
                                            <option value="1">January</option>
                                            <option value="2">February</option>
                                            <option value="3">March</option>
                                            <option value="4">April</option>
                                            <option value="5">May</option>
                                            <option value="6">June</option>
                                            <option value="7">July</option>
                                            <option value="8">August</option>
                                            <option value="9">September</option>
                                            <option value="10">October</option>
                                            <option value="11">November</option>
                                            <option value="12">December</option>
                                        </select>
                                    </div> -->
                                </div>
                             
                                <div class="col-md-6">
                                    <input type="date" v-model="form.school_fee_ledger_report.to_date" class="form-control" placeholder="To Date" />
                                    <!-- <div class="form-group">
                                        <select class="form-control" v-model="form.school_fee_ledger_report.to_month">
                                            <option value="">Select To Month</option>
                                            <option value="1">January</option>
                                            <option value="2">February</option>
                                            <option value="3">March</option>
                                            <option value="4">April</option>
                                            <option value="5">May</option>
                                            <option value="6">June</option>
                                            <option value="7">July</option>
                                            <option value="8">August</option>
                                            <option value="9">September</option>
                                            <option value="10">October</option>
                                            <option value="11">November</option>
                                            <option value="12">December</option>
                                        </select>
                                    </div> -->
                                </div>

                                <div class="col-md-12">
                                    <button class="btn btn-primary btn-block mt-auto" :disabled="loadingAdmission"
                                        @click="SchoolFeeLedgerReport">

                                        <span v-if="!loadingAdmission">
                                            <i class="fa fa-chart-line mr-1"></i> Get Student Ledger
                                        </span>

                                        <span v-else>
                                            <i class="fa fa-spinner fa-spin mr-1"></i> Loading...
                                        </span>

                                    </button>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                 <!-- Fee Summary Head Wise Report -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card cstm_shadow border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fa fa-money text-warning fa-2x mr-3"></i>
                                <h5 class="card-title mb-0">Fee Summary Head Wise</h5>
                            </div>
                            <p class="text-muted small mb-3">
                                Fee Summary Head Wise Reports.
                            </p>
                            <div class="row">

                                  <!-- <div class="col-md-6">
                                    <div class="form-group">
                                        <select class="form-control" v-model="form.fee_summary_head_wise_report.ClassId" >
                                            <option value="">Select Class</option>
                                            <option v-for="classList in classes" :key="classList.id"
                                                :value="classList.id">{{
                                                classList.ClassName }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select class="form-control" v-model="form.fee_summary_head_wise_report.SectionId">
                                            <option value="">Select Section</option>
                                            <option v-for="section in filteredStudentSummaryHeadwise" :key="section.id"
                                                :value="section.id">{{ section?.SectionName }}</option>
                                        </select>
                                    </div>
                                </div> -->
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select class="form-control" v-model="form.fee_summary_head_wise_report.month">
                                            <option value="1">January</option>
                                            <option value="2">February</option>
                                            <option value="3">March</option>
                                            <option value="4">April</option>
                                            <option value="5">May</option>
                                            <option value="6">June</option>
                                            <option value="7">July</option>
                                            <option value="8">August</option>
                                            <option value="9">September</option>
                                            <option value="10">October</option>
                                            <option value="11">November</option>
                                            <option value="12">December</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select class="form-control" v-model="form.fee_summary_head_wise_report.year"
                                            required>
                                            <option value="">Select a year</option>
                                            <option value="2022">2022</option>
                                            <option value="2023">2023</option>
                                            <option value="2024">2024</option>
                                            <option value="2025">2025</option>
                                            <option value="2026">2026</option>
                                            <option value="2027">2027</option>
                                            <option value="2028">2028</option>
                                            <option value="2029">2029</option>
                                            <option value="2030">2030</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button class="btn btn-primary btn-block mt-auto" :disabled="loadingAdmission"
                                        @click="GetFeeSummaryHeadWise">

                                        <span v-if="!loadingAdmission">
                                            <i class="fa fa-chart-line mr-1"></i> Get Fee Summary Head Wise
                                        </span>
                                        <span v-else>
                                            <i class="fa fa-spinner fa-spin mr-1"></i> Loading...
                                        </span>
                                    </button>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>



            <h4>HR & Operations:</h4>
            <div class="row">
                <!-- Employee Record Report -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card cstm_shadow border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fa fa-credit-card-alt text-warning fa-2x mr-3"></i>
                                <h5 class="card-title mb-0">Employee Record</h5>
                            </div>
                            <p class="text-muted small mb-3">
                                Employee Record report.
                            </p>
                            <div class="row">
                                <div class="col-md-12">
                                    <button class="btn btn-primary btn-block mt-auto" :disabled="loadingAdmission"
                                        @click="getEmployeeRecord">

                                        <span v-if="!loadingAdmission">
                                            <i class="fa fa-chart-line mr-1"></i> Get Employee Record
                                        </span>

                                        <span v-else>
                                            <i class="fa fa-spinner fa-spin mr-1"></i> Loading...
                                        </span>

                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                 <!-- Attendance Sync Log Report -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card cstm_shadow border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fa fa-credit-card-alt text-warning fa-2x mr-3"></i>
                                <h5 class="card-title mb-0">Attendance Sync Log Record</h5>
                            </div>
                            <p class="text-muted small mb-3">
                                Attendance Sync Log Report.
                            </p>
                            <div class="row">
                                <div class="col-md-12">
                                    <button class="btn btn-primary btn-block mt-auto" :disabled="loadingAdmission">
                                        <span v-if="!loadingAdmission">
                                            <i class="fa fa-chart-line mr-1"></i> Get Attendance Sync Log
                                        </span>
                                        <span v-else>
                                            <i class="fa fa-spinner fa-spin mr-1"></i> Loading...
                                        </span> 
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <h4>Exams:</h4>
            <div class="row">
                <!-- Assessment Wise Report -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card cstm_shadow border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fa fa-money text-dark fa-2x mr-3"></i>
                                <h5 class="card-title mb-0">Assessment Wise Report</h5>
                            </div>
                            <p class="text-muted small mb-3">
                                Check Assessment Wise Report
                            </p>
                            <div class="row">


                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select class="form-control" v-model="form.assessment_wise_report.exam_term_id"
                                            @change="clearSession">
                                            <option value="">Exam Terms</option>
                                            <option v-for="termList in examTermData" :key="termList.id"
                                                :value="termList.id">{{
                                                    termList.ExamTermName }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select v-if="form.assessment_wise_report.exam_term_id" class="form-control"
                                            v-model="form.assessment_wise_report.session_id"
                                            @change="SelectSession($event.target.value)">
                                            <option value="">Select Session</option>
                                            <option v-for="lmsdata in lmsSessionData" :key="lmsdata.id"
                                                :value="lmsdata.id">{{
                                                lmsdata?.name }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select class="form-control" v-model="form.assessment_wise_report.exam_id"
                                            @change="fetchExamClass($event.target.value)">
                                            <option selected disabled value="">Select Exam</option>
                                            <option v-for="examList in examsData" :key="examList.name"
                                                :value="examList.id">
                                                {{ examList?.ExamName }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select class="form-control" v-model="form.assessment_wise_report.ClassId">
                                            <option selected disabled value="">Select Class</option>
                                            <option v-for="classList in examsClass" :key="classList.name"
                                                :value="classList.id">
                                                {{ classList?.ClassName }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <button class="btn btn-primary btn-block mt-auto" :disabled="loadingAdmission"
                                        @click="fetchAssesmentWiseReport">

                                        <span v-if="!loadingAdmission">
                                            <i class="fa fa-chart-line mr-1"></i> Get Assessment Wise
                                        </span>

                                        <span v-else>
                                            <i class="fa fa-spinner fa-spin mr-1"></i> Loading...
                                        </span>

                                    </button>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Term Wise Report -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card cstm_shadow border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fa fa-money text-dark fa-2x mr-3"></i>
                                <h5 class="card-title mb-0">Term Wise Report</h5>
                            </div>
                            <p class="text-muted small mb-3">
                                Check Term Wise Report
                            </p>
                            <div class="row">


                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select class="form-control" v-model="form.term_wise_report.exam_term_id"
                                            @change="clearSession">
                                            <option value="">Exam Terms</option>
                                            <option v-for="termList in examTermData" :key="termList.id"
                                                :value="termList.id">{{
                                                    termList.ExamTermName }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select v-if="form.term_wise_report.exam_term_id" class="form-control"
                                            v-model="form.term_wise_report.session_id"
                                            @change="SelectTermSession($event.target.value)">
                                            <option value="">Select Session</option>
                                            <option v-for="lmsdata in lmsSessionData" :key="lmsdata.id"
                                                :value="lmsdata.id">{{
                                                lmsdata?.name }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select class="form-control" v-model="form.term_wise_report.exam_id"
                                            @change="fetchTermExamClass($event.target.value)">
                                            <option selected disabled value="">Select Exam</option>
                                            <option v-for="examList in examsData" :key="examList.name"
                                                :value="examList.id">
                                                {{ examList?.ExamName }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select class="form-control" v-model="form.term_wise_report.ClassId">
                                            <option selected disabled value="">Select Class</option>
                                            <option v-for="classList in examsClass" :key="classList.name"
                                                :value="classList.id">
                                                {{ classList?.ClassName }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <button class="btn btn-primary btn-block mt-auto" :disabled="loadingAdmission"
                                        @click="fetchTermWiseReport">

                                        <span v-if="!loadingAdmission">
                                            <i class="fa fa-chart-line mr-1"></i> Get Term Wise
                                        </span>

                                        <span v-else>
                                            <i class="fa fa-spinner fa-spin mr-1"></i> Loading...
                                        </span>

                                    </button>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <h4>Attendance Report:</h4>
            <div class="row">

                
                <!-- Student Attendence Report -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card cstm_shadow border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fa fa-user text-danger fa-2x mr-3"></i>
                                <h5 class="card-title mb-0">Student Attendence Report</h5>
                            </div>
                            <p class="text-muted small mb-3">
                                Filter and view student attendance data.
                            </p>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select class="form-control" v-model="form.student_attendance_report.ClassId">
                                            <option value="">Select Class</option>
                                            <option v-for="classList in classes" :key="classList.id"
                                                :value="classList.id">{{
                                                    classList.ClassName }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select class="form-control" v-model="form.student_attendance_report.SectionId">
                                            <option value="">Select Section</option>
                                            <option v-for="section in filteredSectionsForAttendanceReport"
                                                :key="section.id" :value="section.id">{{ section?.SectionName }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select class="form-control" v-model="form.student_attendance_report.month">
                                            <option value="1">January</option>
                                            <option value="2">February</option>
                                            <option value="3">March</option>
                                            <option value="4">April</option>
                                            <option value="5">May</option>
                                            <option value="6">June</option>
                                            <option value="7">July</option>
                                            <option value="8">August</option>
                                            <option value="9">September</option>
                                            <option value="10">October</option>
                                            <option value="11">November</option>
                                            <option value="12">December</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select class="form-control" v-model="form.student_attendance_report.year"
                                            required>
                                            <option value="">Select a year</option>
                                            <option value="2022">2022</option>
                                            <option value="2023">2023</option>
                                            <option value="2024">2024</option>
                                            <option value="2025">2025</option>
                                            <option value="2026">2026</option>
                                            <option value="2027">2027</option>
                                            <option value="2028">2028</option>
                                            <option value="2029">2029</option>
                                            <option value="2030">2030</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button class="btn btn-primary btn-block mt-auto" :disabled="loadingAdmission"
                                        @click="StudentAttendenceReport">

                                        <span v-if="!loadingAdmission">
                                            <i class="fa fa-chart-line mr-1"></i> Get Student Attendence Report
                                        </span>
                                        <span v-else>
                                            <i class="fa fa-spinner fa-spin mr-1"></i> Loading...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                
                <!-- Staff Attendence Report -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card cstm_shadow border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fa fa-user text-danger fa-2x mr-3"></i>
                                <h5 class="card-title mb-0">Staff Attendence Report</h5>
                            </div>
                            <p class="text-muted small mb-3">
                                Filter and view staff attendance data.
                            </p>
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select class="form-control" v-model="form.staff_attendance_report.month">
                                            <option value="1">January</option>
                                            <option value="2">February</option>
                                            <option value="3">March</option>
                                            <option value="4">April</option>
                                            <option value="5">May</option>
                                            <option value="6">June</option>
                                            <option value="7">July</option>
                                            <option value="8">August</option>
                                            <option value="9">September</option>
                                            <option value="10">October</option>
                                            <option value="11">November</option>
                                            <option value="12">December</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <select class="form-control" v-model="form.staff_attendance_report.year"
                                            required>
                                            <option value="">Select a year</option>
                                            <option value="2022">2022</option>
                                            <option value="2023">2023</option>
                                            <option value="2024">2024</option>
                                            <option value="2025">2025</option>
                                            <option value="2026">2026</option>
                                            <option value="2027">2027</option>
                                            <option value="2028">2028</option>
                                            <option value="2029">2029</option>
                                            <option value="2030">2030</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button class="btn btn-primary btn-block mt-auto" :disabled="loadingAdmission"
                                        @click="StaffAttendanceReport">

                                        <span v-if="!loadingAdmission">
                                            <i class="fa fa-chart-line mr-1"></i> Get Staff Attendance Report
                                        </span>
                                        <span v-else>
                                            <i class="fa fa-spinner fa-spin mr-1"></i> Loading...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <h4>Parent Profession Report:</h4>
            <div class="row">
                
                <!-- Parent Profession Report -->
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card cstm_shadow border-0 h-100">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <i class="fa fa-briefcase text-danger fa-2x mr-3"></i>
                                <h5 class="card-title mb-0">Parent Profession Report</h5>
                            </div>
                            <p class="text-muted small mb-3">
                                Filter and view parents' profession data.
                            </p>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="text" v-model="form.parent_profession_report.profission"
                                            class="form-control" placeholder="Profession" />
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <button class="btn btn-primary btn-block mt-auto" :disabled="loadingAdmission"
                                        @click="ParentProfessionReport">

                                        <span v-if="!loadingAdmission">
                                            <i class="fa fa-chart-line mr-1"></i> Get Parent Profession Report
                                        </span>
                                        <span v-else>
                                            <i class="fa fa-spinner fa-spin mr-1"></i> Loading...
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>  
            </div>

        </div>
    </AuthenticatedLayout>
</template>

<style>
.cstm_shadow {
    box-shadow: rgba(0, 0, 0, 0.05) 0px 6px 24px 0px, rgba(0, 0, 0, 0.08) 0px 0px 0px 1px;
}
</style>
