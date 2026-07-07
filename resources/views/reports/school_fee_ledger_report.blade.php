<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Fee Ledger Report</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { font-family: Arial, sans-serif; }
        .table { border-collapse: collapse; width: 100%; font-size: 12px; }
        .table th, .table td { border: 1px solid #000; padding: 4px; text-align: center; }
        .table th { background-color: #f8f9fa; font-weight: bold; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h3 { margin: 0; }
        .meta { text-align: right; font-size: 12px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h3>FORCES SCHOOL AND COLLEGE SYSTEM</h3>
        <h4>School Fee Ledger Report</h4>
    </div>
    <div class="meta">
        <p><strong>Campus:</strong> {{ $meta['campus'] ?? '' }}</p>
        <p><strong>From Date:</strong> {{ $meta['from_date'] ?? '' }}</p>
        <p><strong>To Date:</strong> {{ $meta['to_date'] ?? '' }}</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Sr#</th>
                <th>Roll#</th>
                <th>Student Name</th>
                <th>Class</th>
                <th>Section</th>
                @foreach($months as $month)
                    <th>{{ \Carbon\Carbon::createFromFormat('Y-m', $month)->format('M Y') }}</th>
                @endforeach
                <th>Total Received</th>
                <th>Total Pending</th>
                <th>Total Receivable</th>
                <th>WaivedFineAmount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $row)
            <tr>
                <td>{{ $row['sr_no'] }}</td>
                <td>{{ $row['roll_number'] }}</td>
                <td>{{ $row['student_name'] }}</td>
                <td>{{ $row['class'] }}</td>
                <td>{{ $row['section'] }}</td>
                @foreach($months as $month)
                    {{-- <td>{{ $row[$month]['label'] ?? '' }}</td> --}}
                    <td>
                        @if(!empty($row[$month]['label']))
                            {{ $row[$month]['label'] }}
                        @endif
                    </td>
                @endforeach
                <td>{{ $row['total_received'] > 0 ? number_format($row['total_received'], 2) : '0.00' }}</td>
                <td>{{ $row['total_pending'] > 0 ? number_format($row['total_pending'], 2) : '0.00' }}</td>
                <td>{{ $row['total_receivable'] > 0 ? number_format($row['total_receivable'], 2) : '0.00' }}</td>
                <td>{{ $row['total_waived_fine_amount'] > 0 ? number_format($row['total_waived_fine_amount'], 2) : '0.00' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ 8 + count($months) }}" class="text-center">No records found</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
