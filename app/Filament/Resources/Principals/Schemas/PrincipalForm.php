<?php

namespace App\Filament\Resources\Principals\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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

                Section::make('Business Details')
                    ->schema([
                        Select::make('supplier_type')
                            ->options([
                                'IVD' => 'IVD',
                                'CL' => 'CL',
                                'Non-CL' => 'Non-CL',
                            ])
                            ->default(null),
                    ])
                    ->columns(2),

                Section::make('Contact Information')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('website')
                            ->label('Website')
                            ->url()
                            ->default(null),
                        Textarea::make('address')
                            ->label('Address')
                            ->default(null)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
