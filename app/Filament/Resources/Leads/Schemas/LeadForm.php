<?php

namespace App\Filament\Resources\Leads\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('company_name')
                    ->required(),
                TextInput::make('contact_person')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                TextInput::make('phone')
                    ->tel()
                    ->required(),
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
                    ->required(),
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
                    ->required(),
                Select::make('priority')
                    ->options(['low' => 'Low', 'medium' => 'Medium', 'high' => 'High', 'urgent' => 'Urgent'])
                    ->default('medium')
                    ->required(),
                TextInput::make('estimated_value')
                    ->numeric()
                    ->default(null),
                Textarea::make('notes')
                    ->default(null)
                    ->columnSpanFull(),
                Select::make('customer_id')
                    ->relationship('customer', 'facility_name')
                    ->default(null)
                    ->preload(),
                DateTimePicker::make('converted_at'),
                DateTimePicker::make('last_contacted_at'),
                TextInput::make('assigned_to')
                    ->numeric()
                    ->default(null),
            ]);
    }
}
