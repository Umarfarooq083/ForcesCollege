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
        <p class="maintxt">Term Wise Report</p>
      </div>

      <div class="meta-container">
        <div><span class="meta-label">Campus:</span> <span class="meta-content">{{ $campus ?? '' }}</span></div>
      </div>
    </div>

   <table border="1" width="100%" cellspacing="0" cellpadding="4">
    <thead>
        <tr>
            <th>Sr. No.</th>
            <th>Roll No.</th>
            <th>Student Name</th>
            <th>{{$exam['ExamName']}}</th>
            <th>Obtain Marks</th>
            <th>Total Marks</th>
            <th>Obtained Marks</th>
            <th>Percentage</th>
            <th>Grade</th>
        </tr>
    </thead>

    <tbody>
        @foreach($termwiseData as $termwise)
            <tr>
                <td>{{ $termwise['SrNo'] }}</td>
                <td>{{ $termwise['RollNumber'] }}</td>
                <td>{{ $termwise['StudentName'] }}</td>
                <td>{{ $termwise['TotalMarks'] }}</td>
                <td>{{ $termwise['ObtMarks'] }}</td>
                <td>{{ $termwise['TotalMarks'] }}</td>
                <td>{{ $termwise['ObtainedMarks'] }}</td>
                <td>{{ $termwise['Percentage'] }}</td>
                <td>{{ $termwise['Grade'] }}</td>
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