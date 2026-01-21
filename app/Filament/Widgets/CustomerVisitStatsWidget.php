<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Services\VisitScopeService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CustomerVisitStatsWidget extends StatsOverviewWidget
{
    public ?Model $record = null;

    protected function getStats(): array
    {
        if (!$this->record instanceof Customer) {
            return [];
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $service = app(VisitScopeService::class);
        $stats = $service->getCustomerVisitStats($user, $this->record->id);

        return [
            Stat::make('Total Customer Visits', $stats['total'])
                ->description('All time visits to this customer')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('primary'),
            Stat::make('Last Visit', $stats['last_visit_date']?->format('d M Y') ?? 'No visits yet')
                ->description('Most recent interaction')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
        ];
    }
}