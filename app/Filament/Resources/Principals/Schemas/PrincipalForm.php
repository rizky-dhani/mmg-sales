<?php

namespace App\Filament\Resources\Principals\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PrincipalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('initial')
                            ->label('Initial')
                            ->required(),
                        Toggle::make('is_active')
                            ->required(),
                    ])
                    ->columns(2),

            ]);
    }
}
