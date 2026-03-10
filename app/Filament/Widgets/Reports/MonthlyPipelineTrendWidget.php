<?php

namespace App\Filament\Widgets\Reports;

use App\DTOs\ReportFilterData;
use App\Services\Reports\PipelineReportService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class MonthlyPipelineTrendWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Monthly Pipeline Trend';

    public function getData(): array
    {
        $filters = $this->pageFilters ?? [];
        $filterData = ReportFilterData::fromArray($filters);
        $service = app(PipelineReportService::class);
        $data = $service->generate($filterData);

        $periods = $data->monthlyTrend->pluck('period')->toArray();
        $values = $data->monthlyTrend->pluck('value')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Pipeline Value',
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
