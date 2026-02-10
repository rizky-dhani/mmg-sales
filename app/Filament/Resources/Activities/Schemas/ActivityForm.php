<?php

namespace App\Filament\Resources\Activities\Schemas;

use App\Models\Contact;
use App\Models\Project;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ActivityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)
                    ->columnSpanFull()
                    ->schema([
                        Section::make('Core Information')
                            ->columns(4)
                            ->schema([
                                Select::make('type')
                                    ->options([
                                        'Call' => 'Call',
                                        'Email' => 'Email',
                                        'Messaging' => 'Messaging (WA/etc)',
                                        'Online Meeting' => 'Online Meeting',
                                        'In-person Meeting' => 'In-person Meeting',
                                        'Demo' => 'Product Demo',
                                        'Presentation' => 'Presentation',
                                        'Administrative' => 'Administrative',
                                    ])
                                    ->required()
                                    ->live(),

                                TextInput::make('subject')
                                    ->required()
                                    ->maxLength(255),

                                Select::make('user_id')
                                    ->label('Sales Rep')
                                    ->relationship('user', 'name')
                                    ->default(auth()->id())
                                    ->required()
                                    ->preload()
                                    ->searchable(),

                                DateTimePicker::make('performed_at')
                                    ->label('Date & Time')
                                    ->required()
                                    ->default(now()),
                            ]),

                        Section::make('Customer Context')
                            ->columns(3)
                            ->schema([
                                Select::make('customer_id')
                                    ->relationship('customer', 'facility_name')
                                    ->searchable()
                                    ->preload()
                                    ->live(),

                                Select::make('contact_id')
                                    ->label('Contact Person')
                                    ->options(fn ($get) => Contact::where('customer_id', $get('customer_id'))->get()->pluck('name', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn ($get) => $get('customer_id')),

                                Select::make('project_id')
                                    ->label('Project')
                                    ->options(fn ($get) => Project::where('customer_id', $get('customer_id'))->pluck('title', 'id'))
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn ($get) => $get('customer_id')),
                            ]),

                        Section::make('Interaction Details')
                            ->columns(2)
                            ->visible(fn ($get) => in_array($get('type'), ['Online Meeting', 'In-person Meeting', 'Demo', 'Presentation']))
                            ->schema([
                                TextInput::make('location')
                                    ->maxLength(255)
                                    ->visible(fn ($get) => $get('type') === 'In-person Meeting'),

                                TextInput::make('meeting_link')
                                    ->url()
                                    ->visible(fn ($get) => $get('type') === 'Online Meeting'),

                                Select::make('messaging_platform')
                                    ->options([
                                        'WhatsApp' => 'WhatsApp',
                                        'Telegram' => 'Telegram',
                                        'Other' => 'Other',
                                    ])
                                    ->visible(fn ($get) => $get('type') === 'Messaging'),

                                DateTimePicker::make('visit_started_at')
                                    ->label('Started At'),

                                DateTimePicker::make('visit_ended_at')
                                    ->label('Ended At'),

                                TextInput::make('duration_minutes')
                                    ->numeric()
                                    ->suffix('minutes'),

                                Select::make('outcome')
                                    ->options([
                                        'Interested' => 'Interested',
                                        'No Answer' => 'No Answer',
                                        'Postponed' => 'Postponed',
                                        'Need more info' => 'Need more info',
                                        'Not Interested' => 'Not Interested',
                                    ]),
                            ]),

                        Section::make('Notes & Feedback')
                            ->columnSpanFull()
                            ->schema([
                                Textarea::make('description')
                                    ->label('Summary Notes')
                                    ->rows(3)
                                    ->columnSpanFull(),

                                Grid::make(2)
                                    ->visible(fn ($get) => in_array($get('type'), ['Online Meeting', 'In-person Meeting', 'Demo', 'Presentation']))
                                    ->schema([
                                        Textarea::make('purpose')->rows(2),
                                        Textarea::make('expectations')->rows(2),
                                        Textarea::make('targets')->rows(2),
                                        Textarea::make('stakeholder_feedback')->rows(2),
                                    ]),
                            ]),

                        Section::make('Follow-up & Planning')
                            ->columns(2)
                            ->schema([
                                Toggle::make('is_worth_keeping')
                                    ->label('Is this lead/project worth keeping?')
                                    ->default(true),

                                DatePicker::make('next_contact_date')
                                    ->label('Next Contact Date'),

                                Textarea::make('follow_up_notes')
                                    ->columnSpanFull()
                                    ->rows(2),
                            ]),
                    ]),
            ]);
    }
}
