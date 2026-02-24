<?php

namespace App\Filament\Resources\Territories\Pages;

use App\Filament\Resources\Territories\TerritoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTerritory extends CreateRecord
{
    protected static string $resource = TerritoryResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
