<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn($query) => $query->where('id', '!=', auth()->user()->id)->orWhere('id', '!=', 1))
            ->columns([
                TextColumn::make('name')
                    ->label('User Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                TextColumn::make('department.name')
                    ->label('Department')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('position.name')
                    ->label('Position')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('roles.name')
                    ->label('Roles')
                    ->badge()
                    ->color('success'),

                TextColumn::make('manager.name')
                    ->label('Manager')
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->date('d M Y')
                    ->formatStateUsing(fn ($state) => strtoupper(\Carbon\Carbon::parse($state)->translatedFormat('d M Y')))
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
