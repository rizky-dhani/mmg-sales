<?php

namespace App\Filament\Resources\Products\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('sku')
                    ->label('SKU')
                    ->required(),
                Select::make('category')
                    ->options([
                        'medical_equipment' => 'Medical equipment',
                        'pharmaceutical' => 'Pharmaceutical',
                        'consumables' => 'Consumables',
                        'diagnostics' => 'Diagnostics',
                        'other' => 'Other',
                    ])
                    ->default('other')
                    ->required(),
                Select::make('principal_id')
                    ->relationship('principal', 'name')
                    ->searchable()
                    ->preload(),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('unit_price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('unit_of_measure')
                    ->required()
                    ->default('pcs'),
                TextInput::make('stock_quantity')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('minimum_stock')
                    ->required()
                    ->numeric()
                    ->default(10),
                TextInput::make('reorder_quantity')
                    ->required()
                    ->numeric()
                    ->default(100),
                Toggle::make('is_active')
                    ->required(),
                Toggle::make('requires_prescription')
                    ->required(),
                TextInput::make('manufacturer')
                    ->default(null),
                DatePicker::make('expiry_date'),
                TextInput::make('storage_requirements')
                    ->default(null),
            ]);
    }
}
