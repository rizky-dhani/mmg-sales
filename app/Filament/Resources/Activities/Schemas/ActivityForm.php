<?php

namespace App\Filament\Resources\Activities\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ActivityForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('lead_id')
                    ->relationship('lead', 'company_name')
                    ->searchable()
                    ->required(),

                Select::make('user_id')
                    ->label('Sales Rep')
                    ->relationship('user', 'name')
                    ->default(auth()->id())
                    ->required(),

                Select::make('type')
                    ->options([
                        'call' => 'Call',
                        'email' => 'Email',
                        'meeting' => 'Meeting',
                        'demo' => 'Demo',
                        'presentation' => 'Presentation',
                    ])
                    ->required(),

                TextInput::make('subject')
                    ->required()
                    ->maxLength(255),

                DateTimePicker::make('performed_at')
                    ->required()
                    ->default(now()),

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

                Textarea::make('description')
                    ->columnSpanFull(),
            ]);
    }
}
