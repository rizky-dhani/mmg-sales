<?php

namespace App\Filament\Resources\CompanyGroups;

use App\Filament\Resources\CompanyGroups\Pages\CreateCompanyGroup;
use App\Filament\Resources\CompanyGroups\Pages\EditCompanyGroup;
use App\Filament\Resources\CompanyGroups\Pages\ListCompanyGroups;
use App\Filament\Resources\CompanyGroups\Schemas\CompanyGroupForm;
use App\Filament\Resources\CompanyGroups\Tables\CompanyGroupsTable;
use App\Models\CompanyGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CompanyGroupResource extends Resource
{
    protected static ?string $model = CompanyGroup::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|\UnitEnum|null $navigationGroup = 'CRM';

    protected static ?string $navigationParentItem = 'Companies';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return CompanyGroupForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompanyGroupsTable::configure($table);
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
            'index' => ListCompanyGroups::route('/'),
            'create' => CreateCompanyGroup::route('/create'),
            'edit' => EditCompanyGroup::route('/{record}/edit'),
        ];
    }
}
