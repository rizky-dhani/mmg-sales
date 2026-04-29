<?php

namespace App\Filament\Resources\Products\Schemas;

use Carbon\Carbon;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Product Specifications')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name')
                            ->label('Product Name')
                            ->weight('bold')
                            ->columnSpan(2),
                        TextEntry::make('sku')
                            ->label('SKU'),
                        TextEntry::make('principal.name')
                            ->label('Principal'),
                        TextEntry::make('category')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => ucfirst(str_replace('_', ' ', $state))),
                        TextEntry::make('unit_price')
                            ->label('Price')
                            ->money('IDR'),
                        TextEntry::make('unit_of_measure')
                            ->label('UoM'),
                        TextEntry::make('manufacturer'),
                        TextEntry::make('expiry_date')
                            ->date('d M Y')
                            ->formatStateUsing(fn ($state) => $state ? strtoupper(Carbon::parse($state)->translatedFormat('d M Y')) : '-'),
                        TextEntry::make('storage_requirements'),
                    ]),

                Section::make('Inventory')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('stock_quantity')
                            ->label('Current Stock')
                            ->weight('bold')
                            ->color(fn ($record) => $record->stock_quantity <= $record->minimum_stock ? 'danger' : 'success'),
                        TextEntry::make('minimum_stock')
                            ->label('Min. Stock'),
                        TextEntry::make('reorder_quantity')
                            ->label('Reorder Qty'),
                    ]),

                Section::make('Description')
                    ->schema([
                        TextEntry::make('description')
                            ->hiddenLabel()
                            ->markdown(),
                    ]),
            ]);
    }
}
