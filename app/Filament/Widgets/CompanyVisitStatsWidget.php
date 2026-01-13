<?php

namespace App\Filament\Widgets;

use App\Models\Company;
use App\Services\VisitScopeService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CompanyVisitStatsWidget extends StatsOverviewWidget
{
    public ?Model $record = null;

    protected function getStats(): array
    {
        if (!$this->record instanceof Company) {
            return [];
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $service = app(VisitScopeService::class);
        $stats = $service->getCompanyVisitStats($user, $this->record->id);

        return [
            Stat::make('Total Company Visits', $stats['total'])
                ->description('All time visits to this company')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('primary'),
            Stat::make('Last Visit', $stats['last_visit_date']?->format('d M Y') ?? 'No visits yet')
                ->description('Most recent interaction')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
        ];
    }
}