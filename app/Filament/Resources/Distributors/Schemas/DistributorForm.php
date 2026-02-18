<?php

namespace App\Filament\Resources\Distributors\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class DistributorForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('code')
                            ->required(),
                        Toggle::make('is_active')
                            ->required(),
                    ])
                    ->columns(2),

                Section::make('Contact Information')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('city')
                            ->default(null),
                        TextInput::make('phone')
                            ->tel()
                            ->default(null),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->default(null),
                        Textarea::make('address')
                            ->default(null)
                            ->autosize()
                            ->columnSpanFull(),
                    ])
                    ->columns(3),
            ]);
    }
}
