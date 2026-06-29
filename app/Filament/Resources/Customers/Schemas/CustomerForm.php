<?php

namespace App\Filament\Resources\Customers\Schemas;

use App\Models\SubSegment;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CustomerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Customer Information')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextInput::make('customer_acc_code')
                            ->label('Internal Code')
                            ->default(null)
                            ->columnSpanFull(),
                        TextInput::make('name')
                            ->required()
                            ->columnSpanFull(),
                        Select::make('type')
                            ->options([
                                'hospital_clinic' => 'Hospital/Apothecary/Clinic',
                                'pt_cv' => 'PT/CV',
                                'other' => 'Other',
                            ])
                            ->default('other')
                            ->required(),
                        TextInput::make('tax_number')
                            ->default(null),
                    ]),

                Grid::make(2)
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Contact Information')
                            ->schema([
                                TextInput::make('email')
                                    ->label('Email address')
                                    ->email()
                                    ->default(null),
                                TextInput::make('phone')
                                    ->tel()
                                    ->default(null),
                                TextInput::make('website')
                                    ->url()
                                    ->default(null),
                            ]),

                        Section::make('Address')
                            ->columns(2)
                            ->schema([
                                TextInput::make('address')
                                    ->default(null)
                                    ->columnSpanFull(),
                                TextInput::make('city')
                                    ->default(null),
                                TextInput::make('state')
                                    ->default(null),
                                TextInput::make('postal_code')
                                    ->default(null),
                                TextInput::make('country')
                                    ->required()
                                    ->default('Indonesia'),
                            ]),
                    ]),

                Section::make('Classification')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Select::make('cd_ncd_type')
                                    ->label('CD / NCD Type')
                                    ->options([
                                        'CD' => 'CD',
                                        'N-CD' => 'N-CD (Life Science)',
                                    ])
                                    ->live(),
                                Select::make('segment_id')
                                    ->label('Segment')
                                    ->relationship('segment', 'name')
                                    ->live()
                                    ->preload()
                                    ->searchable(),
                                Select::make('sub_segment_id')
                                    ->label('Sub Segment')
                                    ->options(fn ($get) => SubSegment::query()
                                        ->where('segment_id', $get('segment_id'))
                                        ->pluck('name', 'id'))
                                    ->preload()
                                    ->searchable(),
                            ]),
                    ]),

                Section::make('Settings')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('status')
                            ->options([
                                'active' => 'Active',
                                'inactive' => 'Inactive',
                            ])
                            ->default('active')
                            ->required(),
                        TextInput::make('max_contact_persons')
                            ->label('Max Contact Persons')
                            ->numeric()
                            ->minValue(1)
                            ->default(null)
                            ->helperText('Leave empty for unlimited'),
                        TextInput::make('payment_terms_days')
                            ->label('Payment Terms (Days)')
                            ->numeric()
                            ->default(30),
                        Toggle::make('is_active')
                            ->default(true),
                    ]),
            ]);
    }
}
