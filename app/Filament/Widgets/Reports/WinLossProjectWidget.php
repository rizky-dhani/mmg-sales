<?php

namespace App\Filament\Widgets\Reports;

use App\DTOs\ReportFilterData;
use App\Services\Reports\PipelineReportService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use NumberFormatter;

class WinLossProjectWidget extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $filters = $this->pageFilters ?? [];
        $filterData = ReportFilterData::fromArray($filters);
        $service = app(PipelineReportService::class);
        $data = $service->generate($filterData);

        $formatter = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);
        $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);

        $totalClosed = $data->wonProjects + $data->lostProjects;

        return [
            Stat::make('Total Won', $formatter->formatCurrency($data->wonValue, 'IDR'))
                ->description("{$data->wonProjects} projects won")
                ->color('success'),

            Stat::make('Total Lost', $formatter->formatCurrency($data->lostValue, 'IDR'))
                ->description("{$data->lostProjects} projects lost")
                ->color('danger'),

            Stat::make('Win Rate', number_format($data->winRate, 1).'%')
                ->description($totalClosed > 0 ? "{$data->wonProjects} won / {$data->lostProjects} lost" : 'No closed projects')
                ->color(match (true) {
                    $data->winRate >= 70 => 'success',
                    $data->winRate >= 40 => 'warning',
                    default => 'danger',
                }),
        ];
    }
}
