<?php

namespace App\Filament\Widgets\Reports;

use App\DTOs\ReportFilterData;
use App\Services\Reports\ProductReportService;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use NumberFormatter;

class ProductReportStatsWidget extends StatsOverviewWidget
{
    use InteractsWithPageFilters;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $filters = $this->pageFilters ?? [];

        $filterData = ReportFilterData::fromArray($filters);
        $service = app(ProductReportService::class);
        $data = $service->generate($filterData);

        $formatter = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);
        $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);

        return [
            Stat::make('Total Revenue', $formatter->formatCurrency($data->totalRevenue, 'IDR'))
                ->description('Product sales revenue'),

            Stat::make('Total Quantity', number_format($data->totalQuantity))
                ->description('Units sold'),

            Stat::make('Total Discount', $formatter->formatCurrency($data->totalDiscount, 'IDR'))
                ->description('Total discount given'),
        ];
    }
}
