<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Project;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use NumberFormatter;

class CRMOverview extends BaseWidget
{
    protected ?string $heading = 'CRM Analytics';

    public static function canView(): bool
    {
        return true;
    }

    protected function getStats(): array
    {
        $totalCompanies = Customer::count();
        $totalProjects = Project::count();

        // Pipeline Value: Sum of estimated revenue for open projects
        $pipelineValue = Project::whereNotIn('status', ['won', 'lost'])->sum('estimated_revenue');

        // Stale Projects: No contact for > 30 days
        $staleProjectsCount = Project::whereNotIn('status', ['won', 'lost'])
            ->where(function ($query) {
                $query->where('last_contacted_at', '<', Carbon::now()->subDays(30))
                    ->orWhereNull('last_contacted_at');
            })
            ->count();

        $formatter = new NumberFormatter('id_ID', NumberFormatter::CURRENCY);
        $formatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);

        return [
            Stat::make('Total Companies', $totalCompanies)
                ->description('Active facilities')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary')
                ->url(route('filament.admin.resources.companies.index')),

            Stat::make('Pipeline Value', $formatter->formatCurrency($pipelineValue, 'IDR'))
                ->description('Potential revenue from open projects')
                ->descriptionIcon('heroicon-m-presentation-chart-line')
                ->color('success')
                ->url(route('filament.admin.resources.projects.index')),

            Stat::make('Stale Projects', $staleProjectsCount)
                ->description('No contact for > 30 days')
                ->descriptionIcon('heroicon-m-clock')
                ->color($staleProjectsCount > 0 ? 'warning' : 'gray')
                ->url(route('filament.admin.resources.projects.index')),
        ];
    }
}
