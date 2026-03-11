<?php

namespace App\Exports;

use App\DTOs\ReportFilterData;
use App\Services\Reports\SalesReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class SalesReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    private SalesReportService $service;

    public function __construct(
        private ReportFilterData $filters
    ) {
        $this->service = app(SalesReportService::class);
    }

    public function collection()
    {
        return $this->service->getExportData($this->filters);
    }

    public function headings(): array
    {
        return [
            'Order Number',
            'Order Date',
            'Customer',
            'Territory',
            'Principal',
            'Segment',
            'Item',
            'Quantity',
            'Gross Sales',
            'Discount',
            'Net Sales',
            'Total Amount',
            'Status',
            'Payment Status',
            'Sales Rep',
        ];
    }

    public function map($row): array
    {
        return [
            $row['Order Number'],
            $row['Order Date'],
            $row['Customer'],
            $row['Territory'],
            $row['Principal'],
            $row['Segment'],
            $row['Item'],
            $row['Quantity'],
            $row['Gross Sales'],
            $row['Discount'],
            $row['Net Sales'],
            $row['Total Amount'],
            $row['Status'],
            $row['Payment Status'],
            $row['Sales Rep'],
        ];
    }

    public function title(): string
    {
        return 'Sales Report';
    }
}
