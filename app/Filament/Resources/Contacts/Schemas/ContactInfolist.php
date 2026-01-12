<?php

namespace App\Filament\Resources\Contacts\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ContactInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contact Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('company.facility_name')
                            ->label('Facility')
                            ->weight('bold'),
                        TextEntry::make('full_name')
                            ->label('Name')
                            ->getStateUsing(fn ($record) => $record->first_name.' '.$record->last_name),
                        TextEntry::make('position'),
                        TextEntry::make('department'),
                        TextEntry::make('email')
                            ->copyable(),
                        TextEntry::make('phone'),
                        TextEntry::make('mobile'),
                        IconEntry::make('is_primary')
                            ->label('Primary Contact')
                            ->boolean(),
                        TextEntry::make('notes')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
