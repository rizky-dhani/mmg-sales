<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Lead;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CRMOverview extends BaseWidget
{
    protected static ?string $heading = 'CRM Analytics';

    protected function getStats(): array
    {
        $totalCustomers = Customer::count();
        $totalLeads = Lead::count();
        $newLeads = Lead::where('status', 'new')->count();

        $wonLeads = Lead::where('status', 'won')->count();
        $conversionRate = $totalLeads > 0 ? round(($wonLeads / $totalLeads) * 100, 2) : 0;

        return [
            Stat::make('Total Customers', $totalCustomers)
                ->description('Active facilities')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary')
                ->url(route('filament.admin.resources.customers.index')),
            Stat::make('Total Leads', $totalLeads)
                ->description($newLeads.' new leads to follow up')
                ->descriptionIcon('heroicon-m-light-bulb')
                ->color('info')
                ->url(route('filament.admin.resources.leads.index')),
            Stat::make('Lead Conversion', $conversionRate.'%')
                ->description('Leads converted to won')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color($conversionRate > 20 ? 'success' : 'warning')
                ->url(route('filament.admin.resources.leads.index', ['tableFilters[status][value]' => 'won'])),
        ];
    }
}
