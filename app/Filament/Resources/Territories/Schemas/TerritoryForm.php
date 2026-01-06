<?php

namespace App\Filament\Resources\Territories\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TerritoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('wilayah_code')
                    ->default(null),
                Select::make('type')
                    ->options(['region' => 'Region', 'province' => 'Province', 'city' => 'City'])
                    ->required(),
                TextInput::make('level')
                    ->required()
                    ->numeric(),
                Select::make('parent_id')
                    ->relationship('parent', 'name')
                    ->default(null),
                Select::make('manager_id')
                    ->relationship('manager', 'name')
                    ->default(null),
            ]);
    }
}
