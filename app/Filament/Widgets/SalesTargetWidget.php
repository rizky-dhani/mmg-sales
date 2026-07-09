<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Target;
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

        $target = Target::where('user_id', $user->id)
            ->where('year', now()->year)
            ->where('month', now()->month)
            ->first();

        $monthlyTarget = (float) ($target->monthly_target ?? 0);
        $annualTarget = (float) ($target->annual_target ?? 0);

        $shippedAmount = Order::query()
            ->where('created_by', $user->id)
            ->whereHas('deliveryStatuses', fn ($q) => $q->whereNotNull('shipped_date'))
            ->whereYear('order_date', now()->year)
            ->sum('total_amount');

        $achievement = $annualTarget > 0 ? ($shippedAmount / $annualTarget) * 100 : 0;
        $color = 'danger';
        if ($achievement >= 100) {
            $color = 'success';
        } elseif ($achievement >= 75) {
            $color = 'warning';
        }

        $formatter = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);

        return [
            Stat::make('Monthly Target', $formatter->formatCurrency($monthlyTarget, 'IDR'))
                ->description(now()->translatedFormat('F Y'))
                ->descriptionIcon('heroicon-m-calendar'),
            Stat::make('Annual Target', $formatter->formatCurrency($annualTarget, 'IDR'))
                ->description(now()->year.' target')
                ->descriptionIcon('heroicon-m-flag'),
            Stat::make('Target Achievement', number_format($achievement, 1).'%')
                ->description($achievement >= 100 ? 'Target Surpassed' : 'Remaining: '.$formatter->formatCurrency(max(0, $annualTarget - $shippedAmount), 'IDR'))
                ->color($color),
        ];
    }
}
