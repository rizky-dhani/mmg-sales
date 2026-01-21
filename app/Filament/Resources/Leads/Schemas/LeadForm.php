<?php

namespace App\Filament\Resources\Leads\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Lead Details')
                            ->columnSpanFull()
                            ->columns(2)
                            ->schema([
                                TextInput::make('title')
                                    ->label('Lead Title')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('customer_name')
                                    ->label('Customer Name')
                                    ->required()
                                    ->maxLength(255),
                                TextInput::make('contact_person')
                                    ->label('Contact Person')
                                    ->required(),
                            ]),

                        Section::make('Assignment Hierarchy')
                            ->columnSpanFull()
                            ->columns(3)
                            ->schema([
                                Select::make('assigned_to')
                                    ->label('Assigned Sales Rep')
                                    ->relationship('assignedUser', 'name')
                                    ->default(auth()->id())
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                // Leads don't have explicit hierarchy fields in DB,
                                // but we can show the context of the assigned rep
                                TextInput::make('rep_department')
                                    ->label('Department')
                                    ->placeholder(auth()->user()?->department?->name ?? '-')
                                    ->readOnly(),
                                TextInput::make('rep_position')
                                    ->label('Position')
                                    ->placeholder(auth()->user()?->position?->name ?? '-')
                                    ->readOnly(),
                            ]),
                    ]),
                Grid::make(2)
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Contact Information')
                            ->columnSpanFull()
                            ->columns(3)
                            ->schema([
                                TextInput::make('contact_person')
                                    ->label('Contact Person')
                                    ->required(),
                                TextInput::make('email')
                                    ->label('Email Address')
                                    ->email()
                                    ->required(),
                                TextInput::make('phone')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->required(),
                            ]),

                        Section::make('Pipeline & Status')
                            ->columnSpanFull()
                            ->columns(4)
                            ->schema([
                                Select::make('status')
                                    ->options([
                                        'new' => 'New',
                                        'contacted' => 'Contacted',
                                        'qualified' => 'Qualified',
                                        'proposal' => 'Proposal',
                                        'negotiation' => 'Negotiation',
                                        'won' => 'Won',
                                        'lost' => 'Lost',
                                    ])
                                    ->default('new')
                                    ->required()
                                    ->searchable(),
                                Select::make('source')
                                    ->options([
                                        'website' => 'Website',
                                        'referral' => 'Referral',
                                        'cold_call' => 'Cold call',
                                        'trade_show' => 'Trade show',
                                        'partner' => 'Partner',
                                        'other' => 'Other',
                                    ])
                                    ->default('other')
                                    ->required()
                                    ->searchable(),
                                Select::make('priority')
                                    ->options([
                                        'low' => 'Low',
                                        'medium' => 'Medium',
                                        'high' => 'High',
                                        'urgent' => 'Urgent',
                                    ])
                                    ->default('medium')
                                    ->required()
                                    ->searchable(),
                                TextInput::make('estimated_value')
                                    ->label('Estimated Value')
                                    ->numeric()
                                    ->prefix('IDR'),
                            ]),
                    ]),
            ]);
    }
}
