<?php

namespace App\Filament\Resources\Principals\Pages;

use App\Filament\Resources\Principals\PrincipalResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePrincipal extends CreateRecord
{
    protected static string $resource = PrincipalResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->successNotificationTitle('Principal created successfully');
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
