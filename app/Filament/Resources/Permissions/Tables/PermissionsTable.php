<?php

namespace App\Filament\Resources\Permissions\Tables;

use App\Helpers\PermissionHelper;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PermissionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function (string $state): string {
                        $parsed = PermissionHelper::parsePermissionName($state);
                        if ($parsed === null) {
                            return $state;
                        }

                        return PermissionHelper::getActionLabel($parsed['action'])
                            .' '
                            .PermissionHelper::getModelLabel($parsed['model']);
                    }),
                TextColumn::make('guard_name')
                    ->badge()
                    ->color('gray')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
