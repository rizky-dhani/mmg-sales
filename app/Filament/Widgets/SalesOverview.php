<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use NumberFormatter;

class SalesOverview extends BaseWidget
{
    protected ?string $heading = 'Sales Performance';

    public static function canView(): bool
    {
        return true;
    }

    protected function getStats(): array
    {
        $totalOrders = Order::count();
        $totalSales = Order::sum('net_sales_total');
        $aov = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        $formatter = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);
        $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);

        return [
            Stat::make('Total Net Sales', $formatter->formatCurrency($totalSales, 'IDR'))
                ->description('Sum of all net sales')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->url(route('filament.admin.resources.orders.index')),

            Stat::make('Avg Order Value (AOV)', $formatter->formatCurrency($aov, 'IDR'))
                ->description('Average revenue per order')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('info'),
        ];
    }
}
