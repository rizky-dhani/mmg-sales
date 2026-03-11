<?php

namespace App\Exports;

use App\DTOs\ReportFilterData;
use App\Services\Reports\CustomerReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class CustomerReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    private CustomerReportService $service;

    public function __construct(
        private ReportFilterData $filters
    ) {
        $this->service = app(CustomerReportService::class);
    }

    public function collection()
    {
        return $this->service->getExportData($this->filters);
    }

    public function headings(): array
    {
        return [
            'Customer',
            'Customer Group',
            'Segment',
            'CD/NCD Type',
            'Order Number',
            'Order Date',
            'Revenue',
            'Net Sales',
            'Payment Status',
        ];
    }

    public function map($row): array
    {
        return [
            $row['Customer'],
            $row['Customer Group'],
            $row['Segment'],
            $row['CD/NCD Type'],
            $row['Order Number'],
            $row['Order Date'],
            $row['Revenue'],
            $row['Net Sales'],
            $row['Payment Status'],
        ];
    }

    public function title(): string
    {
        return 'Customer Report';
    }
}
