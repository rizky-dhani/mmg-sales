<?php

namespace App\Filament\Widgets\Reports;

use App\DTOs\ReportFilterData;
use App\Services\Reports\CustomerReportService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class CustomerDistributionWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Customer Distribution';

    public function getData(): array
    {
        $filters = $this->pageFilters ?? [];
        $filterData = ReportFilterData::fromArray($filters);
        $service = app(CustomerReportService::class);
        $data = $service->generate($filterData);

        $labels = ['New', 'Returning'];
        $values = [$data->newCustomers, $data->returningCustomers];

        return [
            'datasets' => [
                [
                    'label' => 'Customers',
                    'data' => $values,
                    'backgroundColor' => [
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
