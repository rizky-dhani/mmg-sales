<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\LeadStatusChart;
use App\Filament\Widgets\LeadStatusOverview;
use App\Filament\Widgets\TopSalesRepresentativeVisitsWidget;
use App\Filament\Widgets\TopVisitedCustomersWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard';

    protected static ?string $navigationLabel = 'Dashboard';

    protected static ?int $navigationSort = -2;

    public function getWidgets(): array
    {
        return [
            LeadStatusOverview::class,
            LeadStatusChart::class,
            TopVisitedCustomersWidget::class,
            TopSalesRepresentativeVisitsWidget::class,
        ];
    }
}
