<?php

namespace App\Filament\Resources\Activities\Pages;

use App\Filament\Resources\Activities\ActivityResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewActivity extends ViewRecord
{
    protected static string $resource = ActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActivityResource::getChecklistAction()
                ->record($this->getRecord()->project)
                ->visible(fn () => $this->getRecord()->project_id),
            EditAction::make(),
        ];
    }
}
