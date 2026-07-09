<?php

namespace App\Filament\Resources\Leads\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ActivityCommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'activityComments';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Select::make('activity_id')
                ->label('Activity')
                ->relationship('activity', 'subject')
                ->searchable()
                ->required(),
            Forms\Components\Textarea::make('comment')
                ->required()
                ->rows(3)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('comment')
            ->columns([
                Tables\Columns\TextColumn::make('activity.activity_code')
                    ->label('Activity')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Author')
                    ->searchable(),
                Tables\Columns\TextColumn::make('comment')
                    ->label('Comment')
                    ->limit(80)
                    ->wrap(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Posted')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->mutateFormDataUsing(fn (array $data) => array_merge($data, [
                        'user_id' => auth()->id(),
                    ])),
            ])
            ->actions([
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
