<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->required(),
                Select::make('department_id')
                    ->relationship('department', 'name')
                    ->default(null)
                    ->preload(),
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
                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),
            ]);
    }
}
