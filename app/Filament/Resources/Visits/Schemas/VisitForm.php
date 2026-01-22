<?php

namespace App\Filament\Resources\Visits\Schemas;

use App\Models\Contact;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class VisitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Visit Logistics')
                    ->columns(2)
                    ->schema([
                        Select::make('customer_id')
                            ->relationship('customer', 'facility_name')
                            ->required()
                            ->searchable()
                            ->preload()
                            // ->helperText('If the search result is empty, click the + button to create a new one.')
                            // ->createOptionForm([
                            //     TextInput::make('facility_name')
                            //         ->required(),
                            //     Select::make('facility_type')
                            //         ->options([
                            //             'hospital' => 'Hospital',
                            //             'clinic' => 'Clinic',
                            //             'pharmacy' => 'Pharmacy',
                            //             'laboratory' => 'Laboratory',
                            //             'distributor' => 'Distributor',
                            //             'other' => 'Other',
                            //         ])
                            //         ->default('other')
                            //         ->required(),
                            // ])
                            ->live(),
                        Select::make('contact_id')
                            ->label('Contact Person')
                            ->relationship('contact', 'first_name', fn ($query, $get) => $query->where('customer_id', $get('customer_id')))
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->first_name} {$record->last_name}")
                            ->searchable(['first_name', 'last_name'])
                            ->preload(),
                            // ->helperText('If the search result is empty, click the + button to create a new one.')
                            // ->createOptionForm([
                            //     TextInput::make('first_name')
                            //         ->required(),
                            //     TextInput::make('last_name')
                            //         ->required(),
                            //     TextInput::make('position')
                            //         ->placeholder('e.g. Head of Procurement'),
                            // ])
                            // ->createOptionUsing(function (array $data, $get) {
                            //     $data['customer_id'] = $get('customer_id');

                            //     return Contact::create($data)->getKey();
                            // }),
                        Select::make('visit_type')
                            ->options([
                                'In-person' => 'In-person',
                                'Video Call' => 'Video Call',
                                'Phone Call' => 'Phone Call',
                                'Messaging' => 'Messaging',
                            ])
                            ->required()
                            ->columnSpanFull()
                            ->live(),
                        TextInput::make('location')
                            ->placeholder('e.g. Hospital Lobby, Cafe, Office')
                            ->visible(fn ($get) => $get('visit_type') === 'In-person')
                            ->columnSpanFull()
                            ->maxLength(255),
                        // TextInput::make('meeting_link')
                        //     ->label('Meeting Link')
                        //     ->url()
                        //     ->columnSpanFull()
                        //     ->placeholder('https://zoom.us/j/...')
                        //     ->visible(fn ($get) => $get('visit_type') === 'Video Call')
                        //     ->required(),
                        TextInput::make('messaging_platform')
                            ->label('Messaging Platform')
                            ->placeholder('e.g. WhatsApp, Telegram, Slack')
                            ->visible(fn ($get) => $get('visit_type') === 'Messaging')
                            ->columnSpanFull()
                            ->required(),
                        DateTimePicker::make('visit_started_at')
                            ->label('Start Visit')
                            ->required()
                            ->default(now()),
                        DateTimePicker::make('visit_ended_at')
                            ->label('End Visit')
                            ->after('visit_started_at'),
                    ]),

                Section::make('Strategic Intent (Pre-Visit)')
                    ->columns(1)
                    ->schema([
                        TextInput::make('purpose')
                            ->label('Why are you visiting?')
                            ->placeholder('e.g. Introduction of new CT Scanner')
                            ->required(),
                        Textarea::make('expectations')
                            ->label('What are your expectations?')
                            ->placeholder('e.g. Stakeholder will agree to a technical demo')
                            ->required(),
                        Textarea::make('targets')
                            ->label('Specific targets based on expectations')
                            ->placeholder('e.g. Get the contact details of the Head of Radiology')
                            ->required(),
                    ]),

                Section::make('Visit Outcome (Post-Visit)')
                    ->columns(1)
                    ->schema([
                        Textarea::make('summary_notes')
                            ->label('Actual Result / Summary')
                            ->rows(3),
                        Slider::make('confidence_level')
                            ->label('Confidence Level')
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(5)
                            ->default(50)
                            ->tooltips(),
                    ]),

                Section::make('Stakeholder Review')
                    ->columns(1)
                    ->schema([
                        Textarea::make('stakeholder_feedback')
                            ->label('Manager/Stakeholder Feedback'),
                        Toggle::make('is_worth_keeping')
                            ->label('Is this customer worth keeping?')
                            ->onIcon('heroicon-m-check-circle')
                            ->offIcon('heroicon-m-x-circle')
                            ->inline(false),
                    ])
                    ->visible(fn () => Auth::user()?->hasAnyRole(['Super Admin', 'Board of Director'])), // Only stakeholders can see/edit this
            ]);
    }
}
