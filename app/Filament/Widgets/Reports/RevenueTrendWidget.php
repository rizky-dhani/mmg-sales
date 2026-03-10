<?php

namespace App\Filament\Widgets\Reports;

use App\DTOs\ReportFilterData;
use App\Services\Reports\SalesReportService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class RevenueTrendWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Revenue Trend';

    public function getData(): array
    {
        $filters = $this->pageFilters ?? [];
        $filterData = ReportFilterData::fromArray($filters);
        $service = app(SalesReportService::class);
        $data = $service->generate($filterData);

        $periods = $data->revenueByPeriod->pluck('period')->toArray();
        $currentValues = $data->revenueByPeriod->pluck('revenue')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $currentValues,
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
