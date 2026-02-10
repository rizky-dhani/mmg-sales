<?php

namespace App\Filament\Resources\Projects\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Project Details')
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextInput::make('title')
                            ->label('Project Title')
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
                Grid::make(2)
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Contact Information')
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
                        // Section::make('Assignment Hierarchy')
                        //     ->columnSpanFull()
                        //     ->columns(3)
                        //     ->schema([
                        //         Select::make('assigned_to')
                        //             ->label('Assigned Sales Rep')
                        //             ->relationship('assignedUser', 'name')
                        //             ->default(auth()->id())
                        //             ->required()
                        //             ->searchable()
                        //             ->preload(),
                        //         // Projects don't have explicit hierarchy fields in DB,
                        //         // but we can show the context of the assigned rep
                        //         TextInput::make('rep_department')
                        //             ->label('Department')
                        //             ->placeholder(auth()->user()?->department?->name ?? '-')
                        //             ->readOnly(),
                        //         TextInput::make('rep_position')
                        //             ->label('Position')
                        //             ->placeholder(auth()->user()?->position?->name ?? '-')
                        //             ->readOnly(),
                        //     ]),
                    ]),
                Grid::make(2)
                    ->columnSpanFull()
                    ->schema([

                        Section::make('Principals & Products')
                            ->columnSpanFull()
                            ->schema([
                                Repeater::make('supplier_products')
                                    ->label('Principals & Products')
                                    ->schema([
                                        Select::make('principal_id')
                                            ->label('Principal')
                                            ->options(\App\Models\Principal::pluck('name', 'id'))
                                            ->required()
                                            ->live()
                                            ->searchable(),
                                        Select::make('product_ids')
                                            ->label('Products')
                                            ->multiple()
                                            ->options(fn ($get) => \App\Models\Product::where('principal_id', $get('principal_id'))->pluck('name', 'id'))
                                            ->required()
                                            ->searchable(),
                                    ])
                                    ->columns(2)
                                    ->afterStateHydrated(function (Repeater $component, $record) {
                                        if (! $record) {
                                            return;
                                        }

                                        $products = $record->products()->with('principal')->get();
                                        $grouped = $products->groupBy('principal_id');

                                        $state = [];
                                        foreach ($grouped as $principalId => $items) {
                                            $state[] = [
                                                'principal_id' => $principalId,
                                                'product_ids' => $items->pluck('id')->toArray(),
                                            ];
                                        }

                                        $component->state($state);
                                    })
                                    ->dehydrated(false) // Handle saving via form submit
                                    ->saveRelationshipsUsing(function ($record, $state) {
                                        $productIds = [];
                                        foreach ($state as $item) {
                                            if (isset($item['product_ids'])) {
                                                $productIds = array_merge($productIds, (array) $item['product_ids']);
                                            }
                                        }
                                        $record->products()->sync($productIds);
                                    }),
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
                        Section::make('Estimation')
                            ->columnSpanFull()
                            ->columns(2)
                            ->schema([
                                TextInput::make('estimated_revenue')
                                    ->label('Expected Revenue')
                                    ->numeric()
                                    ->prefix('IDR')
                                    ->helperText('The specific revenue expected from this project.'),
                                DatePicker::make('estimated_completion_date')
                                    ->label('Estimated Completion Date')
                                    ->helperText('When the project is expected to be fully finished/delivered.'),
                            ]),
                    ]),
            ]);
    }
}
