<?php

namespace App\Exports;

use App\DTOs\ReportFilterData;
use App\Services\Reports\PipelineReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class PipelineReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    private PipelineReportService $service;

    public function __construct(
        private ReportFilterData $filters
    ) {
        $this->service = app(PipelineReportService::class);
    }

    public function collection()
    {
        return $this->service->getExportData($this->filters);
    }

    public function headings(): array
    {
        return [
            'Name',
            'Customer',
            'Sales Rep',
            'Status',
            'Estimated Value',
            'Confidence Level',
            'Created Date',
            'Closed Date',
        ];
    }

    public function map($row): array
    {
        return [
            $row['Name'],
            $row['Customer'],
            $row['Sales Rep'],
            $row['Status'],
            $row['Estimated Value'],
            $row['Confidence Level'],
            $row['Created Date'],
            $row['Closed Date'],
        ];
    }

    public function title(): string
    {
        return 'Pipeline Report';
    }
}
