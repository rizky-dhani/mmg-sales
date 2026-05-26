<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Models\Contact;
use App\Models\Principal;
use App\Models\Product;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
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
                Grid::make(2)
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Project Details')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Project Title')
                                    ->required()
                                    ->maxLength(255),
                                Select::make('customer_id')
                                    ->label('Customer Name')
                                    ->relationship('customer', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn (callable $set) => $set('contact_person', null)),
                                Select::make('contact_person')
                                    ->label('Contact Person')
                                    ->options(fn ($get) => Contact::where('customer_id', $get('customer_id'))->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn ($get) => $get('customer_id')),
                                Select::make('assigned_users')
                                    ->label('Assign Users')
                                    ->multiple()
                                    ->options(User::pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        if ($record) {
                                            $component->state($record->collaborators->pluck('id')->toArray());
                                        }
                                    }),
                            ]),

                        Section::make('Contact Information')
                            ->schema([
                                TextInput::make('email')
                                    ->label('Email Address')
                                    ->email(),
                                TextInput::make('phone')
                                    ->label('Phone Number')
                                    ->tel()
                                    ->required(),
                            ]),
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
                                            ->options(Principal::pluck('name', 'id'))
                                            ->required()
                                            ->live()
                                            ->searchable(),
                                        Select::make('product_ids')
                                            ->label('Products')
                                            ->multiple()
                                            ->options(fn ($get) => $get('principal_id')
                                                ? Product::where('principal_id', $get('principal_id'))->pluck('name', 'id')
                                                : [])
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
                            ->columns(2)
                            ->schema([
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
