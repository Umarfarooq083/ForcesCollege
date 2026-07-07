<?php

namespace App\Exports;

use App\Models\DownloadContentLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DownloadedLogsExport implements FromCollection, WithHeadings
{
    public function __construct(
        public array $filters = [],
    ) {}

    public function collection()
    {
        $query = DownloadContentLog::query()
            ->with('user:id,name,tenant_id,email')
            ->with('uploadContent:id,ContentTitle,ContentType')
            ->orderByDesc('id');

        if (! empty($this->filters['campus_wise'])) {
            $query->where('tenant_id', $this->filters['campus_wise']);
        }

        if (! empty($this->filters['title'])) {
            $query->whereHas('uploadContent', function ($q) {
                $q->where('ContentTitle', 'like', '%'.$this->filters['title'].'%');
            });
        }

        $hasRange = ! empty($this->filters['date_from']) || ! empty($this->filters['date_to']);

        if ($hasRange) {
            if (! empty($this->filters['date_from'])) {
                $query->whereDate('created_at', '>=', $this->filters['date_from']);
            }
            if (! empty($this->filters['date_to'])) {
                $query->whereDate('created_at', '<=', $this->filters['date_to']);
            }
        } elseif (! empty($this->filters['date'])) {
            $query->whereDate('created_at', $this->filters['date']);
        }

        $selectedIds = $this->filters['selected_ids'] ?? null;

        if ($selectedIds !== null) {
            $query->whereIn('id', $selectedIds);
        }

        return $query->get()->map(function ($log) {
            return [
                'id' => $log->id,
                'Content Title' => $log->uploadContent->ContentTitle ?? 'N/A',
                'User Name' => $log->user->name ?? 'N/A',
                'Domain' => $log->domainName,
                'Tenant ID' => $log->tenant_id,
                'Date' => $log->created_at?->toDateTimeString() ?? '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Content Title',
            'User Name',
            'Domain',
            'Tenant ID',
            'Date',
        ];
    }
}
