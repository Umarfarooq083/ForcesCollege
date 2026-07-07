<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Withdraw Report</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
  <style>
    body {
      font-family: Arial, sans-serif;
      -webkit-print-color-adjust: exact;
    }
    .report-header {
      display: inline-block;
      border-bottom: 1px solid #000;
      padding-bottom: 10px;
      margin-bottom: 20px;
      width: 100%;
    }
    .logo-container {
      display: inline-block;
      width: 20%;
    }
    .school-logo {
      width: 80px;
      height: 80px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      font-size: 12px;
    }
    .heading-container {
      width: 50%;
      display: inline-block;
      text-align: center;
    }
    .maintxt {
      font-size: 24px;
      font-weight: bold;
      margin: 0;
    }
    .meta-container {
      width: 28%;
      display: inline-block;
      text-align: right;
      font-size: 13px;
      line-height: 1.4;
    }
    .meta-container div {
      width: 100%;
      display: inline-block;
    }
    .meta-label {
      font-weight: bold;
      display: inline-block;
      width: 80px;
      text-align: left;
    }
    span.meta-content {
      width: 80px;
      display: inline-block;
      text-align: left;
    }
    .table {
      border-collapse: collapse;
    }
    .table thead th {
      border-bottom: 1px solid #1e3e1d !important;
      padding: 6px 8px;
      font-size: 13px;
      color: #1e3e1d;
    }
    .table tbody td {
      border-bottom: 1px solid #000 !important;
      padding: 6px 8px;
      font-size: 13px;
      vertical-align: middle;
    }
    .center {
      text-align: center;
    }
    .footer-note {
      margin-top: 20px;
      font-size: 12px;
      text-align: left;
    }
  </style>
</head>

<body>
  <div class="a4-page">
    <div class="report-header">
      <div class="logo-container">
        <div class="school-logo">
          <img src="{{ public_path('assets/images/Logo.jpeg') }}" width="100%" alt="School Logo">
        </div>
      </div>

      <div class="heading-container">
        <h1 class="maintxt">FORCES SCHOOL AND COLLEGE SYSTEM</h1>
        <p>Student Withdraw Report</p>
      </div>

      <div class="meta-container">
        <div><span class="meta-label">Campus:</span> <span class="meta-content">{{ $meta['campus'] ?? '' }}</span></div>
        <div><span class="meta-label">Class:</span> <span class="meta-content">{{ $meta['class_name'] ?? '' }}</span></div>
        <div><span class="meta-label">Section:</span> <span class="meta-content">{{ $meta['section_name'] ?? '' }}</span></div>
      </div>
    </div>

    <table class="table table-bordered mb-0">
      <thead>
        <tr>
          <th>SR. No.</th>
          <th>Admission Date</th>
          <th>Roll Number</th>
          <th>Student Name</th>
          <th>Gender</th>
          <th>Class Name</th>
          <th>Section Name</th>
          <th>Challan No</th>
          <th>Challan Amount</th>
          <th>Challan Status</th>
          <th>Withdrawal Date</th>
          <th>Reason</th>
        </tr>
      </thead>
      <tbody>
        @forelse($students as $idx => $student)
        <tr>
          <td class="center">{{ $idx + 1 }}</td>
          <td class="center">{{ \Carbon\Carbon::parse($student->AdmissionDate)->format('d-M-Y') }}</td>
          <td class="center">{{ $student?->RollNumber }}</td>
          <td class="center">{{ $student?->FirstName }} {{ $student?->LastName }}</td>
          <td class="center">{{ $student?->Gender }}</td>
          <td class="center">{{ $student?->class?->ClassName }}</td>
          <td class="center">{{ $student?->section?->SectionName }}</td>
          <td class="center">{{ $student?->last_challan_no ?? 'N/A' }}</td>
          <td class="center">{{ $student?->last_challan_amount ?? 'N/A' }}</td>
          <td class="center">{{$student?->withdraw_status }}</td>
          <td class="center">{{ $student?->withdraw_date ? \Carbon\Carbon::parse($student->withdraw_date)->format('d-M-Y') : 'N/A' }}</td>
          <td class="center">{{ $student?->withdraw_reason ?? 'N/A' }}</td>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="12" class="text-center">No records found</td>
        </tr>
        @endforelse
      </tbody>
    </table>

    <div class="footer-note">
      RptStdWithdraw
    </div>
  </div>
</body>
</html>