<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daily Fee Collection</title>

  <style>
    body {
      font-family: Arial, sans-serif;
      -webkit-print-color-adjust: exact;
    }

    table {
      border-collapse: collapse;
      width: 100%;
      text-align: center;
    }

    thead th {
      background: #20401f;
      color: #fff;
      padding: 10px;
    }

    tbody td {
      border-bottom: 1px solid #aaa;
      padding: 6px;
      font-size: 13px;
      vertical-align: top;
    }
    
    .summary-row {
      background: #f0f0f0 !important;
      font-weight: bold;
    }
  </style>
</head>

<body>

  @php
  $grandTotal = 0;
  $grandPaid = 0;
  $grandBalance = 0;
  $grandWaived = 0;
  @endphp

  <table>
    <thead>
      <tr>
        <th>Class</th>
        <th style="text-align:left;">Section</th>
      </tr>
    </thead>

    <tbody>
      @foreach ($GenerateFeeChallan as $className => $sections)
      @php
      $classTotal = 0;
      $classPaid = 0;
      $classBalance = 0;
      $classWaived = 0;
      $classPartial = 0;
      @endphp
      <tr>
        <td width="120" style="vertical-align: top;"><strong>{{ $className }}</strong></td>
        <td>

          <table>
            @foreach($sections as $sectionName => $students)
            @php
            $sectionTotal = 0;
            $sectionPaid = 0;
            $sectionBalance = 0;
            $sectionWaived = 0;
            $sectionPartial = 0;
            @endphp
            <tr>
              <td width="120" style="vertical-align: top;"><strong>{{ $sectionName }}</strong></td>
              <td>

                <table>
                  <thead>
                    <tr>
                      <th>Sr#</th>
                      <th>Roll</th>
                      <th>Challan</th>
                      <th>Name</th>
                      <th>Amount</th>
                      <th>Waived</th>
                      <th>Status</th>
                      <th>Partial</th>
                    </tr>
                  </thead>

                  <tbody>
                    @foreach ($students as $index => $row)
                    @php
                    // Calculate amount for this student
                    $amount = ($row['transection_sum_balancefeeafterdiscount'] ?? 0)
                    + ($row['total_arrear_fine'] ?? 0)
                    + ($row['total_arrears_amount'] ?? 0)
                    - ($row['arrear_partial_sum'] ?? 0);
                    
                    $sectionTotal += $amount;
                    $sectionWaived += $row['WaivedFineAmount'] ?? 0;
                    
                    // Calculate student partial payments
                    $studentPartial = 0;
                    $hasPartialPayments = isset($row['partialPayments']) && count($row['partialPayments']) > 0;
                    
                    if ($hasPartialPayments) {
                      foreach ($row['partialPayments'] as $p) {
                        $studentPartial += $p['ReceivedAmount'] ?? 0;
                      }
                    }
                    
                    // Update totals based on status
                    if ($row['Status'] === 'Paid') {
                      $sectionPaid += $amount;
                    } else {
                      $sectionBalance += $amount;
                      
                      // If unpaid but has partial payments
                      if ($hasPartialPayments) {
                        $sectionPaid += $studentPartial;
                        $sectionBalance -= $studentPartial;
                        $sectionPartial += $studentPartial;
                      }
                    }
                    @endphp
                    <tr>
                      <td>{{ $index + 1 }}</td>
                      <td>{{ $row['StudentRel']['RollNumber'] ?? '-' }}</td>
                      <td>{{ $row['challan_no'] ?? '-' }}</td>
                      <td style="text-align:left;">
                        {{ $row['StudentRel']['FirstName'] ?? '' }}
                        {{ $row['StudentRel']['LastName'] ?? '' }}
                      </td>
                      <td>{{ $amount }}</td>
                      <td>{{ $row['WaivedFineAmount'] ?? 0 }}</td>
                      <td>{{ $row['Status'] }}</td>
                      <td>
                        @if ($hasPartialPayments)
                        <table style="width:100%;">
                          @foreach ($row['partialPayments'] as $p)
                          <tr>
                            <td style="font-size:11px;">{{ $p['CollectDate'] ?? '' }}</td>
                            <td style="font-size:11px;">{{ $p['ReceivedAmount'] ?? 0 }}</td>
                          </tr>
                          @endforeach
                        </table>
                        @else
                        -
                        @endif
                      </td>
                    </tr>
                    @endforeach
                  
                    {{-- Section Totals Row --}}
                    @php
                    $sectionPaidAfterWaived = $sectionPaid - $sectionWaived;
                    @endphp
                    <tr class="summary-row">
                      <td colspan="4" style="text-align:right;"><strong>Total:</strong></td>
                      <td><strong>{{ $sectionTotal }}</strong></td>
                      <td><strong>{{ $sectionWaived }}</strong></td>
                      <td><strong>Paid: {{ $sectionPaidAfterWaived }}</strong></td>
                      <td><strong>Balance: {{ $sectionBalance }}</strong></td>
                    </tr>
                   
                    @php
                    // Accumulate class totals from sections
                    $classTotal += $sectionTotal;
                    $classPaid += $sectionPaid;
                    $classBalance += $sectionBalance;
                    $classWaived += $sectionWaived;
                    $classPartial += $sectionPartial;
                    @endphp

                  </tbody>
                </table>

              </td>
            </tr>
            @endforeach
            
            {{-- Class Totals Row --}}
            @php
              $classPaidAfterWaived = $classPaid - $classWaived;
            @endphp
            <tr>
              <td colspan="2">
                <table style="width:100%; background:#e8e8e8;">
                  <tr>
                    <td style="text-align:center; padding: 8px;"><strong>Class Total: {{ $classTotal }}</strong></td>
                    <td style="padding: 8px;"><strong>Paid: {{ $classPaidAfterWaived }}</strong></td>
                    <td style="padding: 8px;"><strong>Balance: {{ $classBalance }}</strong></td>
                    <td style="padding: 8px;"><strong>Waived: {{ $classWaived }}</strong></td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>

        </td>
      </tr>
      
      @php
        // Accumulate grand totals from classes
        $grandTotal += $classTotal;
        $grandPaid += $classPaid;
        $grandBalance += $classBalance;
        $grandWaived += $classWaived;
      @endphp
      
      @endforeach
    </tbody>
  </table>

  @php
  $grandPaidAfterWaived = $grandPaid - $grandWaived;
  @endphp
  <div style="background:#eee; padding:15px; display:flex; justify-content:center; gap:40px;">
    <span><strong>Grand Total:</strong> {{ $grandTotal }}</span>
    <span><strong>Grand Paid:</strong> {{ $grandPaidAfterWaived }}</span>
    <span><strong>Grand Balance:</strong> {{ $grandBalance }}</span>
    <span><strong>Grand Waived:</strong> {{ $grandWaived }}</span>
  </div>

</body>

</html> 
