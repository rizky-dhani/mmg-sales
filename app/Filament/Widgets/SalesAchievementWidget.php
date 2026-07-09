<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use NumberFormatter;

class SalesAchievementWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();

        if (! $user) {
            return [];
        }

        $target = (float) ($user->sales_target ?? 0);

        $shippedOrders = Order::query()
            ->where('created_by', $user->id)
            ->whereHas('deliveryStatuses', fn ($q) => $q->whereNotNull('shipped_date'))
            ->whereYear('order_date', now()->year);

        $shippedCount = $shippedOrders->count();
        $shippedAmount = $shippedOrders->sum('total_amount');

        $openOrders = Order::query()
            ->where('created_by', $user->id)
            ->whereDoesntHave('deliveryStatuses')
            ->whereDoesntHave('paymentStatuses')
            ->whereYear('order_date', now()->year);

        $openCount = $openOrders->count();
        $openAmount = $openOrders->sum('total_amount');

        $formatter = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);
        $formattedTarget = $formatter->formatCurrency($target, 'IDR');
        $formattedShipped = $formatter->formatCurrency($shippedAmount, 'IDR');
        $formattedOpen = $formatter->formatCurrency($openAmount, 'IDR');

        $achievement = $target > 0 ? ($shippedAmount / $target) * 100 : 0;
        $color = 'danger';
        if ($achievement >= 100) {
            $color = 'success';
        } elseif ($achievement >= 75) {
            $color = 'warning';
        }

        return [
            Stat::make('Annual Sales Target', $formattedTarget)
                ->description('Yearly target goal')
                ->descriptionIcon('heroicon-m-flag'),
            Stat::make('Open Orders (YTD)', $openCount.' orders')
                ->description($formattedOpen.' in open orders')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Shipped Orders (YTD)', $shippedCount.' orders')
                ->description($formattedShipped.' in shipped orders')
                ->descriptionIcon('heroicon-m-truck')
                ->color('primary'),
            Stat::make('Target Achievement', number_format($achievement, 1).'%')
                ->description($achievement >= 100 ? 'Target Surpassed' : 'Remaining: '.$formatter->formatCurrency(max(0, $target - $shippedAmount), 'IDR'))
                ->color($color),
        ];
    }
}
