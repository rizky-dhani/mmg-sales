<?php

namespace App\Filament\Resources\Positions\Schemas;

use App\Models\Position;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PositionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('level')
                    ->required()
                    ->numeric()
                    ->helperText('Lowest number = highest position. '.Position::DIRECTOR_LEVEL.'=Director, '.Position::RSM_LEVEL.'=RSM, '.Position::ASM_LEVEL.'=ASM, '.Position::SPV_LEVEL.'=SPV, '.Position::SR_LEVEL.'=Sales Rep'),
                Select::make('parent_id')
                    ->relationship('parent', 'name')
                    ->default(null)
                    ->preload(),
                Select::make('department_id')
                    ->relationship('department', 'name')
                    ->required()
                    ->preload(),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
