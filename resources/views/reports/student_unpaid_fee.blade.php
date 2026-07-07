<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Admission Report</title>
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
          <img src="{{ public_path('assets/images/logo.jpeg') }}" width="100%" alt="School Logo">
        </div>
      </div>

      <div class="heading-container">
        <h1 class="maintxt">FORCES SCHOOL AND COLLEGE SYSTEM</h1>
      </div>

      <div class="meta-container">
        <div><span class="meta-label">Campus:</span> <span class="meta-content">{{ $meta['campus'] ?? '' }}</span></div>
        <div><span class="meta-label">Month:</span> <span class="meta-content">{{ $meta['month'] ?? '' }}</span></div>
        <div><span class="meta-label">Year:</span> <span class="meta-content">{{ $meta['year'] ?? '' }}</span></div>
      </div>
    </div>

    <table class="table table-bordered mb-0">
      <thead>
        <tr>
          <th>SR. No.</th>
          <th>Student Name</th>
          <th>Class</th>
          <th>Section</th>
          <th>Challan No</th>
          <th>Billing Month </th>
          <th>Due Date</th>
          <th>Total Amount</th>
        </tr>
      </thead>
      <tbody>
        @forelse($generated_challan as $idx => $unpaidChallan)

          @php
            // Calculate amount for this student
            $amount = ($unpaidChallan['transection_sum_balancefeeafterdiscount'] ?? 0)
            + ($unpaidChallan['total_arrear_fine'] ?? 0)
            + ($unpaidChallan['total_arrears_amount'] ?? 0)
            - ($unpaidChallan['arrear_partial_sum'] ?? 0);

          @endphp


          <tr>
            <td>{{ $idx + 1 }}</td>
            <td>{{ $unpaidChallan?->student?->FirstName }} {{ $unpaidChallan?->student?->LastName }}</td>
            <td>{{ $unpaidChallan?->student?->class?->ClassName }} </td>
            <td>{{ $unpaidChallan?->student?->section?->SectionName }} </td>
            <td>{{ $unpaidChallan?->challan_no }}</td>
            <td>{{ $unpaidChallan?->ChallanMonth }}</td>
            <td>{{ $unpaidChallan?->DueDate }}</td>
            <td>{{ $amount }}</td>
            {{-- <td>{{ $unpaidChallan?->transection_sum_balancefeeafterdiscount }}</td> --}}
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center">No records found</td>
          </tr>
        @endforelse
      </tbody>
    </table>

    <div class="footer-note">
      RptStdAdmissionInquiry
    </div>
  </div>
</body>

</html>