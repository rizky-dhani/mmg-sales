<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LeadStatusOverview extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return true;
    }

    protected function getStats(): array
    {
        $totalCount = Lead::count();
        $wonCount = Lead::where('status', 'won')->count();
        $lostCount = Lead::where('status', 'lost')->count();
        $inProgressCount = Lead::whereNotIn('status', ['new', 'won', 'lost'])->count();

        return [
            Stat::make('Converted', $wonCount)
                ->description('Partnerships established')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->url(route('filament.admin.resources.leads.index', ['tableFilters[status][value]' => 'won'])),

            Stat::make('In Progress', $inProgressCount)
                ->description('Active negotiations/proposals')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info')
                ->url(route('filament.admin.resources.leads.index')),

            Stat::make('Did Not Convert', $lostCount)
                ->description('Opportunities learned from')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('warning')
                ->url(route('filament.admin.resources.leads.index', ['tableFilters[status][value]' => 'lost'])),

            Stat::make('Total', $totalCount)
                ->description('All leads')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->url(route('filament.admin.resources.leads.index')),
        ];
    }
}
