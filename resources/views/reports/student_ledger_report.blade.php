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
      width: 100%;
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
    .text-center{
      text-align: center;
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
        <h3 class="maintxt">FORCES SCHOOL AND COLLEGE SYSTEM</h3>
        <p class="maintxt" style="font-size: 18px;">Student Ledger Report</p>
      </div>

      <div class="meta-container">
        <div><span class="meta-label">Campus:</span> <span class="meta-content">{{ $meta['campus'] ?? '' }}</span></div>
      </div>
    </div>

    <table class="table table-bordered mb-0 text-center">
      <thead>
        <tr>
          <th>SR. No.</th>
          <th>Challan No</th>
          <th>Due Date</th>
          <th>Collect Date</th>
          <th>Amount Due</th>
          {{-- <th>Total Due</th> --}}
          <th>Amount Paid</th>
          <th>Waived off</th>
          <th>Balance</th>
          <th>Billing Month</th>
          <th>Session Name</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($generateChallanData as $idx => $challanData)
        <tr>
          <td>{{ $idx + 1 }}</td>
          <td>{{ $challanData->challan_no }} </td>
          <td>{{ \Carbon\Carbon::parse($challanData?->DueDate)->format('Y-m-d')  }}  </td>
          <td>{{ \Carbon\Carbon::parse($challanData?->CollectDate)->format('Y-m-d') }}</td>
          <td>{{ $challanData?->amount_due ?? 0 }}</td>
          {{-- <td>{{ $challanData?->amount_due ?? 0 }}</td> --}}
          <td>{{ $challanData?->amount_paid ?? 0 }}</td>
          <td>{{ $challanData?->WaivedFineAmount ?? 0 }}</td>
          <td>{{ $challanData?->balance ?? 0 }}</td>
          <td>{{ \Carbon\Carbon::parse($challanData?->ChallanMonth)->format('Y-m-d') }}</td>
          <td>{{ \Carbon\Carbon::parse($challanData?->session_rel?->start_date)->format('Y-m-d') }} - {{\Carbon\Carbon::parse($challanData?->session_rel?->end_date)->format('Y-m-d') }}</td>
          <td>{{ $challanData?->Status }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="13" class="text-center">No records found</td>
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