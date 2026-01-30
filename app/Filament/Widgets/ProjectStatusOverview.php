<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProjectStatusOverview extends BaseWidget
{
    protected ?string $heading = 'Project Pipeline Status';

    public static function canView(): bool
    {
        return false;
    }

    protected function getStats(): array
    {
        $wonCount = Project::where('status', 'won')->count();
        $lostCount = Project::where('status', 'lost')->count();
        $inProgressCount = Project::whereNotIn('status', ['new', 'won', 'lost'])->count();

        return [
            Stat::make('Won Projects', $wonCount)
                ->description('Converted to companies')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success')
                ->url(route('filament.admin.resources.projects.index', ['tableFilters[status][value]' => 'won'])),

            Stat::make('In Progress', $inProgressCount)
                ->description('Active negotiations/proposals')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('info')
                ->url(route('filament.admin.resources.projects.index')),

            Stat::make('Lost Projects', $lostCount)
                ->description('Dropped opportunities')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger')
                ->url(route('filament.admin.resources.projects.index', ['tableFilters[status][value]' => 'lost'])),
        ];
    }
}
