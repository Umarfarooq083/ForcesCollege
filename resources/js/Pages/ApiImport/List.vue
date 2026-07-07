<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';
import { useToast } from 'vue-toastification';

const toast = useToast();
const page = usePage();
const loading = ref(false);
const buttonLoading = ref({});
const setButtonLoading = (key, value) => {
  buttonLoading.value[key] = value;
};

const callApi = async (key, url, successMessage, errorMessage) => {
  loading.value = true;
  setButtonLoading(key, true);
  try {
    await axios.get(url);
    toast.success(successMessage);
  } catch (error) {
    page.props.flash = { error: errorMessage };
  } finally {
    loading.value = false;
    setButtonLoading(key, false);
  }
};

// Wrapper functions
const sessioncallApi = () => callApi('session', route('get.all.session'), 'Session list fetched successfully!', 'Something went wrong while calling Session API.');
const campuscallApi = () => callApi('campus', route('get.all.campus'), 'Campus list fetched successfully!', 'Something went wrong while calling Campus API.');
const classescallApi = () => callApi('classes', route('get.all.classes'), 'Classes list fetched successfully!', 'Something went wrong while calling Classes API.');
const sectioncallApi = () => callApi('sections', route('get.all.sections'), 'Sections list fetched successfully!', 'Something went wrong while calling Sections API.');
const subjectcallApi = () => callApi('subject', route('get.all.subjects'), 'Subject list fetched successfully!', 'Something went wrong while calling Subjects API.');
const studentInquirycallApi = () => callApi('student-inquiry', route('get.all.student'), 'Student list fetched successfully!', 'Something went wrong while calling Student API.');
const studentInquirylostcallApi = () => callApi('student-inquiry-lost', route('get.all.student.inquiry.lost'), 'Student inquiry list fetched successfully!', 'Something went wrong while calling Student API.');
const studentAttendancecallApi = () => callApi('student-attendance', route('get.all.student.attendance'), 'Student attendance  fetched successfully!', 'Something went wrong while calling Student API.');
const feesStracturecallApi = () => callApi('fee-stracture', route('get.all.fees.stracture'), 'Fee stracture fetched successfully!', 'Something went wrong while calling Student API.');
const optionalFeescallApi = () => callApi('optional-fee-mapping', route('get.all.optional.fee.mapping'), 'Optional fee mapping fetched successfully!', 'Something went wrong while calling Student API.');
const studentFeesDiscountcallApi = () => callApi('student-fee-discount', route('get.all.student.fee.discount'), 'Student fee discount fetched successfully!', 'Something went wrong while calling Student API.');
const studentFeesChallancallApi = () => callApi('student-fee-challan', route('get.all.student.fee.challan'), 'Student fee challan fetched successfully!', 'Something went wrong while calling Student API.');
const studentFeesChallanTranscationcallApi = () => callApi('student-fee-challan-transcation', route('get.all.student.fee.challan.transcation'), 'Student fee challan transcation fetched successfully!', 'Something went wrong while calling Student API.');
const studentFeesChallanPartialcallApi = () => callApi('student-fee-challan-partial-payment', route('get.all.challan.partial.payment'), 'Student challan partial payments fetched successfully!', 'Something went wrong while calling Student API.');
const studentFeesChallanArrearscallApi = () => callApi('student-fee-challan-arrears', route('student.fee.challan.arrears'), 'Student challan arrears fetched successfully!', 'Something went wrong while calling Student API.');
const examTermscallApi = () => callApi('get-exam-terms', route('get.exam.terms'), 'Exam terms fetched successfully!', 'Something went wrong while calling Student API.');
const examTypecallApi = () => callApi('get-exam-types', route('get.exam.types'), 'Exam types fetched successfully!', 'Something went wrong while calling Student API.');
const examSubjectcallApi = () => callApi('get-exam-subject', route('get.exam.subject'), 'Exam subject fetched successfully!', 'Something went wrong while calling Student API.');
const examStudentcallApi = () => callApi('get-exam-student', route('get.imported.exam.student'), 'Exam student fetched successfully!', 'Something went wrong while calling Student API.');
const examMarkscallApi = () => callApi('get-exam-marks', route('get.exam.marks'), 'Exam marks fetched successfully!', 'Something went wrong while calling Student API.');
const examMarksDetailcallApi = () => callApi('get-exam-marks-detail', route('get.exam.marks.detail'), 'Exam marks detail fetched successfully!', 'Something went wrong while calling Student API.');
const examMarksGradecallApi = () => callApi('get-exam-marks-grade', route('get.exam.marks.grade'), 'Exam grade fetched successfully!', 'Something went wrong while calling Student API.');
const departmentcallApi = () => callApi('get-department-list', route('get.department.list'), 'Department fetched successfully!', 'Something went wrong while calling Student API.');
const designationcallApi = () => callApi('get-designation-list', route('get.designation.list'), 'Designation fetched successfully!', 'Something went wrong while calling Student API.');
const getStaffcallApi = () => callApi('get-staff-list', route('get.staff.list'), 'Staff fetched successfully!', 'Something went wrong while calling Student API.');
const assignClassTeachercallApi = () => callApi('get-assign-class-teacher', route('get.assign.class.teacher'), 'Assign Class Teacher fetched successfully!', 'Something went wrong while calling Student API.');
const ClassTimeTablecallApi = () => callApi('get-class-time-table', route('get.class.time.table'), 'Class time table fetched successfully!', 'Something went wrong while calling Student API.');
const UsercallApi = () => callApi('get-user-list', route('get.user.list'), 'User fetched successfully!', 'Something went wrong while calling Student API.');
const rolescallApi = () => callApi('get-roles-list', route('get.roles.list'), 'Roles fetched successfully!', 'Something went wrong while calling Student API.');
const GetUploadContentGroupcallApi = () => callApi('get-upload-content-group-list', route('get.upload.content.group.list'), 'Upload content group fetched successfully!', 'Something went wrong while calling Student API.');
const GetUploadContentcallApi = () => callApi('get-upload-content-list', route('get.upload.content.list'), 'Upload content fetched successfully!', 'Something went wrong while calling Student API.');
const GetUploadContentLogcallApi = () => callApi('get-upload-content-log-list', route('get.upload.content.log.list'), 'Upload content fetched successfully!', 'Something went wrong while calling Student API.');
const GetHomeWorkcallApi = () => callApi('get-home-work-list', route('get.home.work.list'), 'Home work fetched successfully!', 'Something went wrong while calling Student API.');
const GetSMSCreditcallApi = () => callApi('get-sms-credit-list', route('get.sms-credit.list'), 'SMS credit fetched successfully!', 'Something went wrong while calling Student API.');
const GetSMSLogcallApi = () => callApi('get-sms-log-list', route('get.sms.log.list'), 'SMS log fetched successfully!', 'Something went wrong while calling Student API.');
</script>

<template>
  <Head title="API List" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        API List
      </h2>
    </template>

    <h5 style="color: red; text-align: center;">
      <!-- <strong>Most Important Note: Check Your API Campus Before Sending Request: (flagshipbwc)</strong> -->
      <!-- <strong>Most Important Note: Check Your API Campus Before Sending Request: (Premium)</strong> -->
      <strong>Most Important Note: Check Your API Campus Before Sending Request: (Premium)</strong>
    </h5>

    <!-- Session API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">Session List</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetSession"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="sessioncallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center  gap-2"
      >
        <span v-if="buttonLoading.session" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading.session" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading.session ? 'Processing...' : 'Call API' }}
      </button>
    </div>

    <!-- Campus API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">Campus List</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetCampus"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="campuscallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading.campus" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading.campus" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading.campus ? 'Processing...' : 'Call API' }}
      </button>
    </div>

    <!-- Classes API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">Class List</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetClass"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="classescallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading.classes" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading.classes" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading.classes ? 'Processing...' : 'Call API' }}
      </button>
    </div>

    <!-- Section API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">Section List</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetSection"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="sectioncallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading.sections" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading.sections" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading.sections ? 'Processing...' : 'Call API' }}
      </button>
    </div>

    <!-- Subject API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">Subject List</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetSubject"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="subjectcallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading.subject" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading.subject" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading.subject ? 'Processing...' : 'Call API' }}
      </button>
    </div>

     <!-- Student Inquiry Lost API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">Student Inquiry List</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetAdmissionEnquiryLost"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="studentInquirylostcallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading['student-inquiry-lost']" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading['student-inquiry-lost']" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading['student-inquiry-lost'] ? 'Processing...' : 'Call API' }}
      </button>
    </div>
    
    <!-- Student Inquiry API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">Student List</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetStudents"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="studentInquirycallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading['student-inquiry']" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading['student-inquiry']" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading['student-inquiry'] ? 'Processing...' : 'Call API' }}
      </button>
    </div>

     <!-- Student Attendance API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">Student Attendance</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetStudentAttendance"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="studentAttendancecallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading['student-attendance']" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading['student-attendance']" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading['student-attendance'] ? 'Processing...' : 'Call API' }}
      </button>
    </div>

     <!-- Fees Stracture API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">Fee Stracture</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetFeeStructure"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="feesStracturecallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading['fee-stracture']" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading['fee-stracture']" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading['fee-stracture'] ? 'Processing...' : 'Call API' }}
      </button>
    </div>
     
    <!-- Optional Fees Mapping API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">Optional Fee Mapping</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetStudentOptionalFees"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="optionalFeescallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading['optional-fee-mapping']" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading['optional-fee-mapping']" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading['optional-fee-mapping'] ? 'Processing...' : 'Call API' }}
      </button>
    </div>
   
    <!-- Student Fees Discounts API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">Student Fee Discount</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetStudentDiscounts"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="studentFeesDiscountcallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading['student-fee-discount']" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading['student-fee-discount']" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading['student-fee-discount'] ? 'Processing...' : 'Call API' }}
      </button>
    </div> 
   
    <!-- Student Fees Challan API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">Student Fee Challan</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetStudentChallans"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="studentFeesChallancallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading['student-fee-challan']" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading['student-fee-challan']" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading['student-fee-challan'] ? 'Processing...' : 'Call API' }}
      </button>
    </div>
   
    <!-- Student Fees Challan Transcation API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">Student Fee Challan Transcation</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetChallanTransactions"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="studentFeesChallanTranscationcallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading['student-fee-challan-transcation']" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading['student-fee-challan-transcation']" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading['student-fee-challan-transcation'] ? 'Processing...' : 'Call API' }}
      </button>
    </div>
    
    <!-- Student Challan Partial Payments API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">Student Challan Partial Payments</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetChallanPartialPayments"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="studentFeesChallanPartialcallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading['student-fee-challan-partial-payment']" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading['student-fee-challan-partial-payment']" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading['student-fee-challan-partial-payment'] ? 'Processing...' : 'Call API' }}
      </button>
    </div>

    <!-- Student Challan Arrars API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">Student Challan Arrars</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetChallanArrears"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="studentFeesChallanArrearscallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading['student-fee-challan-arrears']" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading['student-fee-challan-arrears']" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading['student-fee-challan-arrears'] ? 'Processing...' : 'Call API' }}
      </button>
    </div>
   
    <!-- Exam Terms API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">Exam Terms</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetExamTerm"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="examTermscallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading['get-exam-terms']" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading['get-exam-terms']" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading['get-exam-terms'] ? 'Processing...' : 'Call API' }}
      </button>
    </div>

    <!-- Exam type API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">Exams</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetExam"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="examTypecallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading['get-exam-types']" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading['get-exam-types']" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading['get-exam-types'] ? 'Processing...' : 'Call API' }}
      </button>
    </div>

    <!-- Exam Subject API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">Exam Subject</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetExamSubjects"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="examSubjectcallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading['get-exam-subject']" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading['get-exam-subject']" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading['get-exam-subject'] ? 'Processing...' : 'Call API' }}
      </button>
    </div>
   
    <!-- Exam Student API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">Exam Student</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetExamStudents"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="examStudentcallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading['get-exam-student']" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading['get-exam-student']" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading['get-exam-student'] ? 'Processing...' : 'Call API' }}
      </button>
    </div>
 
    <!-- Exam Marks API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">Exam Marks</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetExamMarks"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="examMarkscallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading['get-exam-marks']" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading['get-exam-marks']" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading['get-exam-marks'] ? 'Processing...' : 'Call API' }}
      </button>
    </div>

    <!-- Exam Marks Detail API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">Exam Marks Detail</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetExamMarksDetail"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="examMarksDetailcallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading['get-exam-marks-detail']" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading['get-exam-marks-detail']" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading['get-exam-marks-detail'] ? 'Processing...' : 'Call API' }}
      </button>
    </div>

    <!-- Exam Marks Grade API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">Exam Marks Grade</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetMarksGrades"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="examMarksGradecallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading['get-exam-marks-grade']" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading['get-exam-marks-grade']" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading['get-exam-marks-grade'] ? 'Processing...' : 'Call API' }}
      </button>
    </div>

    <!-- EDepartment API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">Department List</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetDepartment"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="departmentcallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading['get-department-list']" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading['get-department-list']" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading['get-department-list'] ? 'Processing...' : 'Call API' }}
      </button>
    </div>
   
     <!-- Designation API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">Designation List</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetDesignation"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="designationcallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading['get-designation-list']" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading['get-designation-list']" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading['get-designation-list'] ? 'Processing...' : 'Call API' }}
      </button>
    </div>
   
    <!-- Get Staff API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">Staff List</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetStaff"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="getStaffcallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading['get-staff-list']" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading['get-staff-list']" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading['get-staff-list'] ? 'Processing...' : 'Call API' }}
      </button>
    </div>
     
    <!-- Assign Class Teacher API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">Assign Class Teacher</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetAssignClassTeacher"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="assignClassTeachercallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading['get-assign-class-teacher']" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading['get-assign-class-teacher']" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading['get-assign-class-teacher'] ? 'Processing...' : 'Call API' }}
      </button>
    </div>
  
    <!-- Class Time Table API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">Class Time Table</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetClassTimeTable"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="ClassTimeTablecallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading['get-class-time-table']" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading['get-class-time-table']" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading['get-class-time-table'] ? 'Processing...' : 'Call API' }}
      </button>
    </div>
   
    <!-- User API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">User List</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetUser"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="UsercallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading['get-user-list']" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading['get-user-list']" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading['get-user-list'] ? 'Processing...' : 'Call API' }}
      </button>
    </div>
    
    <!-- Roles API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">Roles List</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetRole"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="rolescallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading['get-roles-list']" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading['get-roles-list']" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading['get-roles-list'] ? 'Processing...' : 'Call API' }}
      </button>
    </div>
   
    <!-- Upload Content Group API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">Upload Content Group</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetUploadContentGroup"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="GetUploadContentGroupcallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading['get-upload-content-group-list']" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading['get-upload-content-group-list']" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading['get-upload-content-group-list'] ? 'Processing...' : 'Call API' }}
      </button>
    </div>
    
    <!-- Upload Content API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">Upload Content</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetUploadContent"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="GetUploadContentcallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading['get-upload-content-list']" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading['get-upload-content-list']" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading['get-upload-content-list'] ? 'Processing...' : 'Call API' }}
      </button>
    </div>
   
    <!-- Upload Content Log API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">Upload Content Log</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetDownloadContentLog"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="GetUploadContentLogcallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading['get-upload-content-log-list']" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading['get-upload-content-log-list']" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading['get-upload-content-log-list'] ? 'Processing...' : 'Call API' }}
      </button>
    </div>
  
    <!-- Get Home Work API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">Home Work</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetHomework"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="GetHomeWorkcallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading['get-home-work-list']" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading['get-home-work-list']" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading['get-home-work-list'] ? 'Processing...' : 'Call API' }}
      </button>
    </div>
  
    <!-- Get SMS Credits API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">SMS Credit</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetSMSCredit"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="GetSMSCreditcallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading['get-sms-credit-list']" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading['get-sms-credit-list']" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading['get-sms-credit-list'] ? 'Processing...' : 'Call API' }}
      </button>
    </div>
    
    <!-- Get SMS Log API -->
    <div class="card shadow rounded-lg p-4 bg-white">
      <div class="header mb-3">
        <div class="font-weight-bold text-lg">SMS Log</div>
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700 mb-1">End Point</label>
        <input
          value="http://headoffice.fscscampus.com/api/Migration/GetSMSLog"
          type="text"
          class="border rounded p-2 w-full focus:ring focus:ring-blue-200"
          disabled
        />
      </div>

      <button
        @click="GetSMSLogcallApi"
        :disabled="loading"
        class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700 disabled:opacity-50 flex items-center gap-2"
      >
        <span v-if="buttonLoading['get-sms-log-list']" class="animate-spin border-2 border-white border-t-transparent rounded-full w-4 h-4"></span>
        <svg v-if="buttonLoading['get-sms-log-list']" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
        </svg>
        {{ buttonLoading['get-sms-log-list'] ? 'Processing...' : 'Call API' }}
      </button>
    </div>

  </AuthenticatedLayout>
</template>

<style scoped>
.card {
  border: 1px solid #e5e7eb;
}
.card .header{
      padding: 1px !important;
}
</style>
