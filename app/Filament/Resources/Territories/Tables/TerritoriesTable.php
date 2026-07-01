<?php

namespace App\Filament\Resources\Territories\Tables;

use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TerritoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->orderBy('created_at', 'desc'))
            ->columns([
                TextColumn::make('name')
                    ->label('Territory Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'region' => 'primary',
                        'province' => 'info',
                        'city' => 'success',
                        default => 'gray',
                    }),

                TextColumn::make('wilayah_code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('parent.name')
                    ->label('Parent Territory')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('manager.name')
                    ->label('Manager')
                    ->searchable()
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
