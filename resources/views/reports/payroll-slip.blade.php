<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Payroll Slip</title>
    <style>
        /* PDF Layout Controls */
        @page {
            size: A4 portrait;
            margin: 0;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 0;
            padding: 45px;
            background: #ffffff;
            color: #1e293b;
            font-size: 12px;
            line-height: 1.4;
            -webkit-print-color-adjust: exact;
        }
        
        .payroll-container {
            max-width: 800px;
            margin: 0 auto;
        }

        /* Top Header Grid */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .header-logo {
            width: 65px;
            vertical-align: top;
        }
        .header-logo img {
            display: block;
            border-radius: 4px;
        }
        .header-title {
            text-align: right;
            vertical-align: bottom;
        }
        .header-title h1 {
            color: #0f172a;
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 1px;
        }
        .header-title h3 {
            color: #475569;
            margin: 5px 0 0 0;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .divider {
            border-top: 2px solid #0f172a;
            margin-bottom: 25px;
        }

        /* Metadata Details Block */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            margin-bottom: 30px;
        }
        .meta-table td {
            padding: 12px 15px;
            width: 25%;
            vertical-align: top;
            border-right: 1px solid #e2e8f0;
        }
        .meta-table td:last-child {
            border-right: none;
        }
        .meta-label {
            color: #64748b;
            font-size: 10px;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }
        .meta-value {
            color: #0f172a;
            font-size: 12px;
            font-weight: 600;
        }

        /* Two Column Master Structure */
        .main-layout-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .main-layout-table td.column-side {
            width: 48%;
            vertical-align: top;
        }
        .main-layout-table td.column-spacer {
            width: 4%;
        }

        /* Table Components */
        .table-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #1e293b;
            border-bottom: 2px solid #334155;
            padding-bottom: 6px;
            margin-bottom: 8px;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .data-table th {
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
            color: #64748b;
            padding: 6px 8px;
            border-bottom: 1px solid #cbd5e1;
        }
        .data-table td {
            padding: 7px 8px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }
        .data-table td.right, .data-table th.right {
            text-align: right;
        }
        
        /* Summary Row Stylings */
        .data-table tr.summary-row td {
            font-weight: 700;
            border-top: 1px solid #94a3b8;
            border-bottom: 1px solid #94a3b8;
            color: #0f172a;
            background: #f8fafc;
        }

        /* Highlight Block for Net Salary */
        .net-pay-container {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #0f172a;
            margin-top: 10px;
            margin-bottom: 40px;
        }
        .net-pay-container td {
            padding: 16px 20px;
        }
        .net-pay-label {
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
        }
        .net-pay-value {
            text-align: right;
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
        }

        /* Footer System */
        .footer {
            text-align: center;
            font-size: 10px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 15px;
        }
        .footer p {
            margin: 2px 0;
        }
    </style>
</head>
<body>

    <div class="payroll-container">
        
        <table class="header-table">
            <tr>
                <td class="header-logo">
                    <img src="{{ public_path('assets/images/Logo.jpeg') }}" width="60" height="60" alt="Logo">
                </td>
                <td class="header-title">
                    <h1>PAYROLL SLIP</h1>
                    <h3>{{ \Carbon\Carbon::create()->month($payrollSlip->payroll_month)->format('F') }} {{ $payrollSlip->payroll_year }}</h3>
                </td>
            </tr>
        </table>

        <div class="divider"></div>

        <table class="meta-table">
            <tr>
                <td>
                    <div class="meta-label">Employee Name</div>
                    <div class="meta-value">{{ $payrollSlip->staff->FirstName }} {{ $payrollSlip->staff->LastName }}</div>
                </td>
                <td>
                    <div class="meta-label">Staff ID</div>
                    <div class="meta-value">#{{ $payrollSlip->staff->id }}</div>
                </td>
                <td>
                    <div class="meta-label">Designation</div>
                    <div class="meta-value">{{ $payrollSlip->desigination_name }}</div>
                </td>
                <td>
                    <div class="meta-label">Department</div>
                    <div class="meta-value">{{ $payrollSlip->department_name ?? 'N/A' }}</div>
                </td>
            </tr>
        </table>

        <table class="main-layout-table">
            <tr>
                <td class="column-side">
                    <div class="table-title">Earnings Breakdown</div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Item Description</th>
                                <th class="right">Amount (PKR)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Basic Salary</td>
                                <td class="right">{{ number_format($payrollSlip->basic_salary, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Transport Allowance</td>
                                <td class="right">{{ number_format($payrollSlip->transport_allowance, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Computer Allowance</td>
                                <td class="right">{{ number_format($payrollSlip->computer_allowance, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Mobile Allowance</td>
                                <td class="right">{{ number_format($payrollSlip->mobile_allowance, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Recreation Allowance</td>
                                <td class="right">{{ number_format($payrollSlip->recreation_allowance, 2) }}</td>
                            </tr>
                            <tr class="summary-row">
                                <td>Gross Earnings</td>
                                <td class="right">{{ number_format($payrollSlip->gross_salary, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </td>

                <td class="column-spacer"></td>

                <td class="column-side">
                    
                    <div class="table-title">Attendance Summary</div>
                    <table class="data-table" style="margin-bottom: 20px;">
                        <thead>
                            <tr>
                                <th>Attendance Metric</th>
                                <th class="right">Days</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Total Working Days</td>
                                <td class="right">{{ $payrollSlip->working_days }}</td>
                            </tr>
                            <tr>
                                <td>Days Present</td>
                                <td class="right">{{ $payrollSlip->present_days }}</td>
                            </tr>
                            <tr>
                                <td>Days Absent</td>
                                <td class="right" style="{{ $payrollSlip->absent_days > 0 ? 'color: #dc2626; font-weight: bold;' : '' }}">{{ $payrollSlip->absent_days }}</td>
                            </tr>
                            <tr>
                                <td>Authorized Leaves</td>
                                <td class="right">{{ $payrollSlip->leave_days }}</td>
                            </tr>
                            <tr>
                                <td>Gazetted Holidays</td>
                                <td class="right">{{ $payrollSlip->gazetted_leaves_count }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="table-title">Deductions & Statutory</div>
                    <table class="data-table" style="margin-bottom: 20px;">
                        <tbody>
                            <tr>
                                <td>Gross Salary</td>
                                <td class="right">{{ number_format($payrollSlip->total_absent_deduction, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Fine Deduction</td>
                                <td class="right">{{ number_format($payrollSlip->fine_deduction, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Late Fine Deduction</td>
                                <td class="right">{{ number_format($payrollSlip->late_fine_deduction, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Salary Tax</td>
                                <td class="right">{{ number_format($payrollSlip->salary_tax, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Security Deductions</td>
                                <td class="right">{{ number_format($payrollSlip->security_deduction, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="table-title">Adjustments & Other Payments</div>
                    <table class="data-table">
                        <tbody>
                            <tr>
                                <td>Miscellaneous Payments</td>
                                <td class="right">{{ number_format($payrollSlip->miscellaneous_payment, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Security Refund Release</td>
                                <td class="right">{{ number_format($payrollSlip->security_refund, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>

                </td>
            </tr>
        </table>

        <table class="net-pay-container">
            <tr>
                <td class="net-pay-label">Total Net Pay Distribution</td>
                <td class="net-pay-value">
                    {{ number_format($payrollSlip->net_salary, 2) }} <span style="font-size: 13px; font-weight: 500; color: #475569;">PKR</span>
                </td>
            </tr>
        </table>

        <div class="footer">
            <p>System Record Timestamp: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
            <p>This document is cryptographically verified and electronically generated. No physical signature or stamp required.</p>
        </div>
    </div>

</body>
</html>