<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section as ComponentsSection;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ComponentsSection::make('Basic Information')
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

                ComponentsSection::make('Organization & Hierarchy')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
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
                            ->preload()
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
