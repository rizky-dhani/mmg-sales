<?php

namespace App\Filament\Resources\SalesTypes;

use App\Filament\Resources\SalesTypes\Pages\CreateSalesType;
use App\Filament\Resources\SalesTypes\Pages\EditSalesType;
use App\Filament\Resources\SalesTypes\Pages\ListSalesTypes;
use App\Filament\Resources\SalesTypes\Schemas\SalesTypeForm;
use App\Filament\Resources\SalesTypes\Tables\SalesTypesTable;
use App\Models\SalesType;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class SalesTypeResource extends Resource
{
    protected static ?string $model = SalesType::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Sales';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SalesTypeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SalesTypesTable::configure($table);
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
            'index' => ListSalesTypes::route('/'),
            'create' => CreateSalesType::route('/create'),
            'edit' => EditSalesType::route('/{record}/edit'),
        ];
    }
}
