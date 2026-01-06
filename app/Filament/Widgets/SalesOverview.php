<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use NumberFormatter;

class SalesOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalOrders = Order::count();
        $totalSales = Order::sum('net_sales_total');
        $pendingOrders = Order::where('status', 'pending')->count();

        $formatter = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);
        $formattedSales = $formatter->formatCurrency($totalSales, 'IDR');

        return [
            Stat::make('Total Orders', $totalOrders)
                ->description('Overall orders placed')
                ->descriptionIcon('heroicon-m-shopping-cart'),
            Stat::make('Total Net Sales', $formattedSales)
                ->description('Sum of all net sales total')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
            Stat::make('Pending Orders', $pendingOrders)
                ->description('Orders awaiting confirmation')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}
