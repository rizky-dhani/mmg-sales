<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Infolists\Components\TextEntry;

class CustomerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Facility Information')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('facility_name')
                            ->label('Facility Name')
                            ->weight('bold'),
                        TextEntry::make('facility_type')
                            ->label('Type')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                        TextEntry::make('classification')
                            ->label('Tier')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => str_replace('_', ' ', ucfirst($state))),
                        TextEntry::make('tax_number')
                            ->label('Tax Number'),
                        TextEntry::make('credit_limit')
                            ->money('IDR'),
                        TextEntry::make('payment_terms_days')
                            ->label('Payment Terms')
                            ->suffix(' Days'),
                    ]),

                Section::make('Location & Contact')
                    ->columns(6)
                    ->schema([
                        TextEntry::make('city'),
                        TextEntry::make('state')
                        ->label('Province'),
                        TextEntry::make('postal_code'),
                        TextEntry::make('country'),
                        TextEntry::make('email')
                        ->copyable(),
                        TextEntry::make('phone'),
                        TextEntry::make('address')
                        ->columnSpan(3),
                        TextEntry::make('website')
                            ->columnSpan(3)
                            ->url(fn ($state) => $state, true),
                    ]),

                Section::make('Assignment')
                    ->schema([
                        TextEntry::make('assignedUser.name')
                            ->label('Account Manager'),
                    ]),
            ]);
    }
}