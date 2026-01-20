<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VisitsExport implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithTitle
{
    use Exportable;

    public function __construct(
        protected Builder $query,
        protected ?string $title = 'Visits'
    ) {}

    public function query()
    {
        return $this->query->with(['user', 'company']);
    }

    public function title(): string
    {
        return substr($this->title, 0, 31);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold
            1 => ['font' => ['bold' => true]],

            // Apply center alignment and wrap text to all cells
            'A1:Z1000' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Date & Time',
            'Duration',
            'Sales Rep',
            'Company',
            'Purpose',
            'Summary',
            'Stakeholder Feedback',
            'Worth Keeping?',
        ];
    }

    public function map($visit): array
    {
        $duration = '-';
        if ($visit->visit_started_at && $visit->visit_ended_at) {
            $duration = $visit->visit_started_at->diffForHumans($visit->visit_ended_at, true);
        }

        return [
            $visit->visit_started_at?->format('d M Y H:i') ?? '-',
            $duration,
            $visit->user?->name ?? '-',
            $visit->company?->facility_name ?? '-',
            $visit->purpose,
            $visit->summary_notes,
            $visit->stakeholder_feedback,
            $visit->is_worth_keeping ? 'Yes' : 'No',
        ];
    }
}
