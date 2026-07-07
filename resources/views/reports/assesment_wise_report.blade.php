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
        <p class="maintxt">Assessment Wise Report</p>
      </div>

      <div class="meta-container">
        <div><span class="meta-label">Campus:</span> <span class="meta-content">{{ $meta['campus'] ?? '' }}</span></div>
        <div><span class="meta-label">Start Date:</span> <span class="meta-content">{{ $meta['start_date'] ?? '' }}</span></div>
        <div><span class="meta-label">End Date:</span> <span class="meta-content">{{ $meta['end_date'] ?? '' }}</span></div>
      </div>
    </div>

   <table border="1" width="100%" cellspacing="0" cellpadding="4">
    <thead>
        <tr>
            <th>Sr. No.</th>
            <th>Roll No.</th>
            <th>Student</th>

            @foreach($subjects as $sub)
                <th>{{ $sub }}</th>
            @endforeach

            <th>Obtain Marks</th>
            <th>Total Marks</th>
            <th>% age</th>
            <th>Grade</th>
        </tr>
    </thead>

    <tbody>
        @foreach($students as $index => $st)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $st['RollNumber'] }}</td>
                <td>{{ $st['StudentName'] }}</td>

                {{-- Dynamic subject marks --}}
                @foreach($subjects as $sub)
                    <td>{{ $st['Subjects'][$sub] }}</td>
                @endforeach

                <td>{{ $st['TotalObtained'] }}</td>
                <td>{{ $st['TotalMax'] }}</td>
                <td>{{ $st['Percentage'] }}%</td>
                <td>{{ $st['Grade'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>


    <div class="footer-note">
      RptStdAdmissionInquiry
    </div>
  </div>
</body>

</html>