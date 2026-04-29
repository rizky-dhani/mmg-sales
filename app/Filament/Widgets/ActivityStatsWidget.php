<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Services\ActivityScopeService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class ActivityStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        /** @var User $user */
        $user = Auth::user();
        $service = app(ActivityScopeService::class);
        $stats = $service->getActivityStats($user, ['In-person Meeting', 'Online Meeting', 'Demo', 'Presentation']);

        return [
            Stat::make('Monthly Interactions', $stats['monthly'])
                ->description($stats['growth'].'% '.($stats['growth'] >= 0 ? 'increase' : 'decrease'))
                ->descriptionIcon($stats['growth'] >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($stats['growth'] >= 0 ? 'success' : 'danger'),
            Stat::make('Top Co. Engagement', $stats['top_rep_customer_count'])
                ->description('Most activities with one customer by '.$stats['top_rep_name'])
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('info'),
        ];
    }
}
