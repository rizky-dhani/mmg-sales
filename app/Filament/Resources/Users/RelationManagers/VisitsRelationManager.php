<?php

namespace App\Filament\Resources\Users\RelationManagers;

use App\Filament\Resources\Visits\Schemas\VisitForm;
use App\Filament\Resources\Visits\Schemas\VisitInfolist;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VisitsRelationManager extends RelationManager
{
    protected static string $relationship = 'visits';

    public function form(Schema $schema): Schema
    {
        return VisitForm::configure($schema);
    }

    public function infolist(Schema $schema): Schema
    {
        return VisitInfolist::configure($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('purpose')
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
                TextColumn::make('customer.facility_name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('purpose')
                    ->limit(30)
                    ->searchable(),
                IconColumn::make('is_worth_keeping')
                    ->label('Worth it?')
                    ->boolean()
                    ->placeholder('Pending Review'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
