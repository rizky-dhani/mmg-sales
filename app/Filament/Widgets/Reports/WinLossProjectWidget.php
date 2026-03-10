<?php

namespace App\Filament\Widgets\Reports;

use App\DTOs\ReportFilterData;
use App\Services\Reports\PipelineReportService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;

class WinLossProjectWidget extends Widget
{
    use InteractsWithPageFilters;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.reports.win-loss-project';

    public function getRecentWins(): array
    {
        $filters = $this->pageFilters ?? [];
        $filterData = ReportFilterData::fromArray($filters);
        $service = app(PipelineReportService::class);
        $data = $service->generate($filterData);

        return $data->recentWins->toArray();
    }

    public function getRecentLosses(): array
    {
        $filters = $this->pageFilters ?? [];
        $filterData = ReportFilterData::fromArray($filters);
        $service = app(PipelineReportService::class);
        $data = $service->generate($filterData);

        return $data->recentLosses->toArray();
    }
}
