<?php

namespace App\Filament\Widgets\Reports;

use App\DTOs\ReportFilterData;
use App\Services\Reports\CustomerReportService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class CustomerMonthlyRevenueTrendWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Monthly Revenue Trend';

    public function getData(): array
    {
        $filters = $this->pageFilters ?? [];
        $filterData = ReportFilterData::fromArray($filters);
        $service = app(CustomerReportService::class);
        $data = $service->generate($filterData);

        $periods = $data->monthlyTrend->pluck('period')->toArray();
        $values = $data->monthlyTrend->pluck('revenue')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $values,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $periods,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
