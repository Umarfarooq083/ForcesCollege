<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Sibling Report - All</title>
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
            margin-bottom: 20px;
        }

        .table thead th {
            border-bottom: 1px solid #1e3e1d !important;
            padding: 6px 8px;
            font-size: 13px;
            color: #1e3e1d;
            text-align: left;
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

        .guardian-header {
            background-color: #f8f9fa;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
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
                <p>Student Sibling Report - All</p>
            </div>

            <div class="meta-container">
                <div><span class="meta-label">Campus:</span> <span class="meta-content">{{ $meta['campus'] ?? '' }}</span></div>
            </div>
        </div>

        @forelse($siblings as $guardian)
        <div class="guardian-header">
            <strong>Guardian:</strong> {{ $guardian['guardian']['name'] }} | <strong>CNIC:</strong> {{ $guardian['guardian']['cnic'] }}
        </div>

        <table class="table table-bordered mb-0">
            <thead>
                <tr>
                    <th>Sr #</th>
                    <th>Student Name</th>
                    <th>Father Name</th>
                    <th>Mother Name</th>
                    <th>Class</th>
                    <th>Section</th>
                    <th>Gender</th>
                    <th>Roll No</th>
                </tr>
            </thead>
            <tbody>
                @foreach($guardian['students'] as $idx => $item)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>{{ $item['FirstName'] }} {{ $item['LastName'] }}</td>
                    <td>{{ $item['FatherName'] }}</td>
                    <td>{{ $item['MotherName'] }}</td>
                    <td>{{ $item['class']['ClassName'] ?? 'N/A' }}</td>
                    <td>{{ $item['section']['SectionName'] ?? 'N/A' }}</td>
                    <td>{{ $item['Gender'] }}</td>
                    <td>{{ $item['RollNumber'] ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @empty
        <p class="text-center">No students with siblings found</p>
        @endforelse

        <div class="footer-note">
            Student Sibling Report - All
        </div>
    </div>
</body>

</html>