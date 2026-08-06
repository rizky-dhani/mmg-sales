<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Principal;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SalesOverview extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return true;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Total Orders', Order::count())
                ->description('All orders placed')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('primary')
                ->url(route('filament.admin.resources.orders.index')),

            Stat::make('Total Principals', Principal::count())
                ->description('Active principals')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('warning')
                ->url(route('filament.admin.resources.principals.index')),
        ];
    }
}
