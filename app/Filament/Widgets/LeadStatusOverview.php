<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LeadStatusOverview extends BaseWidget
{
    protected ?string $heading = 'Lead Pipeline Status';

    public static function canView(): bool
    {
        return true;
    }

    protected function getStats(): array
    {
        $wonCount = Lead::where('status', 'won')->count();
        $lostCount = Lead::where('status', 'lost')->count();
        $inProgressCount = Lead::whereNotIn('status', ['new', 'won', 'lost'])->count();

        return [
            Stat::make('Won Leads', $wonCount)
                ->description('Converted to customers')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->url(route('filament.admin.resources.leads.index', ['tableFilters[status][value]' => 'won'])),

            Stat::make('In Progress', $inProgressCount)
                ->description('Active negotiations/proposals')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info')
                ->url(route('filament.admin.resources.leads.index')),

            Stat::make('Lost Leads', $lostCount)
                ->description('Dropped opportunities')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger')
                ->url(route('filament.admin.resources.leads.index', ['tableFilters[status][value]' => 'lost'])),
        ];
    }
}
