<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Services\ActivityScopeService;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CustomerActivityStatsWidget extends StatsOverviewWidget
{
    public ?Model $record = null;

    protected function getStats(): array
    {
        if (! $this->record instanceof Customer) {
            return [];
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $service = app(ActivityScopeService::class);
        $stats = $service->getCustomerActivityStats($user, $this->record->id);

        return [
            Stat::make('Total Customer Interactions', $stats['total'])
                ->description('All time activities with this customer')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('primary'),
            Stat::make('Last Interaction', $stats['last_activity_date']?->format('d M Y') ?? 'No activities yet')
                ->description('Most recent interaction')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
        ];
    }
}
