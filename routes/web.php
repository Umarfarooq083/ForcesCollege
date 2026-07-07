<?php

use App\Http\Controllers\APIImportData\ImportController;
use App\Http\Controllers\Campus\CampusController;
use App\Http\Controllers\CampusUser\UserController;
use App\Http\Controllers\Class\ClassController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\Exam\ExamStudentController;
use App\Http\Controllers\Exam\ExamSubjectController;
use App\Http\Controllers\Exam\ExamTermController;
use App\Http\Controllers\Exam\ExamTypeController;
use App\Http\Controllers\Exam\MarksController;
use App\Http\Controllers\Exam\MarksGradeController;
use App\Http\Controllers\Fees\CampusFeesMasterController;
use App\Http\Controllers\Fees\DeleteChallanController;
use App\Http\Controllers\Fees\FeeCollectionController;
use App\Http\Controllers\Fees\FeesTypeController;
use App\Http\Controllers\Fees\GenerateFeeChallanController;
use App\Http\Controllers\Fees\OptionalFeeMasterController;
use App\Http\Controllers\Fees\StudentFeeDiscountController;
use App\Http\Controllers\LmsSessions\LmsSessionsController;
use App\Http\Controllers\PhonebookController;
use App\Http\Controllers\PhonebookGroupController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Reports\MasterReportController;
use App\Http\Controllers\Reports\ResultCardController;
use App\Http\Controllers\Roles\RolesController;
use App\Http\Controllers\Section\SectionController;
use App\Http\Controllers\Setting\SiteSettingController;
use App\Http\Controllers\SMS\SendSMSController;
use App\Http\Controllers\SmsCreditController;
use App\Http\Controllers\Staff\AssignClassTeacherController;
use App\Http\Controllers\Staff\AttendanceController as StaffAttendanceController;
use App\Http\Controllers\Staff\ClassTimeTableController;
use App\Http\Controllers\Staff\DepartmentController;
use App\Http\Controllers\Staff\DesignationController;
use App\Http\Controllers\Staff\GazettedLeaveController;
use App\Http\Controllers\Staff\StaffController;
use App\Http\Controllers\Student\AttendanceController;
use App\Http\Controllers\Student\HomeWorkController;
use App\Http\Controllers\Student\PromoteStudentController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\StudentInquiry\StudentInquiryController;
use App\Http\Controllers\Subject\SubjectController;
use App\Http\Controllers\UploadContent\ContentUploadController;
use App\Http\Controllers\UploadContent\UploadContentGroupController;
use App\Http\Controllers\Zone\ZoneController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

// Route::middleware([
//     'web',
//     InitializeTenancyByDomain::class,
//     PreventAccessFromCentralDomains::class,
// ])->group(function () {
//     Route::get('/umar', function () {
//         return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
//     });
// });

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {

    Route::get('/auto-login', [DeviceController::class, 'autoLogin']);

    Route::get('/', function () {
        // return Inertia::render('Welcome', [
        //     'canLogin' => Route::has('login'),
        //     'canRegister' => Route::has('register'),
        //     'laravelVersion' => Application::VERSION,
        //     'phpVersion' => PHP_VERSION,
        // ]);
        return redirect()->route('login');
    });

    // Route::get('/dashboard', function () {
    //     return Inertia::render('Dashboard');
    // })->middleware(['auth', 'verified'])->name('dashboard');

    Route::group(['middleware' => ['auth', 'verified']], function () {
        Route::get('dashboard', [DashboardController::class, 'getDashboardData'])->name('dashboard');
        Route::get('dashboard/fees-data', [DashboardController::class, 'getFeesData'])->name('dashboard.feesData');
        Route::get('dashboard/staff-attendance-filter', [DashboardController::class, 'getStaffAttendanceFilterData'])->name('dashboard.staff.attendance');
        Route::get('dashboard/staff-attendance-table-filter', [DashboardController::class, 'getStaffAttendanceTableFilter'])->name('dashboard.staff.table.filter');
        Route::get('dashboard/student-attendance-filter', [DashboardController::class, 'getStudentAttendanceFilterData'])->name('dashboard.student.attendance');
        Route::get('dashboard/student-inquiry-filter', [DashboardController::class, 'getStudentInquiryFilterData'])->name('dashboard.students.inquiry');
    });

    Route::middleware(['auth', 'check_permission'])->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    Route::group(['middleware' => ['auth']], function () {
        Route::get('campus-data', [UserController::class, 'getCampusData'])->name('get.campus.data');
        Route::post('switch-campus', [UserController::class, 'switchCampus'])->name('get.campus.data');
    });

    Route::group(['prefix' => 'roles', 'middleware' => ['auth', 'check_permission']], function () {
        Route::get('list', [RolesController::class, 'index'])->name('role.index');
        Route::get('create', [RolesController::class, 'create'])->name('role.create');
        Route::post('submit', [RolesController::class, 'submit'])->name('role.submit');
        Route::get('edit', [RolesController::class, 'edit'])->name('role.edit');
        Route::put('update', [RolesController::class, 'update'])->name('role.update');
        Route::delete('delete', [RolesController::class, 'delete'])->name('role.delete');
        Route::get('assing-permission', [RolesController::class, 'permissionAssign'])->name('role.permission.assign');
        Route::post('assing-permission-submit', [RolesController::class, 'permissionAssignSubmit'])->name('role.permission.submit');
    });

    Route::group(['prefix' => 'campus', 'middleware' => ['auth', 'check_permission']], function () {
        Route::get('list', [CampusController::class, 'index'])->name('campus.index');
        Route::get('create', [CampusController::class, 'create'])->name('campus.create');
        Route::post('submit', [CampusController::class, 'submit'])->name('campus.submit');
        Route::get('edit', [CampusController::class, 'edit'])->name('campus.edit');
        Route::put('update', [CampusController::class, 'update'])->name('campus.update');
        Route::delete('delete', [CampusController::class, 'delete'])->name('campus.delete');
    });

    Route::group(['prefix' => 'user', 'middleware' => ['auth', 'check_permission']], function () {
        Route::get('list', [UserController::class, 'index'])->name('user.index');
        Route::get('create', [UserController::class, 'create'])->name('user.create');
        Route::post('submit', [UserController::class, 'submit'])->name('user.submit');
        Route::get('edit', [UserController::class, 'edit'])->name('user.edit');
        Route::put('update', [UserController::class, 'update'])->name('user.update');
        Route::delete('delete', [UserController::class, 'delete'])->name('user.delete');
    });

    Route::group(['prefix' => 'zones', 'middleware' => ['auth', 'check_permission']], function () {
        Route::get('list', [ZoneController::class, 'index'])->name('zone.index');
        Route::get('create', [ZoneController::class, 'create'])->name('zone.create');
        Route::post('submit', [ZoneController::class, 'submit'])->name('zone.submit');
        Route::put('/{id}/toggle-status', [ZoneController::class, 'toggleStatus'])->name('zone.toggleStatus');
    });

    Route::group(['prefix' => 'student-inquiry', 'middleware' => ['auth', 'check_permission']], function () {
        Route::get('inquiry-list', [StudentInquiryController::class, 'index'])->name('inquiry.index');
        Route::get('inquiry-create', [StudentInquiryController::class, 'create'])->name('inquiry.create');
        Route::post('inquiry-submit', [StudentInquiryController::class, 'submit'])->name('inquiry.submit');
        Route::get('inquiry-edit', [StudentInquiryController::class, 'edit'])->name('inquiry.edit');
        Route::get('inquiry-detail', [StudentInquiryController::class, 'detail'])->name('inquiry.detail');
        Route::put('inquiry-update', [StudentInquiryController::class, 'update'])->name('inquiry.update');
        Route::put('/{id}/inquiry-status', [StudentInquiryController::class, 'statusUpdate'])->name('inquiry.status');
        Route::post('check-guardian', [StudentInquiryController::class, 'checkGuardian'])->name('inquiry.check.guardian');
    });

    Route::group(['prefix' => 'class', 'middleware' => ['auth', 'check_permission']], function () {
        Route::get('list', [ClassController::class, 'index'])->name('class.index');
        Route::get('create', [ClassController::class, 'create'])->name('class.create');
        Route::post('submit', [ClassController::class, 'submit'])->name('class.submit');
        Route::get('edit', [ClassController::class, 'edit'])->name('class.edit');
        Route::post('update', [ClassController::class, 'update'])->name('class.update');
        Route::delete('delete', [ClassController::class, 'delete'])->name('class.delete');
        Route::put('/{id}/class-status', [ClassController::class, 'statusUpdate'])->name('class.status');
    });

    Route::group(['prefix' => 'section', 'middleware' => ['auth', 'check_permission']], function () {
        Route::get('list', [SectionController::class, 'index'])->name('section.index');
        Route::get('create', [SectionController::class, 'create'])->name('section.create');
        Route::post('submit', [SectionController::class, 'submit'])->name('section.submit');
        Route::get('edit', [SectionController::class, 'edit'])->name('section.edit');
        Route::put('update', [SectionController::class, 'update'])->name('section.update');
    });
    Route::group(['prefix' => 'lms-sessions', 'middleware' => ['auth', 'check_permission']], function () {
        Route::get('list', [LmsSessionsController::class, 'index'])->name('lmssessions.index');
        Route::get('create', [LmsSessionsController::class, 'create'])->name('lmssessions.create');
        Route::post('submit', [LmsSessionsController::class, 'submit'])->name('lmssessions.submit');
        Route::get('{id}/edit', [LmsSessionsController::class, 'edit'])->name('lmssessions.edit');
        Route::put('{id}/update', [LmsSessionsController::class, 'update'])->name('lmssessions.update');
        Route::delete('{id}/delete', [LmsSessionsController::class, 'destroy'])->name('lmssessions.destroy');
        Route::put('/{id}/toggle-status', [LmsSessionsController::class, 'toggleStatus'])->name('lmssessions.toggleStatus');
    });

    Route::group(['prefix' => 'student', 'middleware' => ['auth', 'check_permission']], function () {
        Route::get('list', [StudentController::class, 'index'])->name('student.index');
        Route::get('create', [StudentController::class, 'create'])->name('student.create');
        Route::post('submit', [StudentController::class, 'submit'])->name('student.submit');
        Route::get('edit', [StudentController::class, 'edit'])->name('student.edit');
        Route::put('update', [StudentController::class, 'update'])->name('student.update');
        Route::get('detail', [StudentController::class, 'detail'])->name('student.detail');
        Route::get('card', [StudentController::class, 'card'])->name('student.card');
        Route::get('card-pdf', [StudentController::class, 'cardPdf'])->name('student.card.pdf');
        Route::get('withdraw', [StudentController::class, 'withdraw'])->name('student.withdraw');
        Route::put('toggle-status/{id}', [StudentController::class, 'toggleStatus'])->name('student.toggleStatus');
        Route::post('withdraw/{id}', [StudentController::class, 'withdrawSubmit'])->name('student.withdrawsubmit');
        Route::get('withdraw-list', [StudentController::class, 'withdrawList'])->name('student.withdrawlist');
        Route::put('withdraw-approve/{id}', [StudentController::class, 'approveWithdraw'])->name('student.withdrawapprove');
        Route::put('withdraw-reject/{id}', [StudentController::class, 'rejectWithdraw'])->name('student.withdrawreject');
        Route::get('readmission-list', [StudentController::class, 'readmissionList'])->name('student.readmissionlist');
        Route::get('readmission/{id}', [StudentController::class, 'readmission'])->name('student.readmission');
        Route::post('readmission/{id}', [StudentController::class, 'readmissionSubmit'])->name('student.readmissionsubmit');

        Route::get('homework/list', [HomeWorkController::class, 'index'])->name('homework.index');
        Route::get('homework/create', [HomeWorkController::class, 'create'])->name('homework.create');
        Route::Post('homework/submit', [HomeWorkController::class, 'submit'])->name('homework.submit');
        Route::get('homework/edit', [HomeWorkController::class, 'edit'])->name('homework.edit');
        Route::Post('homework/update', [HomeWorkController::class, 'update'])->name('homework.update');
        Route::get('homework/detail', [HomeWorkController::class, 'show'])->name('homework.show');
        Route::delete('homework/delete', [HomeWorkController::class, 'destroy'])->name('homework.delete');

        Route::get('promote-student', [PromoteStudentController::class, 'index'])->name('promotestudent.index');
        Route::get('fetch-promote-student', [PromoteStudentController::class, 'fetch'])->name('promotestudent.fetch');
        Route::get('fetch-promote-list', [PromoteStudentController::class, 'promotionlist'])->name('promotestudent.list');
        Route::post('promote-submit', [PromoteStudentController::class, 'promotionSubmit'])->name('promotestudent.store');

    });

    Route::group(['prefix' => 'disablestudent', 'middleware' => ['auth', 'check_permission']], function () {
        Route::get('disablelist', [StudentController::class, 'disablelist'])->name('disablestudent.disablelist');
    });

    Route::group(['prefix' => 'subject', 'middleware' => ['auth', 'check_permission']], function () {
        Route::get('list', [SubjectController::class, 'index'])->name('subject.index');
        Route::get('create', [SubjectController::class, 'create'])->name('subject.create');
        Route::post('submit', [SubjectController::class, 'submit'])->name('subject.submit');
        Route::get('edit', [SubjectController::class, 'edit'])->name('subject.edit');
        Route::put('update', [SubjectController::class, 'update'])->name('subject.update');
    });

    Route::group(['prefix' => 'content', 'middleware' => ['auth', 'check_permission']], function () {
        Route::get('group/list', [UploadContentGroupController::class, 'index'])->name('content.index');
        Route::get('group/create', [UploadContentGroupController::class, 'create'])->name('content.create');
        Route::post('group/submit', [UploadContentGroupController::class, 'submit'])->name('content.submit');
        Route::get('group/edit', [UploadContentGroupController::class, 'edit'])->name('content.edit');
        Route::put('group/update', [UploadContentGroupController::class, 'update'])->name('content.update');
        Route::get('group/delete', [UploadContentGroupController::class, 'delete'])->name('content.delete');
    });

    Route::group(['prefix' => 'uploads', 'middleware' => ['auth', 'check_permission']], function () {

        Route::get('content/specialist-list', [ContentUploadController::class, 'specialistList'])->name('uploads.specialistlist');
        Route::get('content/approval', [ContentUploadController::class, 'Approval'])->name('uploads.approval');
        Route::post('content/approve', [ContentUploadController::class, 'Approve'])->name('uploads.approve');

        Route::get('content/list', [ContentUploadController::class, 'index'])->name('uploads.index');
        Route::get('content/create', [ContentUploadController::class, 'create'])->name('uploads.create');
        Route::post('content/submit', [ContentUploadController::class, 'submit'])->name('uploads.submit');
        Route::get('content/edit', [ContentUploadController::class, 'edit'])->name('uploads.edit');
        // Use POST for file uploads; Laravel/Symfony doesn't reliably parse multipart PUT/PATCH payloads.
        Route::post('content/update', [ContentUploadController::class, 'update'])->name('uploads.update');
        Route::get('content/download', [ContentUploadController::class, 'download'])->name('uploads.download');
        Route::delete('content/delete', [ContentUploadController::class, 'delete'])->name('uploads.delete');
        Route::get('content/downloaded-logs', [ContentUploadController::class, 'downloadedLogs'])->name('downloaded.logs');
        Route::post('content/downloaded-logs/export', [ContentUploadController::class, 'exportDownloadedLogs'])->name('downloaded.logs.export');
    });

    Route::group(['prefix' => 'attendance', 'middleware' => ['auth', 'check_permission']], function () {
        Route::get('create', [AttendanceController::class, 'attendanceList'])->name('attendance.create');
        Route::post('submit', [AttendanceController::class, 'submitAttendance'])->name('attendance.submit');
    });

Route::group(['prefix' => 'staff', 'middleware' => ['auth', 'check_permission']], function () {
         Route::get('leave-request/list', [\App\Http\Controllers\Staff\LeaveRequestController::class, 'index'])->name('leave-request.index');
         Route::get('leave-request/create', [\App\Http\Controllers\Staff\LeaveRequestController::class, 'create'])->name('leave-request.create');
         Route::post('leave-request/submit', [\App\Http\Controllers\Staff\LeaveRequestController::class, 'submit'])->name('leave-request.submit');
         Route::get('leave-request/edit', [\App\Http\Controllers\Staff\LeaveRequestController::class, 'edit'])->name('leave-request.edit');
         Route::put('leave-request/update', [\App\Http\Controllers\Staff\LeaveRequestController::class, 'update'])->name('leave-request.update');
         Route::put('leave-request/{id}/approve', [\App\Http\Controllers\Staff\LeaveRequestController::class, 'approve'])->name('leave-request.approve');
         Route::delete('leave-request/delete', [\App\Http\Controllers\Staff\LeaveRequestController::class, 'delete'])->name('leave-request.delete');

         Route::get('list', [StaffController::class, 'index'])->name('staff.list');
        Route::get('create', [StaffController::class, 'create'])->name('staff.create');
        Route::post('submit', [StaffController::class, 'submit'])->name('staff.submit');
        Route::get('edit', [StaffController::class, 'edit'])->name('staff.edit');
        Route::post('update', [StaffController::class, 'update'])->name('staff.update');
        Route::delete('delete', [StaffController::class, 'delete'])->name('staff.delete');
        Route::put('toggle-status/{id}', [StaffController::class, 'toggleStatus'])->name('staff.toggleStatus');

        Route::get('assign-class/list', [AssignClassTeacherController::class, 'index'])->name('assign.class.teacher.list');
        Route::get('assign-class/create', [AssignClassTeacherController::class, 'create'])->name('assign.class.teacher.create');
        Route::post('assign-class/submit', [AssignClassTeacherController::class, 'submit'])->name('assign.class.teacher.submit');
        Route::get('assign-class/edit', [AssignClassTeacherController::class, 'edit'])->name('assign.class.teacher.edit');
        Route::put('assign-class/update', [AssignClassTeacherController::class, 'update'])->name('assign.class.teacher.update');

        Route::get('classtimetable/list', [ClassTimeTableController::class, 'index'])->name('classtimetable.index');
        Route::get('classtimetable/create', [ClassTimeTableController::class, 'create'])->name('classtimetable.create');
        Route::post('classtimetable/submit', [ClassTimeTableController::class, 'submit'])->name('classtimetable.submit');
        Route::get('classtimetable/{id}/sections-subjects', [ClassTimeTableController::class, 'getSectionsAndSubjects'])->name('classtimetable.sections_subjects');

        Route::get('attendance/create', [StaffAttendanceController::class, 'staffAttendanceList'])->name('staff.attendance.list');
        Route::post('attendance/submit', [StaffAttendanceController::class, 'submitStaffAttendance'])->name('staff.attendance.submit');

        Route::get('department/list', [DepartmentController::class, 'index'])->name('department.list');
        Route::get('department/create', [DepartmentController::class, 'create'])->name('department.create');
        Route::post('department/submit', [DepartmentController::class, 'submit'])->name('department.submit');
        Route::get('department/edit', [DepartmentController::class, 'edit'])->name('department.edit');
        Route::put('department/update', [DepartmentController::class, 'update'])->name('department.update');

        Route::get('designation/list', [DesignationController::class, 'index'])->name('designation.index');
        Route::get('designation/create', [DesignationController::class, 'create'])->name('designation.create');
        Route::post('designation/submit', [DesignationController::class, 'submit'])->name('designation.submit');
        Route::get('designation/edit', [DesignationController::class, 'edit'])->name('designation.edit');
        Route::put('designation/update', [DesignationController::class, 'update'])->name('designation.update');
        Route::delete('{id}/designation/delete', [DesignationController::class, 'destroy'])->name('designation.destroy');
        Route::put('designation/toggle-status/{id}', [DesignationController::class, 'toggleStatus'])->name('designation.toggleStatus');

      
    });

     Route::group(['prefix' => 'hr', 'middleware' => ['auth', 'check_permission']], function () {

        Route::get('campus-weekly-holiday/list', [\App\Http\Controllers\Staff\CampusWeeklyHolidayController::class, 'index'])->name('campus-weekly-holiday.index');
        Route::get('campus-weekly-holiday/create', [\App\Http\Controllers\Staff\CampusWeeklyHolidayController::class, 'create'])->name('campus-weekly-holiday.create');
        Route::post('campus-weekly-holiday/submit', [\App\Http\Controllers\Staff\CampusWeeklyHolidayController::class, 'submit'])->name('campus-weekly-holiday.submit');
        Route::get('campus-weekly-holiday/edit', [\App\Http\Controllers\Staff\CampusWeeklyHolidayController::class, 'edit'])->name('campus-weekly-holiday.edit');
        Route::put('campus-weekly-holiday/update', [\App\Http\Controllers\Staff\CampusWeeklyHolidayController::class, 'update'])->name('campus-weekly-holiday.update');
        Route::delete('campus-weekly-holiday/delete', [\App\Http\Controllers\Staff\CampusWeeklyHolidayController::class, 'destroy'])->name('campus-weekly-holiday.delete');

        Route::get('gazetted-leave/list', [GazettedLeaveController::class, 'index'])->name('gazettedleave.index');
        Route::get('gazetted-leave/create', [GazettedLeaveController::class, 'create'])->name('gazettedleave.create');
        Route::post('gazetted-leave/submit', [GazettedLeaveController::class, 'submit'])->name('gazettedleave.submit');
        Route::get('gazetted-leave/edit', [GazettedLeaveController::class, 'edit'])->name('gazettedleave.edit');
        Route::put('gazetted-leave/update', [GazettedLeaveController::class, 'update'])->name('gazettedleave.update');
        Route::delete('gazetted-leave/delete', [GazettedLeaveController::class, 'delete'])->name('gazettedleave.delete');

        Route::get('fine-deduction/list', [\App\Http\Controllers\Staff\FineDeductionController::class, 'index'])->name('finededuction.index');
        Route::get('fine-deduction/create', [\App\Http\Controllers\Staff\FineDeductionController::class, 'create'])->name('finededuction.create');
        Route::post('fine-deduction/submit', [\App\Http\Controllers\Staff\FineDeductionController::class, 'submit'])->name('finededuction.submit');
        Route::get('fine-deduction/edit', [\App\Http\Controllers\Staff\FineDeductionController::class, 'edit'])->name('finededuction.edit');
        Route::put('fine-deduction/update', [\App\Http\Controllers\Staff\FineDeductionController::class, 'update'])->name('finededuction.update');
        Route::delete('fine-deduction/delete', [\App\Http\Controllers\Staff\FineDeductionController::class, 'delete'])->name('finededuction.delete');

        Route::get('late-fine/list', [\App\Http\Controllers\Staff\LateFineController::class, 'index'])->name('latefine.index');
        Route::get('late-fine/create', [\App\Http\Controllers\Staff\LateFineController::class, 'create'])->name('latefine.create');
        Route::post('late-fine/submit', [\App\Http\Controllers\Staff\LateFineController::class, 'submit'])->name('latefine.submit');
        Route::get('late-fine/edit', [\App\Http\Controllers\Staff\LateFineController::class, 'edit'])->name('latefine.edit');
        Route::put('late-fine/update', [\App\Http\Controllers\Staff\LateFineController::class, 'update'])->name('latefine.update');
        Route::delete('late-fine/delete', [\App\Http\Controllers\Staff\LateFineController::class, 'delete'])->name('latefine.delete');

        Route::get('miscellaneous-payment/list', [\App\Http\Controllers\Staff\MiscellaneousPaymentController::class, 'index'])->name('miscellaneouspayment.index');
        Route::get('miscellaneous-payment/create', [\App\Http\Controllers\Staff\MiscellaneousPaymentController::class, 'create'])->name('miscellaneouspayment.create');
        Route::post('miscellaneous-payment/submit', [\App\Http\Controllers\Staff\MiscellaneousPaymentController::class, 'submit'])->name('miscellaneouspayment.submit');
        Route::get('miscellaneous-payment/edit', [\App\Http\Controllers\Staff\MiscellaneousPaymentController::class, 'edit'])->name('miscellaneouspayment.edit');
        Route::put('miscellaneous-payment/update', [\App\Http\Controllers\Staff\MiscellaneousPaymentController::class, 'update'])->name('miscellaneouspayment.update');
        Route::delete('miscellaneous-payment/delete', [\App\Http\Controllers\Staff\MiscellaneousPaymentController::class, 'delete'])->name('miscellaneouspayment.delete');

        Route::get('salary-tax/list', [\App\Http\Controllers\Staff\SalaryTaxController::class, 'index'])->name('salarytax.index');
        Route::get('salary-tax/create', [\App\Http\Controllers\Staff\SalaryTaxController::class, 'create'])->name('salarytax.create');
        Route::post('salary-tax/submit', [\App\Http\Controllers\Staff\SalaryTaxController::class, 'submit'])->name('salarytax.submit');
        Route::get('salary-tax/edit', [\App\Http\Controllers\Staff\SalaryTaxController::class, 'edit'])->name('salarytax.edit');
        Route::put('salary-tax/update', [\App\Http\Controllers\Staff\SalaryTaxController::class, 'update'])->name('salarytax.update');
        Route::delete('salary-tax/delete', [\App\Http\Controllers\Staff\SalaryTaxController::class, 'delete'])->name('salarytax.delete');

        Route::get('security-refund/list', [\App\Http\Controllers\Staff\SecurityRefundController::class, 'index'])->name('securityrefund.index');
        Route::get('security-refund/create', [\App\Http\Controllers\Staff\SecurityRefundController::class, 'create'])->name('securityrefund.create');
        Route::post('security-refund/submit', [\App\Http\Controllers\Staff\SecurityRefundController::class, 'submit'])->name('securityrefund.submit');
        Route::get('security-refund/edit', [\App\Http\Controllers\Staff\SecurityRefundController::class, 'edit'])->name('securityrefund.edit');
        Route::put('security-refund/update', [\App\Http\Controllers\Staff\SecurityRefundController::class, 'update'])->name('securityrefund.update');
        Route::delete('security-refund/delete', [\App\Http\Controllers\Staff\SecurityRefundController::class, 'delete'])->name('securityrefund.delete');

        Route::get('security-deduction/list', [\App\Http\Controllers\Staff\SecurityDeductionController::class, 'index'])->name('securitydeduction.index');
        Route::get('security-deduction/create', [\App\Http\Controllers\Staff\SecurityDeductionController::class, 'create'])->name('securitydeduction.create');
        Route::post('security-deduction/submit', [\App\Http\Controllers\Staff\SecurityDeductionController::class, 'submit'])->name('securitydeduction.submit');
        Route::get('security-deduction/edit', [\App\Http\Controllers\Staff\SecurityDeductionController::class, 'edit'])->name('securitydeduction.edit');
        Route::put('security-deduction/update', [\App\Http\Controllers\Staff\SecurityDeductionController::class, 'update'])->name('securitydeduction.update');
        Route::delete('security-deduction/delete', [\App\Http\Controllers\Staff\SecurityDeductionController::class, 'delete'])->name('securitydeduction.delete');

        Route::get('payroll-slip', [\App\Http\Controllers\Staff\PayrollSlipController::class, 'index'])->name('payrollslip.index');
        Route::get('payroll-slip/create', [\App\Http\Controllers\Staff\PayrollSlipController::class, 'create'])->name('payrollslip.create');
        Route::post('payroll-slip/store', [\App\Http\Controllers\Staff\PayrollSlipController::class, 'store'])->name('payrollslip.store');
        Route::get('payroll-slip/list', [\App\Http\Controllers\Staff\PayrollSlipController::class, 'show'])->name('payrollslip.show');
        Route::get('payroll-slip/detail', [\App\Http\Controllers\Staff\PayrollSlipController::class, 'detail'])->name('payrollslip.detail');
        Route::get('payroll-slip/download', [\App\Http\Controllers\Staff\PayrollSlipController::class, 'download'])->name('payrollslip.download')->withoutMiddleware('check_permission');
    });



    Route::group(['prefix' => 'feestype', 'middleware' => ['auth', 'check_permission']], function () {
        Route::get('/list', [FeesTypeController::class, 'index'])->name('fees.list');
        Route::get('/create', [FeesTypeController::class, 'create'])->name('fees.create');
        Route::post('/submit', [FeesTypeController::class, 'submit'])->name('fees.submit');
        Route::get('/edit', [FeesTypeController::class, 'edit'])->name('fees.edit');
        Route::post('/update', [FeesTypeController::class, 'update'])->name('fees.update');
        Route::delete('/delete', [FeesTypeController::class, 'delete'])->name('fees.delete');

    });
    Route::group(['prefix' => 'fees', 'middleware' => ['auth', 'check_permission']], function () {

        Route::get('optional-fee/list', [OptionalFeeMasterController::class, 'index'])->name('optionalfee.list');
        Route::get('optional-fee/create', [OptionalFeeMasterController::class, 'create'])->name('optionalfee.create');
        Route::post('optional-fee/submit', [OptionalFeeMasterController::class, 'submit'])->name('optionalfee.submit');
        Route::delete('optional-fee/destroy', [OptionalFeeMasterController::class, 'destroy'])->name('optionalfee.destroy');
        Route::post('optional-fee/create-fetch-student', [OptionalFeeMasterController::class, 'createFetchStudent'])->name('create.fetch.student')->withoutMiddleware('check_permission');

        Route::get('discount/list', [StudentFeeDiscountController::class, 'index'])->name('discount.list');
        Route::get('discount/create', [StudentFeeDiscountController::class, 'create'])->name('discount.create');
        Route::post('discount/submit', [StudentFeeDiscountController::class, 'submit'])->name('discount.submit');
        Route::get('discount/edit', [StudentFeeDiscountController::class, 'edit'])->name('discount.edit');
        Route::put('discount/update', [StudentFeeDiscountController::class, 'update'])->name('discount.update');
        Route::delete('discount/delete', [StudentFeeDiscountController::class, 'delete'])->name('discount.delete');
        Route::post('discount/optional-fee-mappng', [StudentFeeDiscountController::class, 'optionalFeeMapping'])->name('discount.optional.fee.mappng');
        Route::post('discount/optional-fee-mappng-master', [StudentFeeDiscountController::class, 'optionalFeeMappingMaster'])->name('discount.optional.fee.mappng.master');

        Route::get('generate-challan/list', [GenerateFeeChallanController::class, 'index'])->name('challan.list');
        Route::post('generate-challan/submit', [GenerateFeeChallanController::class, 'submit'])->name('challan.submit');
        Route::get('/challan/print', [GenerateFeeChallanController::class, 'print'])->name('challan.print')->withoutMiddleware('check_permission');
        // Route::get('/challans/print/{id}', [ChallanController::class, 'print'])->name('challan.print');
        Route::post('generate-challan/mark-as-unpaid', [GenerateFeeChallanController::class, 'markAsUnpaid'])->name('challan.markasunpaid');

        Route::get('delete-challan/list', [DeleteChallanController::class, 'index'])->name('deletechallan.list');
        Route::get('challan/fetch', [DeleteChallanController::class, 'fetchChallan'])->name('challan.list.fetch');
        Route::post('challan/`delete`', [DeleteChallanController::class, 'deleteChallan'])->name('challan.delete');

        Route::get('challan/list', [FeeCollectionController::class, 'index'])->name('fee.collection.list');
        Route::get('challan/collect', [FeeCollectionController::class, 'create'])->name('fee.collection');
        Route::post('challan/submit', [FeeCollectionController::class, 'submit'])->name('fee.collection.submit');
        Route::get('challan/receipt/{id}', [FeeCollectionController::class, 'receipt'])->name('fee.collection.receipt');
        Route::get('challan/detail', [FeeCollectionController::class, 'show'])->name('fee.challan.detail');
        Route::delete('challan/delete/{id}', [FeeCollectionController::class, 'delete'])->name('fee.challan.delete');

        Route::get('fee-master/list', [CampusFeesMasterController::class, 'index'])->name('feemaster.list');
        Route::get('fee-master/create', [CampusFeesMasterController::class, 'create'])->name('feemaster.create');
        Route::post('fee-maste/submit', [CampusFeesMasterController::class, 'submit'])->name('feemaster.submit');
        Route::get('fee-maste/edit', [CampusFeesMasterController::class, 'edit'])->name('feemaster.edit');
        Route::post('fee-maste/update', [CampusFeesMasterController::class, 'update'])->name('feemaster.update');
        Route::delete('fee-maste/delete', [CampusFeesMasterController::class, 'delete'])->name('feemaster.delete');
        Route::get('fee-maste/getclasses', [CampusFeesMasterController::class, 'getClasses'])->name('get.classes');

    });

    Route::group(['prefix' => 'sitesetting', 'middleware' => ['auth', 'check_permission']], function () {
        Route::get('list', [SiteSettingController::class, 'index'])->name('setting.index');
        Route::get('create', [SiteSettingController::class, 'create'])->name('setting.create');
        Route::post('submit', [SiteSettingController::class, 'submit'])->name('setting.submit');
        Route::get('edit', [SiteSettingController::class, 'edit'])->name('setting.edit');
        Route::put('update', [SiteSettingController::class, 'update'])->name('setting.update');
        // Route::delete('delete', [SiteSettingController::class, 'delete'])->name('setting.delete');
    });

    Route::group(['prefix' => 'exam', 'middleware' => ['auth', 'check_permission']], function () {
        Route::get('list', [ExamTermController::class, 'index'])->name('examterm.index');
        Route::get('create', [ExamTermController::class, 'create'])->name('examterm.create');
        Route::post('submit', [ExamTermController::class, 'submit'])->name('examterm.submit');
        Route::get('edit', [ExamTermController::class, 'edit'])->name('examterm.edit');
        Route::put('update', [ExamTermController::class, 'update'])->name('examterm.update');

        Route::get('subject/list', [ExamSubjectController::class, 'index'])->name('examsubject.index');
        Route::get('subject/create', [ExamSubjectController::class, 'create'])->name('examsubject.create');
        Route::post('subject/submit', [ExamSubjectController::class, 'submit'])->name('examsubject.submit');
        Route::get('subject/edit', [ExamSubjectController::class, 'edit'])->name('examsubject.edit');
        Route::put('subject/update', [ExamSubjectController::class, 'update'])->name('examsubject.update');
        Route::delete('subject/create', [ExamSubjectController::class, 'delete'])->name('examsubject.delete');

    });

    Route::group(['prefix' => 'exam', 'middleware' => ['auth', 'check_permission']], function () {
        Route::get('type/list', [ExamTypeController::class, 'index'])->name('examtype.index');
        Route::get('type/create', [ExamTypeController::class, 'create'])->name('examtype.create');
        Route::post('type/submit', [ExamTypeController::class, 'submit'])->name('examtype.submit');
        Route::get('type/edit', [ExamTypeController::class, 'edit'])->name('examtype.edit');
        Route::put('type/update', [ExamTypeController::class, 'update'])->name('examtype.update');

        Route::get('student/list', [ExamStudentController::class, 'index'])->name('examstudent.index');
        Route::get('student/create', [ExamStudentController::class, 'create'])->name('examstudent.create');
        Route::post('student/submit', [ExamStudentController::class, 'submit'])->name('examstudent.submit');
        Route::get('student/edit', [ExamStudentController::class, 'edit'])->name('examstudent.edit');
        Route::put('student/update', [ExamStudentController::class, 'update'])->name('examstudent.update');
        Route::get('student/exam-subjects', [ExamStudentController::class, 'getSubjects'])->name('examstudent.subjects');
        Route::delete('student/exam-delete', [ExamStudentController::class, 'delete'])->name('examstudent.delete');

        Route::get('marks/list', [MarksController::class, 'index'])->name('marks.index');
        Route::get('marks/create', [MarksController::class, 'create'])->name('marks.create');
        Route::post('marks/submit', [MarksController::class, 'submit'])->name('marks.submit');
        Route::get('marks/show', [MarksController::class, 'show'])->name('marks.show');
        Route::get('marks/edit', [MarksController::class, 'edit'])->name('marks.edit');
        Route::delete('marks/delete', [MarksController::class, 'delete'])->name('marks.delete');
        Route::put('marks/update', [MarksController::class, 'update'])->name('marks.update');
        Route::get('marks/data', [MarksController::class, 'getMarksData'])->name('marks.data');

        Route::get('grade/list', [MarksGradeController::class, 'index'])->name('marksgrade.list');
        Route::get('grade/create', [MarksGradeController::class, 'create'])->name('marksgrade.create');
        Route::post('grade/submit', [MarksGradeController::class, 'submit'])->name('marksgrade.submit');
        Route::get('grade/edit', [MarksGradeController::class, 'edit'])->name('marksgrade.edit');
        Route::delete('grade/delete', [MarksGradeController::class, 'delete'])->name('marksgrade.delete');
        Route::put('grade/update', [MarksGradeController::class, 'update'])->name('marksgrade.update');
        //

    });

    Route::group(['prefix' => 'reports', 'middleware' => ['auth', 'check_permission']], function () {
        Route::get('result-sheet', [ResultCardController::class, 'resultSheet'])->name('result.sheet');
        Route::get('result-card', [ResultCardController::class, 'resultCard'])->name('result.card');
        // Route::get('result-card', [ResultCardController::class, 'resultCard'])->name('result.card');

        // Route::get('/result-card/print', [ResultCardController::class, 'resultCard'])
        // ->name('result.card.print')->withoutMiddleware('check_permission');

        Route::get('get-exam-types', [ResultCardController::class, 'getExamTypes'])->name('getexam.types');
        Route::get('get-exam-students', [ResultCardController::class, 'getExamStudents'])->name('get.exam.students');
    });

    Route::group(['prefix' => 'master', 'middleware' => ['auth', 'check_permission']], function () {
        Route::get('report-list', [MasterReportController::class, 'reportList'])->name('masterreport.list');
        Route::get('student-detail', [MasterReportController::class, 'studentDetailReport'])->name('studentdetail.reports');
        Route::get('student-admission', [MasterReportController::class, 'studentAdmissionReport'])->name('studentadmission.report');
        Route::get('student-admission-inquiry', [MasterReportController::class, 'studentAdmissionInquiryReport'])->name('studentadmissioninquiry.report');
        Route::get('student-unpaid-fee', [MasterReportController::class, 'studentUnPaidFee'])->name('studentunpaid.fee');
        Route::get('student-summary-report', [MasterReportController::class, 'studentSummaryReport'])->name('studentsummary.report');
        Route::get('student-content-feedback', [MasterReportController::class, 'contentFeedbackReport'])->name('contentfeedback.report');
        Route::get('student-employee-report', [MasterReportController::class, 'employeeReport'])->name('employee.report');
        Route::get('student-ledger-report', [MasterReportController::class, 'studentLedger'])->name('studentledger.report');
        Route::get('assesment-report-exam', [MasterReportController::class, 'assesmentReportExam'])->name('assesmentexam.report')->withoutMiddleware('check_permission');
        Route::get('assesment-exam-class', [MasterReportController::class, 'assesmentExamClass'])->name('assesmentexam.class')->withoutMiddleware('check_permission');
        Route::get('assesment-report-fetch', [MasterReportController::class, 'assesmentReportFetch'])->name('assesmentreport.fetch');
        Route::get('term-wise-fetch', [MasterReportController::class, 'termWiseFetch'])->name('termwise.fetch');
        Route::get('student-attendance-fetch', [MasterReportController::class, 'studentAttendanceFetch'])->name('studentattendance.report');
        Route::get('staff-attendance-fetch', [MasterReportController::class, 'staffAttendanceReport'])->name('staffattendance.report');
        Route::get('fee-summary-head-wise-fetch', [MasterReportController::class, 'feeSummaryHeadWiseReport'])->name('feesummaryheadwise.report');
        Route::get('paren-profession-report', [MasterReportController::class, 'parentProfessionReport'])->name('parentprofession.report');

    });

    Route::group(['prefix' => 'report', 'middleware' => ['auth', 'check_permission']], function () {
        Route::get('student-information', [MasterReportController::class, 'studentInformation'])->name('student.information');
        Route::get('student-information-fetch', [MasterReportController::class, 'studentInformationFetch'])->name('studentinformation.fetch');
        Route::get('fee-collection-report', [MasterReportController::class, 'feeCollectionReport'])->name('fee.collectionreport');
        Route::get('fee-collection-summary', [MasterReportController::class, 'feeCollectionReportSummary'])->name('fee.collectionsummary');
        Route::get('fee-collection-report-fetch', [MasterReportController::class, 'feeCollectionReportFetch'])->name('fee.collectionreport.fetch');
        Route::get('fee-collection-summary-fetch', [MasterReportController::class, 'feeCollectionSummaryFetch'])->name('fee.collectionsummary.fetch');
        Route::get('daily-fee-collection', [MasterReportController::class, 'dailyFeeCollection'])->name('fee.dailycollection');
        Route::get('daily-fee-collection-fetch', [MasterReportController::class, 'dailyFeeCollectionFetch'])->name('fee.dailycollection.fetch');
        Route::get('student-fee-balance', [MasterReportController::class, 'studentFeebalanceFetch'])->name('student.feebalance');
        Route::get('student-fee-balance-fetch', [MasterReportController::class, 'studentFeebalance'])->name('student.feebalance.fetch');

        Route::get('student-sibling-report', [MasterReportController::class, 'studentSiblingReport'])->name('sibling.report');
        Route::get('fetch-all-sibling-students', [MasterReportController::class, 'FetchAllSiblingStudents'])->name('inquiry.fetch.all.siblings');
        Route::get('student-sibling-report-all-pdf', [MasterReportController::class, 'FetchAllSiblingStudentsPdf'])->name('sibling.report.all.pdf')->withoutMiddleware('check_permission');

        Route::get('school-fee-ledger-report', [MasterReportController::class, 'schoolFeeLedgerReport'])->name('schoolfeeledger.report');

        Route::get('student-withdraw-report', [MasterReportController::class, 'studentWithdrawReport'])->name('student.withdraw.report');
        Route::get('student-withdraw-report-fetch', [MasterReportController::class, 'studentWithdrawReportFetch'])->name('student.withdraw.report.fetch');
    });

    Route::group(['prefix' => 'sms', 'middleware' => ['auth', 'check_permission']], function () {
        Route::get('credit-list', [SmsCreditController::class, 'index'])->name('smscredit.index');
        Route::get('credit-create', [SmsCreditController::class, 'create'])->name('smscredit.create');
        Route::post('submit', [SmsCreditController::class, 'submit'])->name('smscredit.submit');

        Route::get('log-list', [SmsCreditController::class, 'logList'])->name('smslog.index');
        Route::get('log-detail', [SmsCreditController::class, 'logDetail'])->name('smslog.detail');

        Route::get('create-sms', [SendSMSController::class, 'createSMS'])->name('SendSMS.create');

        Route::get('/sections/{class_id}', [SendSMSController::class, 'getByClass'])->withoutMiddleware('check_permission');

        //    Route::get('send-sms', [SendSMSController::class, 'sendSms'])->name('SendSMS.submit');

        Route::get('/get-contacts', [SendSMSController::class, 'getContacts'])->name('get.contacts');
        Route::post('send-sms', [SendSMSController::class, 'sendSms'])->name('SendSMS.submit');
        Route::get('remaining-credit', function () {
            $tenantId = tenant('id');
            $remainingCredit = \App\Helpers\SmsHelper::getRemainingCredits($tenantId);

            return response()->json([
                'remainingCredit' => $remainingCredit,
            ]);
        })->name('sms.remaining-credit');
    });

    Route::group(['prefix' => 'phonebookgroup', 'middleware' => ['auth', 'check_permission']], function () {
        Route::get('list', [PhonebookGroupController::class, 'index'])->name('phonebookgroup.index');
        Route::post('submit', [PhonebookGroupController::class, 'submit'])->name('phonebookgroup.submit');
        Route::put('update', [PhonebookGroupController::class, 'update'])->name('phonebookgroup.update');
        Route::delete('delete', [PhonebookGroupController::class, 'delete'])->name('phonebookgroup.delete');
    });

    Route::group(['prefix' => 'phonebook', 'middleware' => ['auth', 'check_permission']], function () {
        Route::get('list', [PhonebookController::class, 'index'])->name('phonebook.index');
        Route::get('create', [PhonebookController::class, 'create'])->name('phonebook.create');
        Route::post('submit', [PhonebookController::class, 'submit'])->name('phonebook.submit');
        Route::get('show-details', [PhonebookController::class, 'show'])->name('phonebook.show');
        Route::get('edit', [PhonebookController::class, 'edit'])->name('phonebook.edit');
        Route::put('update', [PhonebookController::class, 'update'])->name('phonebook.update');
        Route::delete('delete', [PhonebookController::class, 'delete'])->name('phonebook.delete');
    });

    // Route::group(['prefix' => 'apiimport', 'middleware' => ['auth']], function () {
    //     Route::get('api-list', [ImportController::class, 'getApiList'])->name('get.all.api.list');
    //     Route::get('get-session', [ImportController::class, 'getSessionsList'])->name('get.all.session');
    //     Route::get('get-campus', [ImportController::class, 'getCampusList'])->name('get.all.campus');
    //     Route::get('get-classes', [ImportController::class, 'getClassesList'])->name('get.all.classes');
    //     Route::get('get-sections', [ImportController::class, 'getSectionList'])->name('get.all.sections');
    //     Route::get('get-subjects', [ImportController::class, 'getSubjectList'])->name('get.all.subjects');
    //     Route::get('get-student', [ImportController::class, 'getStudentList'])->name('get.all.student');
    //     Route::get('get-student-inquiry-lost', [ImportController::class, 'getStudentInquiryLostList'])->name('get.all.student.inquiry.lost');
    //     Route::get('get-student-attenance', [ImportController::class, 'getStudentAttendanceList'])->name('get.all.student.attendance');
    //     Route::get('get-fee-stracture', [ImportController::class, 'getFeeStracture'])->name('get.all.fees.stracture');
    //     Route::get('get-optional-fee-mapping', [ImportController::class, 'getOptionalFeeMapping'])->name('get.all.optional.fee.mapping');
    //     Route::get('get-student-fee-discount', [ImportController::class, 'getStudentFeeDiscount'])->name('get.all.student.fee.discount');
    //     Route::get('get-student-fee-challan', [ImportController::class, 'getStudentFeeChallan'])->name('get.all.student.fee.challan');
    //     Route::get('get-student-fee-challan-transcation', [ImportController::class, 'getStudentFeeChallanTranscation'])->name('get.all.student.fee.challan.transcation');
    //     Route::get('get-student-fee-challan-payment', [ImportController::class, 'getStudentChallanPartialPayment'])->name('get.all.challan.partial.payment');
    //     Route::get('get-student-fee-challan-arrears', [ImportController::class, 'getStudentChallanArrears'])->name('student.fee.challan.arrears');
    //     Route::get('get-exam-terms', [ImportController::class, 'getExamTerms'])->name('get.exam.terms');
    //     Route::get('get-import-exam-types', [ImportController::class, 'getImportExamTypes'])->name('get.exam.types');
    //     Route::get('get-import-exam-subject', [ImportController::class, 'getImportExamSubject'])->name('get.exam.subject');
    //     Route::get('get-import-exam-student', [ImportController::class, 'getImportExamStudent'])->name('get.imported.exam.student');
    //     Route::get('get-import-exam-marks', [ImportController::class, 'getImportExamMarks'])->name('get.exam.marks');
    //     Route::get('get-import-exam-marks-detail', [ImportController::class, 'getImportExamMarksDetail'])->name('get.exam.marks.detail');
    //     Route::get('get-import-exam-marks-grade', [ImportController::class, 'getImportExamMarksGrade'])->name('get.exam.marks.grade');
    //     Route::get('get-department-list', [ImportController::class, 'getDepartmentList'])->name('get.department.list');
    //     Route::get('get-designation-list', [ImportController::class, 'getDesignationList'])->name('get.designation.list');
    //     Route::get('get-staff-list', [ImportController::class, 'getStaffList'])->name('get.staff.list');
    //     Route::get('get-assign-class-teache', [ImportController::class, 'getAssignClassTeacherList'])->name('get.assign.class.teacher');
    //     Route::get('get-class-time-table', [ImportController::class, 'getClassTimeTable'])->name('get.class.time.table');
    //     Route::get('get-user-list', [ImportController::class, 'getUserList'])->name('get.user.list');
    //     Route::get('get-roles-list', [ImportController::class, 'getRolesList'])->name('get.roles.list');
    //     Route::get('get-upload-content-group-list', [ImportController::class, 'getUploadContentGroupList'])->name('get.upload.content.group.list');
    //     Route::get('get-upload-content-list', [ImportController::class, 'getUploadContentList'])->name('get.upload.content.list');
    //     Route::get('get-upload-content-log-list', [ImportController::class, 'getUploadContentLogList'])->name('get.upload.content.log.list');
    //     Route::get('get-home-work-list', [ImportController::class, 'getHomeWorkList'])->name('get.home.work.list');
    //     Route::get('get-sms-credit-list', [ImportController::class, 'getSMSCreditList'])->name('get.sms-credit.list');
    //     Route::get('get-sms-log-list', [ImportController::class, 'getSMSLogList'])->name('get.sms.log.list');
    // });

    Route::post('uploads/content/fetch-subject', [ContentUploadController::class, 'classSubjectFetch'])->name('class.subject.fetch');

    Route::get('/testing', function () {
        return 'This is your multi-tenant application for admin. The id of the current tenant is '.tenant('id');
    });
});

// dhkdhjdkahsjk

require __DIR__.'/auth.php';
