<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use NumberFormatter;

class SalesOrdersWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();

        if (! $user) {
            return [];
        }

        $formatter = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);

        $openOrders = Order::query()
            ->whereDoesntHave('deliveryStatuses')
            ->whereDoesntHave('paymentStatuses')
            ->whereYear('order_date', now()->year);

        $openCount = $openOrders->count();
        $openAmount = $openOrders->sum('total_amount');

        $shippedOrders = Order::query()
            ->where('created_by', $user->id)
            ->whereHas('deliveryStatuses', fn ($q) => $q->whereNotNull('shipped_date'))
            ->whereYear('order_date', now()->year);

        $shippedCount = $shippedOrders->count();
        $shippedAmount = $shippedOrders->sum('total_amount');

        $totalOrders = Order::query()->count();
        $totalAmount = Order::query()->sum('total_amount');

        return [
            Stat::make('Open Orders (YTD)', $openCount.' orders')
                ->description($formatter->formatCurrency($openAmount, 'IDR').' in open orders')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Shipped Orders (YTD)', $shippedCount.' orders')
                ->description($formatter->formatCurrency($shippedAmount, 'IDR').' in shipped orders')
                ->descriptionIcon('heroicon-m-truck')
                ->color('primary'),
            Stat::make('Total Orders', $totalOrders.' orders')
                ->description($formatter->formatCurrency($totalAmount, 'IDR').' all-time')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('info'),
        ];
    }
}
