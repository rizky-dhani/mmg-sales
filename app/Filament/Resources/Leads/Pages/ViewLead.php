<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use App\Filament\Widgets\LeadRevenueComparisonChart;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewLead extends ViewRecord
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            LeadRevenueComparisonChart::make(['record' => $this->record]),
        ];
    }
}
