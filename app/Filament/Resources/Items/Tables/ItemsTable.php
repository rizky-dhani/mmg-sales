<?php

namespace App\Filament\Resources\Items\Tables;

use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->orderBy('created_at', 'desc'))
            ->columns([
                TextColumn::make('name')
                    ->label('Item Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('internal_code')
                    ->label('Internal Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('principal.name')
                    ->label('Principal')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('unit_price')
                    ->label('Price')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('unit')
                    ->label('UoM')
                    ->searchable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->date('d M Y')
                    ->formatStateUsing(fn ($state) => strtoupper(Carbon::parse($state)->translatedFormat('d M Y')))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
