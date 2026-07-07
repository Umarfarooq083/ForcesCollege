<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Fee Summary (Head Wise)</title>

  <link rel="stylesheet"
    href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />

  <style>
    body {
      font-family: Arial, sans-serif;
      font-size: 13px;
      -webkit-print-color-adjust: exact;
    }

    .report-header {
      display: flex;
      align-items: center;
      border-bottom: 1px solid #000;
      padding-bottom: 10px;
      margin-bottom: 15px;
    }

    .logo-container {
      width: 20%;
    }

    .heading-container {
      width: 60%;
      text-align: center;
    }

    .meta-container {
      width: 20%;
      text-align: right;
      font-size: 12px;
    }

    .maintxt {
      font-size: 20px;
      font-weight: bold;
      margin: 0;
    }

    table {
      border-collapse: collapse;
      width: 100%;
    }

    th,
    td {
      border: 1px solid #000;
      padding: 6px;
    }

    th {
      background: #f1f1f1;
      text-align: center;
      font-weight: bold;
    }

    td.text-right {
      text-align: right;
    }

    td.text-center {
      text-align: center;
    }

    .total-row {
      font-weight: bold;
      background: #f9f9f9;
    }

    .footer-note {
      margin-top: 15px;
      font-size: 11px;
    }
  </style>
</head>

<body>

  @php
  $arrearKey = 'Arrear Amount';
  $dynamicColumns = [];
  foreach ($result as $row) {
  foreach ($row as $key => $value) {

  if (in_array($key, ['Class Name', 'Section', $arrearKey])) {
  continue;
  }

  if (!in_array($key, $dynamicColumns)) {
  $dynamicColumns[] = $key;
  }
  }
  }

  foreach ($result as $row) {
  if (array_key_exists($arrearKey, $row)) {
  $dynamicColumns[] = $arrearKey;
  break;
  }
  }
  @endphp

  <div class="a4-page">
    <div class="report-header">
      <div class="logo-container">
        <img src="{{ public_path('assets/images/Logo.jpeg') }}" width="80">
      </div>

      <div class="heading-container">
        <div class="maintxt">FORCES SCHOOL AND COLLEGE SYSTEM</div>
        <div class="maintxt">Fee Summary (Head Wise)</div>
      </div>

      <div class="meta-container">
        Campus: {{ $meta['campus'] ?? '' }}
      </div>
    </div>
    <table>
      <thead>
        <tr>
          <th>Class</th>
          <th>Section</th>

          @foreach($dynamicColumns as $column)
          <th>{{ $column }}</th>
          @endforeach
        </tr>
      </thead>

      <tbody>
        @forelse($result as $row)
        <tr class="{{ ($row['Class Name'] ?? '') === 'Total' ? 'total-row' : '' }}">
          <td>{{ $row['Class Name'] ?? '' }}</td>
          <td class="text-center">{{ $row['Section'] ?? '' }}</td>
          @foreach($dynamicColumns as $column)
          <td class="text-right">
            {{ number_format($row[$column] ?? 0, 2) }}
          </td>
          @endforeach
        </tr>
        @empty
        <tr>
          <td colspan="{{ 2 + count($dynamicColumns) }}" class="text-center">
            No records found
          </td>
        </tr>
        @endforelse

      </tbody>
    </table>




  </div>





  @php
    // Process data for both tables
    $paidRow = ['Total Received Fee'];
    $unpaidRow = ['Total Receivable Fee'];
    $headers = [''];
    
    // Get all fee types first
    $feeTypes = [];
    foreach ($summary as $feeTypeKey => $paymentData) {
        $feeTypes[] = ucwords($feeTypeKey);
    }
    
    // Add headers
    foreach ($feeTypes as $feeType) {
        $headers[] = $feeType;
    }
    $headers[] = 'Arrear Amount';
    
    // Calculate paid row (only paid amounts)
    $paidArrear = 0;
    foreach ($feeTypes as $feeType) {
        $total = 0;
        
        foreach ($summary as $feeTypeKey => $paymentData) {
            if (ucwords($feeTypeKey) === $feeType) {
                // Add paid fee amount
                if (isset($paymentData['paid'])) {
                    $total += $paymentData['paid']['total_fee_amount'] ?? 0;
                    // Add paid arrear amount
                    $paidArrear += $paymentData['paid']['total_arrear_amount'] ?? 0;
                }
                break;
            }
        }
        
        $paidRow[] = number_format($total, 2);
    }
    $paidRow[] = number_format($paidArrear, 2);
    
    // Calculate unpaid row (only unpaid amounts)
    $unpaidArrear = 0;
    foreach ($feeTypes as $feeType) {
        $total = 0;
        
        foreach ($summary as $feeTypeKey => $paymentData) {
            if (ucwords($feeTypeKey) === $feeType) {
                // Add unpaid fee amount
                if (isset($paymentData['unpaid'])) {
                    $total += $paymentData['unpaid']['total_fee_amount'] ?? 0;
                    // Add unpaid arrear amount
                    $unpaidArrear += $paymentData['unpaid']['total_arrear_amount'] ?? 0;
                }
                break;
            }
        }
        
        $unpaidRow[] = number_format($total, 2);
    }
    $unpaidRow[] = number_format($unpaidArrear, 2);
@endphp

<!-- Unpaid Fees Table -->
<div>
    <h3 style="margin-bottom: 50px; color: #c62828;"></h3>
    <table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%;">
        <thead>
            <tr style="background-color: #ffebee;">
                @foreach($headers as $header)
                    <th style="text-align: {{ $loop->first ? 'left' : 'right' }}; padding: 10px;">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                @foreach($unpaidRow as $cell)
                    <td style="text-align: {{ $loop->first ? 'left' : 'right' }}; padding: 10px; 
                        {{ $loop->first ? 'font-weight: bold;' : '' }}">
                        {{ $cell }}
                    </td>
                @endforeach
            </tr>
        </tbody>
    </table>
</div>

<!-- Paid Fees Table -->
<div style="margin-bottom: 10px;">
    <h3 style="color: #2e7d32;"></h3>
    <table border="1" cellpadding="8" cellspacing="0" style="border-collapse: collapse; width: 100%;">
        <thead>
            <tr style="background-color: #e8f5e9;">
                @foreach($headers as $header)
                    <th style="text-align: {{ $loop->first ? 'left' : 'right' }}; padding: 10px;">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                @foreach($paidRow as $cell)
                    <td style="text-align: {{ $loop->first ? 'left' : 'right' }}; padding: 10px; 
                        {{ $loop->first ? 'font-weight: bold;' : '' }}">
                        {{ $cell }}
                    </td>
                @endforeach
            </tr>
        </tbody>
    </table>
</div>

</body>

</html>