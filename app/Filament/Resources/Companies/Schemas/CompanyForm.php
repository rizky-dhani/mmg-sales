<?php

namespace App\Filament\Resources\Companies\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CompanyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('facility_name')
                    ->required(),
                Select::make('facility_type')
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
                Select::make('classification')
                    ->options(['tier_1' => 'Tier 1', 'tier_2' => 'Tier 2', 'tier_3' => 'Tier 3'])
                    ->default('tier_3')
                    ->required(),
                TextInput::make('tax_number')
                    ->default(null),
                TextInput::make('address')
                    ->default(null),
                TextInput::make('city')
                    ->default(null),
                TextInput::make('state')
                    ->default(null),
                TextInput::make('postal_code')
                    ->default(null),
                TextInput::make('country')
                    ->required()
                    ->default('Indonesia'),
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
                TextInput::make('credit_limit')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('payment_terms_days')
                    ->required()
                    ->numeric()
                    ->default(30),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('assigned_to')
                    ->numeric()
                    ->default(null),
            ]);
    }
}
