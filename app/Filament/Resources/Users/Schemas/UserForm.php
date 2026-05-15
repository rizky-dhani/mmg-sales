<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Helpers\PermissionHelper;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required(),
                    ]),

                Section::make('Organization & Hierarchy')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Select::make('department_id')
                            ->relationship('department', 'name')
                            ->default(null)
                            ->preload()
                            ->live(),
                        Select::make('position_id')
                            ->relationship('position', 'name')
                            ->default(null)
                            ->preload(),
                        Select::make('territory_id')
                            ->relationship('territory', 'name')
                            ->default(null)
                            ->preload(),
                        Select::make('manager_id')
                            ->relationship('manager', 'name')
                            ->default(null)
                            ->preload(),

                        // Department-filtered role assignment
                        Select::make('roles')
                            ->relationship('roles', 'name', function ($query, $get) {
                                $departmentId = $get('department_id');
                                if ($departmentId) {
                                    $query->where(function ($q) use ($departmentId) {
                                        $q->whereNull('department_id')
                                            ->orWhere('department_id', $departmentId);
                                    });
                                }
                            })
                            ->multiple()
                            ->preload()
                            ->columnSpanFull()
                            ->helperText('Only global roles and roles matching the user\'s department are shown.'),

                        // Direct permission overrides
                        CheckboxList::make('permissions')
                            ->relationship('permissions', 'name')
                            ->options(fn () => PermissionHelper::getGroupedOptions())
                            ->columns(3)
                            ->gridDirection('row')
                            ->label('Direct Permissions')
                            ->helperText('Grant additional permissions beyond what roles provide.'),
                    ]),
            ]);
    }
}
