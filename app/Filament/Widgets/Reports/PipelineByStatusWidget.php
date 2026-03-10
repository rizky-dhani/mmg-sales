<?php

namespace App\Filament\Widgets\Reports;

use App\DTOs\ReportFilterData;
use App\Services\Reports\PipelineReportService;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class PipelineByStatusWidget extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $heading = 'Pipeline by Status';

    public function getData(): array
    {
        $filters = $this->pageFilters ?? [];
        $filterData = ReportFilterData::fromArray($filters);
        $service = app(PipelineReportService::class);
        $data = $service->generate($filterData);

        $labels = $data->pipelineByStatus->pluck('status')->toArray();
        $values = $data->pipelineByStatus->pluck('value')->toArray();
        $colors = [
            'New' => 'rgb(107, 114, 128)',
            'Contacted' => 'rgb(59, 130, 246)',
            'Qualified' => 'rgb(16, 185, 129)',
            'Proposal' => 'rgb(245, 158, 11)',
            'Negotiation' => 'rgb(236, 72, 153)',
            'Won' => 'rgb(16, 185, 129)',
            'Lost' => 'rgb(239, 68, 68)',
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Pipeline Value',
                    'data' => $values,
                    'backgroundColor' => array_map(fn ($status) => $colors[$status] ?? 'rgb(107, 114, 128)', $labels),
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
