<?php

namespace App\Filament\Resources\Targets\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TargetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label('Sales Representative')
                    ->relationship('user', 'name')
                    ->required(),
                TextInput::make('year')
                    ->label('Year')
                    ->required()
                    ->numeric()
                    ->minValue(2020)
                    ->maxValue(2030),
                Select::make('month')
                    ->label('Month')
                    ->options([
                        1 => 'January',
                        2 => 'February',
                        3 => 'March',
                        4 => 'April',
                        5 => 'May',
                        6 => 'June',
                        7 => 'July',
                        8 => 'August',
                        9 => 'September',
                        10 => 'October',
                        11 => 'November',
                        12 => 'December',
                    ])
                    ->nullable(),
                TextInput::make('annual_target')
                    ->label('Annual Target')
                    ->numeric()
                    ->prefix('Rp')
                    ->nullable(),
                TextInput::make('monthly_target')
                    ->label('Monthly Target')
                    ->numeric()
                    ->prefix('Rp')
                    ->nullable(),
            ]);
    }
}
