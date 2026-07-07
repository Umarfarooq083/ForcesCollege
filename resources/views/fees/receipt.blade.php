<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Fee Receipt</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --ink: #1a1a2e;
            --accent: #c8963e;
            --accent-light: #f5e9d3;
            --muted: #6b7280;
            --border: #d1c4a8;
            --bg: #faf8f4;
            --white: #ffffff;
        }

        @media print {
            @page { margin: 0; size: A4; }
            body { background: white !important; padding: 0 !important; }
            .no-print { display: none !important; }
            .receipt-wrapper { box-shadow: none !important; border-radius: 0 !important; max-width: 100% !important; width: 100% !important; }
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #ede9e0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 24px 12px 40px;
            color: var(--ink);
        }

        .toolbar {
            width: 100%;
            max-width: 780px;
            display: flex;
            justify-content: space-between;
            margin-bottom: 18px;
        }
        .btn {
            padding: 7px 20px;
            border: none;
            border-radius: 6px;
            font-family: 'DM Sans', sans-serif;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            letter-spacing: .3px;
            transition: opacity .15s;
        }
        .btn:hover { opacity: .85; }
        .btn-back { background: #e2ddd4; color: var(--ink); }
        .btn-print { background: var(--accent); color: #fff; }

        .receipt-wrapper {
            width: 100%;
            max-width: 780px;
            background: var(--white);
            border-radius: 4px;
            box-shadow: 0 4px 32px rgba(0,0,0,.12), 0 1px 4px rgba(0,0,0,.06);
            overflow: hidden;
            position: relative;
        }

        .receipt-wrapper::before {
            content: '';
            display: block;
            height: 6px;
            background: linear-gradient(90deg, #b8822c 0%, #e8b84b 40%, #c8963e 70%, #9e6b20 100%);
        }

        .receipt-body { padding: 28px 32px 32px; }

        .receipt-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 22px;
        }
        .school-brand { display: flex; align-items: center; gap: 14px; }
        .school-logo-placeholder {
            width: 52px; height: 52px;
            border-radius: 50%;
            border: 2px solid var(--border);
            background: var(--accent-light);
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; flex-shrink: 0;
        }
        .school-name {
            font-family: 'Playfair Display', serif;
            font-size: 18px;
            font-weight: 700;
            color: var(--ink);
            line-height: 1.2;
        }
        .school-meta { font-size: 11.5px; color: var(--muted); margin-top: 3px; line-height: 1.5; }
        .receipt-title-block { text-align: right; }
        .receipt-title {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--ink);
            letter-spacing: .5px;
        }
        .receipt-date { font-size: 11.5px; color: var(--muted); margin-top: 4px; }

        .divider {
            border: none;
            border-top: 1.5px solid var(--border);
            margin: 0 0 22px;
            position: relative;
        }
        .divider::after {
            content: '◆';
            position: absolute;
            left: 50%; top: 50%;
            transform: translate(-50%, -50%);
            background: var(--white);
            padding: 0 8px;
            color: var(--accent);
            font-size: 10px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            border: 1.5px solid var(--border);
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 22px;
        }
        .info-col { padding: 14px 18px; }
        .info-col:first-child { border-right: 1.5px solid var(--border); }
        .info-row {
            display: flex;
            gap: 8px;
            padding: 4px 0;
            font-size: 12.5px;
            border-bottom: 1px dashed #e5dfd3;
        }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: var(--muted); width: 110px; flex-shrink: 0; font-size: 11.5px; font-weight: 500; padding-top: 1px; }
        .info-value { color: var(--ink); font-weight: 600; }

        .amount-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
            border: 1.5px solid var(--border);
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 22px;
        }
        .amount-table thead tr { background: var(--ink); color: var(--white); }
        .amount-table thead th {
            padding: 10px 16px;
            text-transform: uppercase;
            letter-spacing: .8px;
            font-size: 11px;
            font-weight: 600;
        }
        .amount-table thead th:last-child { text-align: right; }
        .amount-table tbody tr { border-bottom: 1px solid #ede8de; }
        .amount-table tbody tr:last-child { border-bottom: none; }
        .amount-table tbody td { padding: 9px 16px; color: var(--ink); }
        .amount-table tbody td:last-child { text-align: right; font-weight: 500; }
        .amount-table tbody tr:nth-child(even) { background: #faf7f1; }
        .amount-table tfoot tr { background: var(--accent-light); border-top: 1.5px solid var(--border); }
        .amount-table tfoot td { padding: 11px 16px; font-weight: 700; font-size: 13.5px; color: var(--ink); }
        .amount-table tfoot td:last-child { text-align: right; color: var(--accent); font-size: 15px; }

        .note-block {
            background: #fffbf3;
            border-left: 3px solid var(--accent);
            padding: 10px 14px;
            border-radius: 0 4px 4px 0;
            font-size: 12px;
            color: var(--muted);
            margin-bottom: 22px;
        }
        .note-block strong { color: var(--ink); }

        .sig-row { display: flex; justify-content: space-between; margin-top: 8px; }
        .sig-block { width: 200px; }
        .sig-line { border-bottom: 1.5px solid var(--ink); height: 32px; margin-bottom: 5px; }
        .sig-label { font-size: 11px; color: var(--muted); font-weight: 500; }

        /* ===== PAID STAMP ===== */
        .paid-stamp {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 190px;
            height: 190px;
            border: 7px solid #1b5e20;
            border-radius: 50%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transform: translate(-50%, -50%) rotate(-22deg);
            opacity: 0.20;
            pointer-events: none;
            z-index: 10;
            background: transparent;
        }
        .paid-stamp::before {
            content: '';
            position: absolute;
            inset: 8px;
            border: 3px dashed #1b5e20;
            border-radius: 50%;
        }
        .paid-stamp .stamp-text {
            font-family: 'Playfair Display', serif;
            font-size: 62px;
            font-weight: 700;
            color: #1b5e20;
            letter-spacing: 5px;
            line-height: 1;
            text-transform: uppercase;
        }
        .paid-stamp .stamp-sub {
            font-size: 11px;
            font-weight: 700;
            color: #1b5e20;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            margin-top: 5px;
        }

        .receipt-footer {
            background: var(--ink);
            color: rgba(255,255,255,.5);
            text-align: center;
            font-size: 10.5px;
            padding: 9px 12px;
            letter-spacing: .5px;
        }
    </style>
</head>
<body>

<div class="toolbar no-print">
    <a href="{{ route('fee.collection.list') }}" class="btn btn-back">← Back</a>
    <button class="btn btn-print" onclick="window.print()">🖨 Print Receipt</button>
</div>

<div class="receipt-wrapper">

    <!-- PAID STAMP -->
    <div class="paid-stamp">
        <div class="stamp-text">PAID</div>
        <div class="stamp-sub">
            @if(!empty($receipt['submit_date']))
                {{ \Carbon\Carbon::parse($receipt['submit_date'])->format('d-m-Y') }}
            @else
                ---
            @endif
        </div>
    </div>

    <div class="receipt-body">

        <div class="receipt-header">
            <div class="school-brand">
                <div class="school-logo-placeholder">🏫</div>
                {{-- <img src="/assets/images/Logo.jpeg" alt="logo" class="school-logo"> --}}
                <div>
                    <div class="school-name">{{ $campusData->SchoolName ?? 'School Name' }}</div>
                    <div class="school-meta">
                        {{ $campusData->Address ?? '123 Education Street, City' }}<br>
                        @if(!empty($campusData->PhoneNo) || !empty($campusData->MobileNo))
                            📞 {{ $campusData->PhoneNo ?? $campusData->MobileNo }}
                        @endif
                    </div>
                </div>
            </div>
            <div class="receipt-title-block">
                <div class="receipt-title">Fee Receipt</div>
                <div class="receipt-date">{{ \Carbon\Carbon::parse($receipt['collected_at'] ?? now())->format('d M Y, h:i A') }}</div>
            </div>
        </div>

        <hr class="divider">

        @php
            $student = $challan?->StudentRel;
            $class   = $challan?->ClassRel;
            $section = $challan?->SectionRel;
        @endphp

        <div class="info-grid">
            <div class="info-col">
                <div class="info-row">
                    <span class="info-label">Receipt No</span>
                    <span class="info-value">{{ $receipt['receipt_no'] ?? '---' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Challan No</span>
                    <span class="info-value">{{ $receipt['challan_no'] ?? '---' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Student</span>
                    <span class="info-value">{{ $student->FirstName ?? '' }} {{ $student->LastName ?? '' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Roll #</span>
                    <span class="info-value">{{ $student->RollNumber ?? '---' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Father</span>
                    <span class="info-value">{{ $student->FatherName ?? '---' }}</span>
                </div>
            </div>
            <div class="info-col">
                <div class="info-row">
                    <span class="info-label">Class / Section</span>
                    <span class="info-value">{{ $class->ClassName ?? '---' }} / {{ $section->SectionName ?? '---' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Payment Type</span>
                    <span class="info-value">{{ ucfirst($receipt['payment_type'] ?? '---') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Payment Mode</span>
                    <span class="info-value">{{ $receipt['payment_mode'] ?? '---' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Submit Date</span>
                    <span class="info-value">
                        @if(!empty($receipt['submit_date']))
                            {{ \Carbon\Carbon::parse($receipt['submit_date'])->format('d M Y') }}
                        @else ---
                        @endif
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Collected By</span>
                    <span class="info-value">{{ $receipt['collected_by_name'] ?? '---' }}</span>
                </div>
            </div>
        </div>

        <table class="amount-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Amount (PKR)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Payable Amount</td>
                    <td>{{ number_format((float)($receipt['payable_amount'] ?? 0), 2) }}</td>
                </tr>
                <tr>
                    <td>Already Paid (Before)</td>
                    <td>{{ number_format((float)($receipt['already_paid_before'] ?? 0), 2) }}</td>
                </tr>
                <tr>
                    <td>Received (This Payment)</td>
                    <td>{{ number_format((float)($receipt['received_amount'] ?? 0), 2) }}</td>
                </tr>
                <tr>
                    <td>Waive Off (Total)</td>
                    <td>{{ number_format((float)($receipt['waived_amount_total'] ?? 0), 2) }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td>Balance After Payment</td>
                    <td>{{ number_format((float)($receipt['balance_after'] ?? 0), 2) }}</td>
                </tr>
            </tfoot>
        </table>

        @if(!empty($receipt['note']))
        <div class="note-block">
            <strong>Note:</strong> {{ $receipt['note'] }}
        </div>
        @endif

        <div class="sig-row">
            <div class="sig-block">
                <div class="sig-line"></div>
                <div class="sig-label">Accountant Signature</div>
            </div>
            <div class="sig-block" style="text-align:right;">
                <div class="sig-line"></div>
                <div class="sig-label">Parent / Student Signature</div>
            </div>
        </div>

    </div>

    <div class="receipt-footer">
        This is a computer-generated receipt &nbsp;·&nbsp; Please retain for your records
    </div>

</div>

<script>
    window.addEventListener('load', function () {
        try { window.print(); } catch (e) {}
    });
</script>

</body>
</html>