<?php

namespace App\Filament\Resources\SalesTypes\Pages;

use App\Filament\Resources\SalesTypes\SalesTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSalesType extends EditRecord
{
    protected static string $resource = SalesTypeResource::class;

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
