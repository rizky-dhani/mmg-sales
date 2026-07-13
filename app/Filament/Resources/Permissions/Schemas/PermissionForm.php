<?php

namespace App\Filament\Resources\Permissions\Schemas;

use App\Helpers\PermissionHelper;
use App\Models\Role;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\File;

class PermissionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->regex('/^[a-z_]+$/')
                    ->helperText(function ($get): ?string {
                        $name = $get('name');
                        if (blank($name)) {
                            return null;
                        }

                        $parsed = PermissionHelper::parsePermissionName($name);
                        if ($parsed === null) {
                            return str($name)->replace('_', ' ')->title();
                        }

                        return PermissionHelper::getActionLabel($parsed['action'])
                            .' '.PermissionHelper::getModelLabel($parsed['model']);
                    }),
                Select::make('model')
                    ->label('Model')
                    ->options(collect(File::files(app_path('Models')))
                        ->mapWithKeys(fn (\SplFileInfo $file) => [
                            str($file->getFilename())->before('.php')->snake()->toString() => str($file->getFilename())->before('.php')->headline()->toString(),
                        ])
                        ->sort()
                        ->toArray())
                    ->searchable()
                    ->nullable()
                    ->dehydrated(false),
                Select::make('guard_name')
                    ->options([
                        'web' => 'Web',
                        'api' => 'API',
                    ])
                    ->default('web')
                    ->required(),
                Select::make('roles')
                    ->label('Assign to Roles')
                    ->relationship('roles', 'name')
                    ->options(fn (): array => static::getRoleOptions())
                    ->multiple()
                    ->searchable()
                    ->preload(),
            ]);
    }

    /**
     * Get role options grouped by department.
     */
    private static function getRoleOptions(): array
    {
        $roles = Role::with('department')->orderBy('name')->get();

        $grouped = [];
        foreach ($roles as $role) {
            $group = $role->department?->name ?? 'Global';
            $grouped[$group][$role->id] = $role->name;
        }

        ksort($grouped, SORT_NATURAL);
        if (isset($grouped['Global'])) {
            $global = $grouped['Global'];
            unset($grouped['Global']);
            $grouped = ['Global' => $global] + $grouped;
        }

        return $grouped;
    }
}
