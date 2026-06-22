<?php

namespace App\Filament\Resources\Permissions\Schemas;

use App\Models\Role;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

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
                    ->regex('/^[a-z_]+$/'),
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
            $group = $role->department?->name ?? 'General';
            $label = $role->department
                ? "{$role->name} ({$role->department->name})"
                : $role->name;
            $grouped[$group][$role->id] = $label;
        }

        // Sort groups: General first, then alphabetical
        ksort($grouped, SORT_NATURAL);
        if (isset($grouped['General'])) {
            $general = $grouped['General'];
            unset($grouped['General']);
            $grouped = ['General' => $general] + $grouped;
        }

        return $grouped;
    }
}
