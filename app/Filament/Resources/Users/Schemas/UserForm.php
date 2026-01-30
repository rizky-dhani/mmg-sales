<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required(),
                        DateTimePicker::make('email_verified_at'),
                        TextInput::make('password')
                            ->password()
                            ->required()
                            ->hiddenOn('edit'),
                    ]),

                Section::make('Organization & Hierarchy')
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

                Section::make('Sales Targets')
                    ->columns(2)
                    ->schema([
                        TextInput::make('sales_target')
                            ->label('Annual Sales Target')
                            ->numeric()
                            ->prefix('IDR')
                            ->default(0),
                        KeyValue::make('target_metadata')
                            ->label('Monthly Splits / Metadata')
                            ->keyLabel('Month/Category')
                            ->valueLabel('Target Value')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
