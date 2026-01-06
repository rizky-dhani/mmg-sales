<?php

namespace App\Filament\Resources\SubSegments\Pages;

use App\Filament\Resources\SubSegments\SubSegmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSubSegments extends ListRecords
{
    protected static string $resource = SubSegmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
