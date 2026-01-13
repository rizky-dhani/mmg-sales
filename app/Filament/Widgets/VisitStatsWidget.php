<?php

namespace App\Filament\Widgets;

use App\Services\VisitScopeService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class VisitStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $service = app(VisitScopeService::class);
        $stats = $service->getVisitStats($user);

        return [
            Stat::make('Total Visits', $stats['total'])
                ->description('All time visits')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('primary'),
            Stat::make('This Month', $stats['monthly'])
                ->description($stats['growth'] . '% ' . ($stats['growth'] >= 0 ? 'increase' : 'decrease'))
                ->descriptionIcon($stats['growth'] >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($stats['growth'] >= 0 ? 'success' : 'danger'),
        ];
    }
}