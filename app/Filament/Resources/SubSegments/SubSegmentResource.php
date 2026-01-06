<?php

namespace App\Filament\Resources\SubSegments;

use App\Filament\Resources\SubSegments\Pages\CreateSubSegment;
use App\Filament\Resources\SubSegments\Pages\EditSubSegment;
use App\Filament\Resources\SubSegments\Pages\ListSubSegments;
use App\Filament\Resources\SubSegments\Schemas\SubSegmentForm;
use App\Filament\Resources\SubSegments\Tables\SubSegmentsTable;
use App\Models\SubSegment;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SubSegmentResource extends Resource
{
    protected static ?string $model = SubSegment::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-vertical';

    protected static ?string $navigationGroup = 'Configuration';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SubSegmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SubSegmentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSubSegments::route('/'),
            'create' => CreateSubSegment::route('/create'),
            'edit' => EditSubSegment::route('/{record}/edit'),
        ];
    }
}
