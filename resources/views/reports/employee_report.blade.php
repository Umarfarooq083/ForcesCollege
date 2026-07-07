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
          <img src="{{ public_path('assets/images/Logo.jpeg') }}" width="100%" alt="School Logo">
        </div>
      </div>

      <div class="heading-container">
        <h1 class="maintxt">FORCES SCHOOL AND COLLEGE SYSTEM</h1>
        <p class="maintxt">Employee Record</p>
      </div>

      <div class="meta-container">
        <div><span class="meta-label">Campus:</span> <span class="meta-content">{{ $meta['campus'] ?? '' }}</span></div>
      </div>
    </div>

    <table class="table table-bordered mb-0">
      <thead>
        <tr>
          <th>SR. No.</th>
          <th>First Name</th>
          <th>Last Name</th>
          <th>Monile No</th>
          <th>Address</th>
          <th>Date Of Joining</th>
          <th>Designation</th>
          <th>Qualification</th>
          <th>Work Experience</th>
          <th>Salary</th>
          <th>Portal Status</th>
        </tr>
      </thead>
      <tbody>
        @forelse($staffData as $idx => $staff)
        <tr>
          <td>{{ $idx + 1 }}</td>
          <td>{{ $staff->FirstName }}</td>
          <td>{{ $staff?->LastName }}  </td>
          <td>{{ $staff?->Phone }}</td>
          <td>{{ $staff?->CurrentAddress }}</td>
          <td>{{ \Carbon\Carbon::parse($staff->DateOfJoining)->format('Y-m-d')  }}</td>
          <td>{{ $staff?->DesignationRel->DesignationName }}</td>
          <td>{{ $staff?->Qualification }}</td>
          <td>{{ $staff?->WorkExperience }}</td>
          <td>{{ $staff?->BasicSalary}}</td>
          <td>
            @if($staff?->IsActive == 1)
              Active
            @endif
            </td>
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