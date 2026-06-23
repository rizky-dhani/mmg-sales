<?php

namespace App\Filament\Resources\Permissions\Tables;

use App\Helpers\PermissionHelper;
use App\Models\Department;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

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
                TextColumn::make('model')
                    ->label('Model')
                    ->state(fn ($record): ?string => PermissionHelper::parsePermissionName($record->name)['model'] ?? null)
                    ->formatStateUsing(fn (?string $state): string => $state ? PermissionHelper::getModelLabel($state) : '-')
                    ->sortable(),
                TextColumn::make('roles.name')
                    ->label('Assigned Roles')
                    ->badge()
                    ->color('gray')
                    ->placeholder('None')
                    ->sortable(),
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
                SelectFilter::make('roles')
                    ->label('Role')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),
                SelectFilter::make('department')
                    ->label('Department')
                    ->options(fn (): array => Department::pluck('name', 'id')->toArray())
                    ->query(function ($query, array $data): void {
                        if (empty($data['value'])) {
                            return;
                        }

                        $departmentIds = is_array($data['value']) ? $data['value'] : [$data['value']];

                        $query->whereHas('roles', function ($q) use ($departmentIds): void {
                            $q->whereIn('department_id', $departmentIds);
                        });
                    })
                    ->multiple(),
                SelectFilter::make('model')
                    ->label('Model')
                    ->options(fn (): array => collect(PermissionHelper::getModels())
                        ->mapWithKeys(fn (string $model): array => [$model => PermissionHelper::getModelLabel($model)])
                        ->toArray())
                    ->query(function ($query, array $data): void {
                        if (empty($data['value'])) {
                            return;
                        }

                        $modelSlugs = is_array($data['value']) ? $data['value'] : [$data['value']];

                        $query->where(function ($q) use ($modelSlugs): void {
                            foreach ($modelSlugs as $slug) {
                                $q->orWhere('name', 'like', "%_{$slug}");
                            }
                        });
                    })
                    ->multiple(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('assignToRole')
                        ->label('Assign to Role')
                        ->icon('heroicon-m-user-group')
                        ->form([
                            Select::make('role_id')
                                ->label('Role')
                                ->options(fn (): array => Role::pluck('name', 'id')->toArray())
                                ->searchable()
                                ->required(),
                        ])
                        ->action(function ($records, array $data): void {
                            $role = Role::findOrFail($data['role_id']);
                            $role->givePermissionTo($records->pluck('name')->toArray());
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }
}
