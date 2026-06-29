<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Helpers\PermissionHelper;
use App\Models\Department;
use App\Models\Position;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;
use Spatie\Permission\Models\Permission;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('department_id')
                    ->label('Department')
                    ->options(fn () => Department::pluck('name', 'id'))
                    ->nullable()
                    ->default(null)
                    ->placeholder('Global (all departments)')
                    ->preload()
                    ->helperText('Leave empty for global roles. Scoped roles only grant permissions to users in the selected department.'),
                Select::make('position_id')
                    ->label('Position')
                    ->options(fn () => Position::pluck('name', 'id'))
                    ->nullable()
                    ->default(null)
                    ->placeholder('Select a position')
                    ->preload()
                    ->searchable(),
                TextInput::make('name')
                    ->label('Role Name')
                    ->disabled()
                    ->dehydrated()
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true, modifyRuleUsing: function (Unique $rule, $get) {
                        $departmentId = $get('department_id');
                        if ($departmentId) {
                            return $rule->where('department_id', $departmentId);
                        }

                        return $rule->whereNull('department_id');
                    }),
                Select::make('guard_name')
                    ->options([
                        'web' => 'Web',
                        'api' => 'API',
                    ])
                    ->default('web')
                    ->required(),
                Select::make('permissions')
                    ->multiple()
                    ->relationship('permissions', 'name')
                    ->options(fn () => Permission::all()
                        ->groupBy(function (Permission $permission): string {
                            $parsed = PermissionHelper::parsePermissionName($permission->name);

                            return $parsed
                                ? PermissionHelper::getModelLabel($parsed['model'])
                                : 'Other';
                        })
                        ->mapWithKeys(fn ($group, string $groupName) => [
                            $groupName => $group->pluck('name', 'id'),
                        ])
                        ->toArray())
                    ->searchable()
                    ->preload()
                    ->columnSpanFull(),
            ]);
    }
}
