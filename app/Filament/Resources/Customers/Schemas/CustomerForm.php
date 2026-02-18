<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Customer Information')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->columnSpanFull(),
                        Select::make('type')
                            ->options([
                                'hospital' => 'Hospital',
                                'clinic' => 'Clinic',
                                'pharmacy' => 'Pharmacy',
                                'laboratory' => 'Laboratory',
                                'distributor' => 'Distributor',
                                'other' => 'Other',
                            ])
                            ->default('other')
                            ->required(),
                        TextInput::make('tax_number')
                            ->default(null),
                    ]),

                Grid::make(2)
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Contact Information')
                            ->schema([
                                TextInput::make('email')
                                    ->label('Email address')
                                    ->email()
                                    ->default(null),
                                TextInput::make('phone')
                                    ->tel()
                                    ->default(null),
                                TextInput::make('website')
                                    ->url()
                                    ->default(null),
                            ]),

                        Section::make('Address')
                            ->columns(2)
                            ->schema([
                                TextInput::make('address')
                                    ->default(null)
                                    ->columnSpanFull(),
                                TextInput::make('city')
                                    ->default(null),
                                TextInput::make('state')
                                    ->default(null),
                                TextInput::make('postal_code')
                                    ->default(null),
                                TextInput::make('country')
                                    ->required()
                                    ->default('Indonesia'),
                            ]),
                    ]),

                Section::make('Settings')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('payment_terms_days')
                            ->label('Payment Terms (Days)')
                            ->numeric()
                            ->default(30),
                        Toggle::make('is_active')
                            ->default(true),
                    ]),
            ]);
    }
}
