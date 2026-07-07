<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SchoolFeeLedgerExport implements FromCollection, WithHeadings
{
    private array $months;
    private array $data;

    public function __construct(array $months, array $data)
    {
        $this->months = $months;
        $this->data = $data;
    }

    public function collection()
    {
        $rows = collect($this->data)->map(function ($row) {
            $exportRow = [
                $row['sr_no'] ?? '',
                $row['roll_number'] ?? '',
                $row['student_name'] ?? '',
                $row['class'] ?? '',
                $row['section'] ?? '',
            ];

            foreach ($this->months as $month) {
                $monthData = $row[$month] ?? null;
                if (
                    !$monthData ||
                    (
                        isset($monthData['paid'], $monthData['pending'], $monthData['receivable'], $monthData['waived']) &&
                        $monthData['paid'] == 0 &&
                        $monthData['pending'] == 0 &&
                        $monthData['receivable'] == 0 &&
                        $monthData['waived'] == 0
                    )
                ) 
                {
                    $exportRow[] = null; // blank cell
                } else {
                    $exportRow[] = $monthData['label'] ?? '';
                }
            }

            $exportRow[] = $row['total_received'] ?? 0;
            $exportRow[] = $row['total_waived_fine_amount'] ?? 0;
            $exportRow[] = $row['total_pending'] ?? 0;
            $exportRow[] = $row['total_receivable'] ?? 0;

            return $exportRow;
        });

        return new Collection($rows);
    }

    public function headings(): array
    {
        $headers = [
            'Sr#',
            'Roll#',
            'Student Name',
            'Class',
            'Section',
        ];

        foreach ($this->months as $month) {
            $headers[] = Carbon::createFromFormat('Y-m', $month)->format('M Y');
        }

        $headers[] = 'Total Received';
        $headers[] = 'WaivedFineAmount';
        $headers[] = 'Total Pending';
        $headers[] = 'Total Receivable';

        return $headers;
    }
}
