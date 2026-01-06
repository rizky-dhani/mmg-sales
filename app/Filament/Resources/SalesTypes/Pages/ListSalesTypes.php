<?php

namespace App\Filament\Resources\SalesTypes\Pages;

use App\Filament\Resources\SalesTypes\SalesTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSalesTypes extends ListRecords
{
    protected static string $resource = SalesTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
