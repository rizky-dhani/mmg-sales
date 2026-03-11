<?php

namespace App\Exports;

use App\DTOs\ReportFilterData;
use App\Services\Reports\ProductReportService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProductReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    private ProductReportService $service;

    public function __construct(
        private ReportFilterData $filters
    ) {
        $this->service = app(ProductReportService::class);
    }

    public function collection()
    {
        return $this->service->getExportData($this->filters);
    }

    public function headings(): array
    {
        return [
            'Item',
            'Principal',
            'Segment',
            'Order Number',
            'Order Date',
            'Quantity',
            'Unit Price',
            'Gross Sales',
            'Discount',
            'Net Sales',
            'Total Amount',
        ];
    }

    public function map($row): array
    {
        return [
            $row['Item'],
            $row['Principal'],
            $row['Segment'],
            $row['Order Number'],
            $row['Order Date'],
            $row['Quantity'],
            $row['Unit Price'],
            $row['Gross Sales'],
            $row['Discount'],
            $row['Net Sales'],
            $row['Total Amount'],
        ];
    }

    public function title(): string
    {
        return 'Product Report';
    }
}
