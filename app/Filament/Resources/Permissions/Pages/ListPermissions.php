<?php

namespace App\Filament\Resources\Permissions\Pages;

use App\Filament\Resources\Permissions\PermissionResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class ListPermissions extends ListRecords
{
    protected static string $resource = PermissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('generate')
                ->label('Generate Permissions')
                ->icon('heroicon-o-sparkles')
                ->form([
                    TextInput::make('model')
                        ->label('Model slug')
                        ->required()
                        ->regex('/^[a-z_]+$/')
                        ->helperText('E.g., customer, order, product'),
                    CheckboxList::make('actions')
                        ->options([
                            'view' => 'View',
                            'view_any' => 'View Any',
                            'create' => 'Create',
                            'update' => 'Update',
                            'delete' => 'Delete',
                            'restore' => 'Restore',
                            'force_delete' => 'Force Delete',
                        ])
                        ->required()
                        ->columns(2)
                        ->bulkToggleable(),
                ])
                ->action(function (array $data): void {
                    $model = $data['model'];
                    $actions = $data['actions'];

                    $generated = 0;
                    foreach ($actions as $action) {
                        Permission::findOrCreate("{$action}_{$model}");
                        $generated++;
                    }

                    app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

                    Notification::make()
                        ->success()
                        ->title("{$generated} permissions generated for '{$model}'")
                        ->send();
                }),
        ];
    }
}
