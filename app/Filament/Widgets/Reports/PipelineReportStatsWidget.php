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

    protected ?int $columns = 6;

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
                ->description($this->getGrowthDescription($data->getPipelineGrowthPercentage()))
                ->descriptionIcon($this->getGrowthIcon($data->getPipelineGrowthPercentage()))
                ->color($this->getGrowthColor($data->getPipelineGrowthPercentage())),

            Stat::make('Win Rate', number_format($data->winRate, 1).'%')
                ->description($this->getWinRateChangeDescription($data->getWinRateChange()))
                ->descriptionIcon($this->getWinRateChangeIcon($data->getWinRateChange()))
                ->color($this->getWinRateChangeColor($data->getWinRateChange())),
            Stat::make('Total Projects', number_format($data->totalProjects))
                ->description("Won: {$data->wonProjects} | Lost: {$data->lostProjects}"),

            Stat::make('Total Won', $formatter->formatCurrency($data->wonValue, 'IDR'))
                ->description("{$data->wonProjects} projects won")
                ->color('success'),

            Stat::make('Total Lost', $formatter->formatCurrency($data->lostValue, 'IDR'))
                ->description("{$data->lostProjects} projects lost")
                ->color('danger'),

            Stat::make('Average Deal Size', $formatter->formatCurrency($data->averageDealSize, 'IDR'))
                ->description('Won deals only'),
        ];
    }

    private function getGrowthDescription(?float $percentage): string
    {
        if ($percentage === null) {
            return 'No comparison data';
        }

        $sign = $percentage >= 0 ? '+' : '';

        return "{$sign}".number_format($percentage, 1).'% vs comparison period';
    }

    private function getGrowthIcon(?float $percentage): string
    {
        if ($percentage === null) {
            return 'heroicon-m-minus';
        }

        return $percentage >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
    }

    private function getGrowthColor(?float $percentage): string
    {
        if ($percentage === null) {
            return 'gray';
        }

        return $percentage >= 0 ? 'success' : 'danger';
    }

    private function getWinRateChangeDescription(?float $change): string
    {
        if ($change === null) {
            return 'No comparison data';
        }

        $sign = $change >= 0 ? '+' : '';

        return "{$sign}".number_format($change, 1).'% vs comparison period';
    }

    private function getWinRateChangeIcon(?float $change): string
    {
        if ($change === null) {
            return 'heroicon-m-minus';
        }

        return $change >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
    }

    private function getWinRateChangeColor(?float $change): string
    {
        if ($change === null) {
            return 'gray';
        }

        return $change >= 0 ? 'success' : 'danger';
    }
}
