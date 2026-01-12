<?php

namespace App\Filament\Resources\Visits\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VisitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('visit_started_at')
                    ->label('Date & Time')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('duration')
                    ->label('Duration')
                    ->getStateUsing(function ($record) {
                        if (! $record->visit_started_at || ! $record->visit_ended_at) {
                            return '-';
                        }

                        return $record->visit_started_at->diffForHumans($record->visit_ended_at, true);
                    }),
                TextColumn::make('user.name')
                    ->label('Sales Rep')
                    ->sortable(),
                TextColumn::make('company.facility_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('purpose')
                    ->limit(30)
                    ->searchable(),
                IconColumn::make('is_worth_keeping')
                    ->label('Worth it?')
                    ->boolean()
                    ->placeholder('Pending Review'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
