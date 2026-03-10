<?php

namespace App\Filament\Widgets\Reports;

use App\DTOs\ReportFilterData;
use App\Services\Reports\SalesReportService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use NumberFormatter;

class SalesReportStatsWidget extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $filters = $this->pageFilters ?? [];

        $filterData = ReportFilterData::fromArray($filters);
        $service = app(SalesReportService::class);
        $data = $service->generate($filterData);

        $formatter = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);
        $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);

        return [
            Stat::make('Total Revenue', $formatter->formatCurrency($data->totalRevenue, 'IDR'))
                ->description($this->getGrowthDescription($data->getRevenueGrowthPercentage()))
                ->descriptionIcon($this->getGrowthIcon($data->getRevenueGrowthPercentage()))
                ->color($this->getGrowthColor($data->getRevenueGrowthPercentage())),

            Stat::make('Net Sales', $formatter->formatCurrency($data->totalNetSales, 'IDR'))
                ->description('Total net sales after discounts'),

            Stat::make('Total Orders', number_format($data->totalOrders))
                ->description($this->getGrowthDescription($data->getOrderGrowthPercentage()))
                ->descriptionIcon($this->getGrowthIcon($data->getOrderGrowthPercentage()))
                ->color($this->getGrowthColor($data->getOrderGrowthPercentage())),

            Stat::make('Average Order Value', $formatter->formatCurrency($data->averageOrderValue, 'IDR'))
                ->description($this->getGrowthDescription($data->getAovGrowthPercentage()))
                ->descriptionIcon($this->getGrowthIcon($data->getAovGrowthPercentage()))
                ->color($this->getGrowthColor($data->getAovGrowthPercentage())),
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
}
