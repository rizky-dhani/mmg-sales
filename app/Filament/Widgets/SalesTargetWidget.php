<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;
use NumberFormatter;

class SalesTargetWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $user = Auth::user();

        if (! $user) {
            return [];
        }

        $target = (float) ($user->sales_target ?? 0);

        // Summing net_sales_total for orders created by the user in the current year
        $actual = Order::query()
            ->where('created_by', $user->id)
            ->whereYear('order_date', now()->year)
            ->sum('net_sales_total');

        $formatter = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);
        $formattedTarget = $formatter->formatCurrency($target, 'IDR');
        $formattedActual = $formatter->formatCurrency($actual, 'IDR');

        $achievement = $target > 0 ? ($actual / $target) * 100 : 0;
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
            Stat::make('Current Revenue (YTD)', $formattedActual)
                ->description(number_format($achievement, 1).'% of target achieved')
                ->descriptionIcon($achievement >= 100 ? 'heroicon-m-check-circle' : 'heroicon-m-arrow-trending-up')
                ->color($color),
            Stat::make('Target vs Actual Gauge', number_format($achievement, 1).'%')
                ->description($achievement >= 100 ? 'Target Surpassed' : 'Remaining: '.$formatter->formatCurrency(max(0, $target - $actual), 'IDR'))
                ->color($color)
                ->chart([7, 10, 5, 2, 20, 30, 45, 60, achievement_to_chart($achievement)]), // Simple chart representation
        ];
    }
}

function achievement_to_chart($achievement)
{
    return min(100, $achievement);
}
