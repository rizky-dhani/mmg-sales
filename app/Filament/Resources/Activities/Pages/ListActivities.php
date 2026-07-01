<?php

namespace App\Filament\Resources\Activities\Pages;

use App\Filament\Resources\Activities\ActivityResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListActivities extends ListRecords
{
    protected static string $resource = ActivityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'interested' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('outcome', 'Interested')),
            'not_interested' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('outcome', 'Not Interested')),
            'no_answer' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('outcome', 'No Answer')),
            'need_more_info' => Tab::make()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('outcome', 'Need more info')),
        ];
    }
}
