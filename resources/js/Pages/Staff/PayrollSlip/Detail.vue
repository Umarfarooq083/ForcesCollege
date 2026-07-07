<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    payrollSlip: Object,
});

const months = {
    1: 'January', 2: 'February', 3: 'March', 4: 'April',
    5: 'May', 6: 'June', 7: 'July', 8: 'August',
    9: 'September', 10: 'October', 11: 'November', 12: 'December',
};

const monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

const downloadPdf = () => {
    window.open(route('payrollslip.download', { id: props.payrollSlip.id }), '_blank');
};
</script>

<template>
    <Head title="Payroll Slip Detail" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Payroll Slip Detail</h2>
        </template>

        <div class="card">
            <div class="header">
                <div class="w-100 d-inline-flex justify-content-between align-items-center">
                    <div class="font-weight-bold">Payroll Slip - {{ months[payrollSlip.payroll_month] }} {{ payrollSlip.payroll_year }}</div>
                    <div class="d-flex gap-2">
                        <button @click="downloadPdf" class="btn btn-success btn-sm" title="Download PDF">
                            <i class="fa fa-download"></i>
                        </button>
                        <Link :href="route('payrollslip.show')" class="btn btn-secondary btn-sm">
                            Back to List
                        </Link>
                    </div>
                </div>
            </div>

            <div class="body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th colspan="2" class="bg-primary text-white">Staff Information</th>
                        </tr>
                        <tr>
                            <td><strong>Staff Name</strong></td>
                            <td>{{ payrollSlip.staff?.FirstName }} {{ payrollSlip.staff?.LastName }}</td>
                        </tr>
                        <tr>
                            <td><strong>Staff Code</strong></td>
                            <td>{{ payrollSlip.staff?.StaffCode || payrollSlip.staff?.id }}</td>
                        </tr>
                        <tr>
                            <td><strong>Designation</strong></td>
                            <td>{{ payrollSlip.staff?.designation_rel?.DesignationName || 'N/A' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Department</strong></td>
                            <td>{{ payrollSlip.staff?.department_rel?.DepartmentName || 'N/A' }}</td>
                        </tr>
                    </table>

                    <table class="table table-bordered">
                        <tr>
                            <th colspan="2" class="bg-info text-white">Earnings</th>
                        </tr>
                        <tr>
                            <td>Basic Salary</td>
                            <td class="text-end">{{ payrollSlip.basic_salary }}</td>
                        </tr>
                        <tr>
                            <td>Transport Allowance</td>
                            <td class="text-end">{{ payrollSlip.transport_allowance }}</td>
                        </tr>
                        <tr>
                            <td>Computer Allowance</td>
                            <td class="text-end">{{ payrollSlip.computer_allowance }}</td>
                        </tr>
                        <tr>
                            <td>Mobile Allowance</td>
                            <td class="text-end">{{ payrollSlip.mobile_allowance }}</td>
                        </tr>
                        <tr>
                            <td>Recreation Allowance</td>
                            <td class="text-end">{{ payrollSlip.recreation_allowance }}</td>
                        </tr>
                        <tr>
                            <td><strong>Gross Salary</strong></td>
                            <td class="text-end"><strong>{{ payrollSlip.gross_salary }}</strong></td>
                        </tr>
                    </table>

                    <table class="table table-bordered">
                        <tr>
                            <th colspan="2" class="bg-warning text-white">Attendance Details</th>
                        </tr>
                        <tr>
                            <td>Working Days</td>
                            <td class="text-end">{{ payrollSlip.working_days }}</td>
                        </tr>
                        <tr>
                            <td>Present Days</td>
                            <td class="text-end">{{ payrollSlip.present_days }}</td>
                        </tr>
                        <tr>
                            <td>Absent Days</td>
                            <td class="text-end">{{ payrollSlip.absent_days }}</td>
                        </tr>
                        <tr>
                            <td>Leave Days</td>
                            <td class="text-end">{{ payrollSlip.leave_days }}</td>
                        </tr>
                        <tr>
                            <td>Gazetted Leaves Count</td>
                            <td class="text-end">{{ payrollSlip.gazetted_leaves_count }}</td>
                        </tr>
                    </table>

                    <table class="table table-bordered">
                        <tr>
                            <th colspan="2" class="bg-danger text-white">Deductions</th>
                        </tr>
                        <tr>
                            <td>Absent Deduction</td>
                            <td class="text-end">{{ payrollSlip.total_absent_deduction }}</td>
                        </tr>
                        <tr>
                            <td>Fine Deduction</td>
                            <td class="text-end">{{ payrollSlip.fine_deduction }}</td>
                        </tr>
                        <tr>
                            <td>Salary Tax</td>
                            <td class="text-end">{{ payrollSlip.salary_tax }}</td>
                        </tr>
                        <tr>
                            <td>Security Deduction</td>
                            <td class="text-end">{{ payrollSlip.security_deduction }}</td>
                        </tr>
                    </table>

                    <table class="table table-bordered">
                        <tr>
                            <th colspan="2" class="bg-success text-white">Other Payments</th>
                        </tr>
                        <tr>
                            <td>Miscellaneous Payment / Overtime</td>
                            <td class="text-end">{{ payrollSlip.miscellaneous_payment }}</td>
                        </tr>
                        <tr>
                            <td>Security Refund</td>
                            <td class="text-end">{{ payrollSlip.security_refund }}</td>
                        </tr>
                    </table>

                    <table class="table table-bordered">
                        <tr class="bg-dark text-white">
                            <th><h4>Net Salary</h4></th>
                            <td class="text-end"><h4><strong>{{ payrollSlip.net_salary }}</strong></h4></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>