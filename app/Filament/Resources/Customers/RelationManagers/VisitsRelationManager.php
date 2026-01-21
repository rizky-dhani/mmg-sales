<?php

namespace App\Filament\Resources\Customers\RelationManagers;

use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class VisitsRelationManager extends RelationManager
{
    protected static string $relationship = 'visits';

    public static function canViewForRecord(\Illuminate\Database\Eloquent\Model $ownerRecord, string $pageClass): bool
    {
        return $pageClass === \App\Filament\Resources\Customers\Pages\ViewCustomer::class;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                DatePicker::make('visit_started_at')
                    ->required(),
                TextInput::make('purpose')
                    ->required()
                    ->maxLength(255),
                Textarea::make('summary_notes')
                    ->maxLength(65535),
                Textarea::make('stakeholder_feedback')
                    ->maxLength(65535),
                IconColumn::make('is_worth_keeping')
                    ->boolean(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('purpose')
            ->columns([
                TextColumn::make('user.name')
                    ->label('Representative')
                    ->sortable(),
                TextColumn::make('visit_started_at')
                    ->label('Date')
                    ->date()
                    ->sortable(),
                TextColumn::make('purpose')
                    ->searchable(),
                TextColumn::make('stakeholder_feedback')
                    ->label('Feedback')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // No CreateAction
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                // No BulkActions
            ]);
    }
}
