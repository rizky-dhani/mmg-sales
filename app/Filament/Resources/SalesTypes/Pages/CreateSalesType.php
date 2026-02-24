<?php

namespace App\Filament\Resources\SalesTypes\Pages;

use App\Filament\Resources\SalesTypes\SalesTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSalesType extends CreateRecord
{
    protected static string $resource = SalesTypeResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
