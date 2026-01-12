<?php

namespace App\Filament\Resources\Segments;

use App\Filament\Resources\Segments\Pages\CreateSegment;
use App\Filament\Resources\Segments\Pages\EditSegment;
use App\Filament\Resources\Segments\Pages\ListSegments;
use App\Filament\Resources\Segments\Schemas\SegmentForm;
use App\Filament\Resources\Segments\Tables\SegmentsTable;
use App\Models\Segment;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SegmentResource extends Resource
{
    protected static ?string $model = Segment::class;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->hasRole('Super Admin') ?? false;
    }

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedAdjustmentsHorizontal;

    protected static string|\UnitEnum|null $navigationGroup = 'CRM';

    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return SegmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SegmentsTable::configure($table);
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
            'index' => ListSegments::route('/'),
            'create' => CreateSegment::route('/create'),
            'edit' => EditSegment::route('/{record}/edit'),
        ];
    }
}
