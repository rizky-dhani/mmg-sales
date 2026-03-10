<?php

namespace App\Filament\Widgets\Reports;

use App\DTOs\ReportFilterData;
use App\Services\Reports\SalesReportService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class SalesRevenueBySegmentWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Revenue by Segment';

    public function getData(): array
    {
        $filters = $this->pageFilters ?? [];
        $filterData = ReportFilterData::fromArray($filters);
        $service = app(SalesReportService::class);
        $data = $service->generate($filterData);

        $labels = $data->revenueBySegment->pluck('name')->toArray();
        $values = $data->revenueBySegment->pluck('revenue')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Revenue',
                    'data' => $values,
                    'backgroundColor' => [
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(245, 158, 11)',
                        'rgb(239, 68, 68)',
                        'rgb(139, 92, 246)',
                        'rgb(236, 72, 153)',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
