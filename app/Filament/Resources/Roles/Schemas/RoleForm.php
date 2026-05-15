<?php

namespace App\Filament\Resources\Roles\Schemas;

use App\Helpers\PermissionHelper;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rule;

class RoleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true, modifyRuleUsing: function (Rule $rule, $get) {
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
                Select::make('department_id')
                    ->relationship('department', 'name')
                    ->nullable()
                    ->default(null)
                    ->placeholder('Global (all departments)')
                    ->preload()
                    ->helperText('Leave empty for global roles. Scoped roles only grant permissions to users in the selected department.'),
                CheckboxList::make('permissions')
                    ->relationship('permissions', 'name')
                    ->options(fn () => PermissionHelper::getGroupedOptions())
                    ->columns(3)
                    ->gridDirection('row')
                    ->bulkToggleable(),
            ]);
    }
}
