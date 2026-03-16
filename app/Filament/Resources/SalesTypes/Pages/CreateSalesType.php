<?php

namespace App\Filament\Resources\SalesTypes\Pages;

use App\Filament\Resources\SalesTypes\SalesTypeResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateSalesType extends CreateRecord
{
    protected static string $resource = SalesTypeResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->successNotificationTitle('Sales type created successfully');
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
