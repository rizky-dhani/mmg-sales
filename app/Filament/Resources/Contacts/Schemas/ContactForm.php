<?php

namespace App\Filament\Resources\Contacts\Schemas;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Customer')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Select::make('customer_id')
                            ->relationship('customer', 'name')
                            ->query(fn ($query) => $query->where('status', 'active'))
                            ->required()
                            ->preload()
                            ->searchable()
                            ->columnSpanFull(),
                        Toggle::make('is_primary')
                            ->required(),
                        Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                            ])
                            ->default('active')
                            ->required(),
                    ]),

                Grid::make(2)
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Personal Information')
                            ->columns(2)
                            ->schema([
                                TextInput::make('name')
                                    ->required(),
                                TextInput::make('position')
                                    ->default(null),
                                TextInput::make('department')
                                    ->default(null),
                                TextInput::make('email')
                                    ->label('Email address')
                                    ->email()
                                    ->default(null)
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Phone Numbers')
                            ->schema([
                                Repeater::make('phones')
                                    ->relationship()
                                    ->schema([
                                        Select::make('type')
                                            ->options([
                                                'work' => 'Work',
                                                'mobile' => 'Mobile',
                                                'home' => 'Home',
                                                'fax' => 'Fax',
                                                'other' => 'Other',
                                            ])
                                            ->default('mobile')
                                            ->required(),
                                        TextInput::make('number')
                                            ->tel()
                                            ->required(),
                                    ])
                                    ->columns(2)
                                    ->reorderable()
                                    ->defaultItems(1),
                            ]),
                    ]),

                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
