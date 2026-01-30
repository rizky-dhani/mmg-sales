<?php

namespace App\Filament\Resources\Principals\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PrincipalForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('code')
                    ->required(),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('contact_person')
                    ->default(null),
                TextInput::make('phone')
                    ->tel()
                    ->default(null),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->default(null),
                TextInput::make('website')
                    ->url()
                    ->default(null),
                Select::make('supplier_type')
                    ->options([
                        'IVD' => 'IVD',
                        'CL' => 'CL',
                        'Non-CL' => 'Non-CL',
                    ])
                    ->default(null),
                TextInput::make('annual_target')
                    ->label('Annual Target')
                    ->numeric()
                    ->prefix('IDR')
                    ->default(0),
                Textarea::make('address')
                    ->default(null)
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
