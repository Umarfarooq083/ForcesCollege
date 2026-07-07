<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Admission Report</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      -webkit-print-color-adjust: exact;
    }
    .report-header {
      display: inline-flex;
      align-items: center;
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
    .table {
      border-collapse: collapse;
      width: 100%;
      text-align: center;
    }
    .table tbody td {
      border-bottom: 1px solid #a4a4a4 !important;
      padding: 6px 0px;
      font-size: 13px;
      vertical-align: top !important;
      white-space: normal !important;
    }
    table thead th {
      background-color: #20401f !important;
      color: #fff !important;
      padding: 11px !important;
    }
    .table thead th {
        border-color: unset;
        border: 0;
        border-spacing: 0;
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
          <!-- <img src="{{ public_path('assets/images/Logo.jpeg') }}" width="100%" alt="School Logo"> -->
          <img src="/assets/images/Logo.jpeg" width="100%" alt="School Logo">
        </div>
      </div>

      <div class="heading-container">
        <h1 class="maintxt">FORCES SCHOOL AND COLLEGE SYSTEM</h1>
        <h3 class="maintxt">Monthly Fee Collection Report</h3>
      </div>

      <div class="meta-container">
        <div><span class="meta-label">Campus:</span> <span class="meta-content">{{ $meta['campus'] ?? '' }}</span></div>
      </div>
    </div>
    <div class="table-responsive">
      <table class="table table-striped b-t b-l-white">
        <thead>
            <tr>
                <th>Class</th>
                <th style="text-align: left;">Section</th>
            </tr>
        </thead>
        <tbody>
            @php
              $grandTotal = 0;
              $grandPaid = 0;
              $grandBalance = 0;
              $grandWaived = 0;
            @endphp
            @foreach ($GenerateFeeChallan as $className => $sections)
                @php
                $classTotal = 0;
                $classPaid = 0;
                $classBalance = 0;
                $classWaived = 0;
                @endphp
                <tr>
                    <td style="width: 20px;padding: 6px 8px;"><strong>{{ $className }}</strong></td>
                    <td>
                        <table class="table">
                            @foreach ($sections as $sectionName => $records)
                                @php
                                $sectionTotal = 0;
                                $sectionPaid = 0;
                                $sectionBalance = 0;
                                $sectionWaived = 0;
                                @endphp
                                <tr>
                                    <td style="width: 20px;padding: 6px 8px;"><strong>{{ $sectionName }}</strong></td>
                                    <td>
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Sr.#</th>
                                                    <th class="text-center">Roll No</th>
                                                    <th class="text-center">Challan</th>
                                                    <th class="text-center">Name</th>
                                                    <th class="text-center">Amount</th>
                                                    <th class="text-center">Waived Amount</th>
                                                    <th class="text-center">Status</th>
                                                    <th class="text-center">Partial Payment Details</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($records as $index => $row)
                                                
                                                @php
                                                    $amount = ($row['transection_sum_balancefeeafterdiscount'] ?? 0);
                                                    $sectionTotal += $amount;
                                                    $sectionWaived += ($row['WaivedFineAmount'] ?? 0);
                                                    if ($row['Status'] === 'Paid') {
                                                        $sectionPaid += $amount;
                                                    } else {
                                                        $sectionBalance += $amount;
                                                    }
                                                    $classTotal += $amount;
                                                    $classWaived += ($row['WaivedFineAmount'] ?? 0);
                                                    if ($row['Status'] === 'Paid') {
                                                        $classPaid += $amount;
                                                    } else {
                                                        $classBalance += $amount;
                                                    }
                                                    $grandTotal += $amount;
                                                    $grandWaived += ($row['WaivedFineAmount'] ?? 0);
                                                    if ($row['Status'] === 'Paid') {
                                                        $grandPaid += $amount;
                                                    } else {
                                                        $grandBalance += $amount;
                                                    }
                                                @endphp

                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $row['StudentRel']['RollNumber'] ?? '-' }}</td>
                                                        <td>{{ $row['challan_no'] ?? '-' }}</td>
                                                        <td style="width: 150px;">{{ $row['StudentRel']['FirstName'] ?? '-' }} {{ $row['StudentRel']['LastName'] ?? '-' }} </td>
                                                      
                                                        <td>
                                                            @if (!empty($row['transection_sum_balancefeeafterdiscount']))
                                                              @if($row['Status'] === 'Unpaid' && $row['IsPartialPayment'] == 1 )
                                                                    <strong>Total:</strong> 
                                                              @endif
                                                                {{ $row['transection_sum_balancefeeafterdiscount'] }} <br>
                                                            @else
                                                                0
                                                            @endif
                                                          @php 
                                                            $partial_amount = 0;
                                                          @endphp
                                                          @if (count($row['partialPayments']) > 0)
                                                            @foreach ($row['partialPayments'] as $pIndex => $p)
                                                                @php $partial_amount  +=  $p['ReceivedAmount'] @endphp  
                                                            @endforeach
                                                          @endif

                                                          @if($row['Status'] === 'Unpaid' && $row['IsPartialPayment'] == 1 )
                                                              <strong>Received:</strong> {{ $partial_amount }}
                                                              <strong>Balance:</strong> {{ $row['transection_sum_balancefeeafterdiscount']  - $partial_amount }}
                                                          @endif
                                                        </td>
                                                        <td>{{ $row['WaivedFineAmount']}} </td>
                                                        <td>
                                                            @if ($row['Status'] === 'Paid')
                                                                Paid ( {{ \Carbon\Carbon::parse($row['SubmitDate'])->format('Y-m-d') }} )
                                                            @else
                                                                {{ $row['Status'] }} 
                                                            @endif
                                                        </td>
                                            
                                                        <td colspan="2">
                                                            @if (count($row['partialPayments']) > 0)
                                                                <table class="table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Sr.#</th>
                                                                            <th class="text-center">Collect Date</th>
                                                                            <th class="text-center">Submit Date</th>
                                                                            <th class="text-center">Payment Mode</th>
                                                                            <th class="text-center">Received Amount</th>
                                                                        </tr>
                                                                    </thead>

                                                                    <tbody>
                                                                        @foreach ($row['partialPayments'] as $pIndex => $p)
                                                                            <tr>
                                                                                <td>{{ $pIndex + 1 }}</td>
                                                                                <td>{{ $p['CollectDate'] }}</td>
                                                                                <td>{{ $p['SubmitDate'] }}</td>
                                                                                <td>{{ $p['PaymentMode'] }}</td>
                                                                                <td>{{ $p['ReceivedAmount'] }}</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                          
                                                <tr style="background:#f0f0f0; font-weight:bold;">
                                                    <td colspan="4" style="text-align:right;"><strong>Section Total:</strong></td>
                                                    <td><strong>{{ $sectionTotal }}</strong></td>
                                                    <td><strong>{{ $sectionWaived }}</strong></td>
                                                    <td><strong>Paid: {{ $sectionPaid }}</strong></td>
                                                    <td><strong>Balance: {{ $sectionBalance }}</strong></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            @endforeach
                            
                            <tr>
                                <td colspan="2">
                                    <table style="width:100%; background:#e8e8e8;">
                                        <tr>
                                            <td style="text-align:center; padding: 8px;"><strong>Class Total: {{ $classTotal }}</strong></td>
                                            <td style="padding: 8px;"><strong>Paid: {{ $classPaid }}</strong></td>
                                            <td style="padding: 8px;"><strong>Balance: {{ $classBalance }}</strong></td>
                                            <td style="padding: 8px;"><strong>Waived: {{ $classWaived }}</strong></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            @endforeach
          <!-- <tr style="background:#eee; font-weight:bold;">
              <td colspan="3"><strong>Grand Total:</strong> {{ $grandTotal }}</td>
              <td><strong>Grand Paid:</strong> {{ $grandPaid }}</td>
              <td><strong>Grand Balance:</strong> {{ $grandBalance }}</td>
              <td><strong>Grand Waived:</strong> {{ $grandWaived }}</td>
          </tr> -->
        </tbody>
      </table>
      <div style="background:#eee; text-align:center; display:flex; justify-content:center; gap:40px; padding:15px;">
          <span><strong>Grand Total:</strong> {{ $grandTotal }}</span>
          <span><strong>Grand Paid:</strong> {{ $grandPaid }}</span>
          <span><strong>Grand Balance:</strong> {{ $grandBalance }}</span>
          <span><strong>Grand Waived:</strong> {{ $grandWaived }}</span>
      </div>
    </div>
    <div class="footer-note">
      RptStdAdmissionInquiry
    </div>
  </div>
</body>

</html>