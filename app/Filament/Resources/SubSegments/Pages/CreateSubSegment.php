<?php

namespace App\Filament\Resources\SubSegments\Pages;

use App\Filament\Resources\SubSegments\SubSegmentResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateSubSegment extends CreateRecord
{
    protected static string $resource = SubSegmentResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->successNotificationTitle('Sub segment created successfully');
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
