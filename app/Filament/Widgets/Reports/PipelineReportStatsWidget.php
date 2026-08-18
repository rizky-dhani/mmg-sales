<?php

namespace App\Filament\Widgets\Reports;

use App\DTOs\ReportFilterData;
use App\Services\Reports\PipelineReportService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use NumberFormatter;

class PipelineReportStatsWidget extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected int|string|array $columnSpan = 'full';

    protected int|array|null $columns = 3;

    protected function getStats(): array
    {
        $filters = $this->pageFilters ?? [];

        $filterData = ReportFilterData::fromArray($filters);
        $service = app(PipelineReportService::class);
        $data = $service->generate($filterData);

        $formatter = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);
        $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);

        return [
            Stat::make('Total Pipeline Value', $formatter->formatCurrency($data->totalPipelineValue, 'IDR'))
                ->description('Total estimated value'),

            Stat::make('Win Rate', number_format($data->winRate, 1).'%')
                ->description('Leads converted to Sales'),
            Stat::make('Total Leads', number_format($data->totalProjects))
                ->description("Converted: {$data->wonProjects} | Not Won: {$data->lostProjects}"),

            Stat::make('Converted', $formatter->formatCurrency($data->wonValue, 'IDR'))
                ->description("{$data->wonProjects} partnerships established")
                ->color('success'),

            Stat::make('Not Won', $formatter->formatCurrency($data->lostValue, 'IDR'))
                ->description("{$data->lostProjects} opportunities not converted")
                ->color('danger'),

            Stat::make('Average Deal Size', $formatter->formatCurrency($data->averageDealSize, 'IDR'))
                ->description('Converted deals only'),
        ];
    }

}
