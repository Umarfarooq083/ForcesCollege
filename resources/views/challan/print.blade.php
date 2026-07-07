<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student Fee Challan</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style type="text/css" media="print">
        @page {
            size: landscape;
            margin: 10mm;
        }
        #btnPrintInvoice {
            display: none;
        }
    </style>
    <style>
        body { font-size: 11px; }
        .challan-box {
            border-right: 1px dotted black;
            padding: 10px;
        }
        .challan-box:last-child {
            border-right: none;
        }
        .fontSize { font-size: 11px; }
        .PTable { padding: 2px !important; }
        .copy-title {
            text-align: center;
            font-weight: bold;
            margin-top: 5px;
        }
        .note-area {
            white-space: pre-wrap;
            font-size: 10px;
            border-top: 1px solid #000;
            margin-top: 10px;
            padding-top: 5px;
        }
    
        .student-block {
            page-break-after: always; /* each student prints on a new page */
        }
    </style>
</head>
<!-- <body onload="window.print()"> -->
<body>

<div class="container">
    
    @foreach($challans as $challan)
    
        <div class="student-block">
            <hr>
            <div class="mt-5 row" >
                @php
                    $copies = ['School Copy', 'Bank Copy', 'Parent Copy'];
                @endphp
                 
                @foreach($copies as $copy)
                @php
                    $monthString = [];
                    $arrearsAmount = 0;
                    $arrearsTotalFineAmount = 0;
                    $wived_off = $waivedOffByChallan[$challan['id']] ?? 0;
                
                @endphp 
                @if(count($previousChallanData) > 0 )
                    @if(!empty($previousChallanData[$challan['id']]))
                        @foreach($previousChallanData[$challan['id']] as $previousChallan)
                            @php
                                $arrearsAmount += $previousChallan['total_amount'] ?? 0;
                                $arrearsTotalFineAmount += $previousChallan['total_fine'] ?? 0;
                            @endphp
                            @if($previousChallan['has_arraear'] == 'yes')
                                @php array_push($monthString,'Partial-'.$previousChallan['ChallanMonth']); @endphp
                            @else
                                @php array_push($monthString,$previousChallan['ChallanMonth']); @endphp
                            @endif
                        @endforeach
                    @endif
                @endif
              
                 @php
                    $monthString = implode(',', $monthString);
                    
                @endphp

                <div class="col-4 challan-box">
                    <div class="mb-2 text-center">
                        <img src="/assets/images/Logo.jpeg" alt="logo" width="40" style="float:left;">
                        <span style="font-size:16px; font-weight:bold;">Fee Voucher</span>
                        <h6 class="copy-title">{{ $copy }}</h6>
                    </div>

                    <table class="table mb-2 table-bordered table-sm fontSize">
                        <tr>
                            <td colspan="2" class="text-center font-weight-bold text-decoration-underline">
                                Deposit to Bank
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Bank:</strong></td>
                            <td>{{$campusData->bankName}}</td>
                        </tr>
                        <tr>
                            <td><strong>Account #:</strong></td>
                            <td>{{ $campusData->AccountNo ?? '' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Account Title:</strong></td>
                            <td>{{ $campusData->AccountTitle ?? '' }}</td>
                        </tr>
                    </table>

                    <table class="table mt-2 table-sm table-borderless fontSize">
                        <tr>
                            <td><strong>Roll#:</strong> {{ $challan['student_rel']['RollNumber'] ?? '---' }}</td>
                            <td><strong>Name:</strong> {{ $challan['student_rel']['FirstName'] ?? '---' }} {{ $challan['student_rel']['LastName'] ?? '---' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Class:</strong> {{ $challan['class_rel']['ClassName'] ?? '---' }}</td>
                            <td><strong>Father:</strong> {{ $challan['student_rel']['FatherName'] ?? '---' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Section:</strong> {{ $challan['section_rel']['SectionName'] ?? '---' }}</td>
                            <td><strong>Challan No:</strong> {{ $challan['challan_no'] ?? '---' }}</td>
                        </tr>
                        <tr>
                            <td>
                                <strong>Month:</strong>
                                @if(!empty($feeCycles) && !empty($feeCycles[$challan['id']]))
                                    {{ $feeCycles[$challan['id']] }}
                                @else
                                    {{ !empty($challan['ChallanMonth']) ? \Carbon\Carbon::parse($challan['ChallanMonth'])->format('M-Y') : '---' }}
                                @endif
                            </td>
                            <td><strong>Due Date:</strong> {{ $challan['DueDate'] ?? '---' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Expiry Date:</strong> {{ !empty($challan['ExpiryDate']) ? \Carbon\Carbon::parse($challan['ExpiryDate'])->format('d-M-Y') : '---' }}</td>
                            <td><strong>Issue Date:</strong> {{ !empty($challan['created_at']) ? \Carbon\Carbon::parse($challan['created_at'])->format('d-M-Y') : '---' }}</td>
                            {{-- <td><strong>Issue Date:</strong> {{ now()->format('d-M-Y') }}</td> --}}
                        </tr>
                    </table>

                    <table class="table table-bordered table-sm fontSize">
                        <thead class="thead-light">
                            <tr>
                                <th>Sr#</th>
                                <th>Fee Name</th>
                                <th>Amount</th>
                                <th>Disc%</th>
                                <th>Waive off</th>
                                <th>Net</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $grandTotal = 0; @endphp
                            @foreach($challan['challan_transactions'] as $index => $transaction)
                                <tr>
                                    <td>{{ $index+1 }}</td>
                                    <td>{{ $transaction['TransactionType'] ?? '' }}</td>
                                    <td>{{ number_format($transaction['FeeAmount'] ?? 0, 2) }}</td>
                                    <td>{{ $transaction['DiscountAmount'] ?? 0 }}</td>
                                    <td>0</td>
                                    <td>{{ number_format($transaction['BalanceFeeAfterDiscount'] ?? 0, 2) }}</td>
                                </tr>
                                    @if($index == 0 && count($previousChallanData) > 0)
                                        <tr>
                                            <td>{{$index+2 }}</td>
                                            <td>
                                                @if($monthString)
                                                    Arrears({{$monthString}})
                                                @endif
                                            </td>
                                            <td>{{$arrearsAmount}}</td>
                                            <td>0</td>
                                            <td>{{ $wived_off }}</td>
                                            <td>{{ ($arrearsAmount - $wived_off) }}</td>
                                        </tr>
                                    @endif

                                    @if($index == 0 && count($previousChallanData) > 0)
                                        <tr>
                                            <td>{{$index+3 }}</td>
                                            <td>Fine</td>
                                            <td></td>
                                            <td>0</td>
                                            <td>0</td>
                                            <td>{{$arrearsTotalFineAmount}}</td>
                                        </tr>
                                    @endif
                                @php $grandTotal += $transaction['BalanceFeeAfterDiscount'] ?? 0;
                                @endphp
                            @endforeach
                            <tr>
                                <td colspan="5" class="text-right font-weight-bold">Total</td>
                                <td class="font-weight-bold">{{ number_format($grandTotal+ $arrearsAmount + $arrearsTotalFineAmount -  $wived_off ,2) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="note-area">
                        <strong>Note:</strong> In case of late payment, fine will be charged.  
                        Fee once paid is not refundable or adjustable.  
                        <strong>Date:</strong> ___________________  
                        <strong>Accountant:</strong> ___________________
                    </div>
                    <hr>
                </div>
                
                @endforeach
            </div>
        </div>
    @endforeach
</div>

</body>
</html>
