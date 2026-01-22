<?php

namespace App\Filament\Resources\Visits\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class VisitInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Visit Logistics')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('customer.facility_name')
                            ->label('Customer')
                            ->weight('bold')
                            ->url(fn ($record) => $record->customer_id ? "/admin/companies/{$record->customer_id}" : null),
                        TextEntry::make('contact')
                            ->label('Contact Person')
                            ->formatStateUsing(fn ($record) => $record->contact ? "{$record->contact->first_name} {$record->contact->last_name}" : '-'),
                        TextEntry::make('visit_started_at')
                            ->label('Start Visit')
                            ->dateTime('d M Y H:i'),
                        TextEntry::make('visit_ended_at')
                            ->label('End Visit')
                            ->dateTime('d M Y H:i'),
                        TextEntry::make('visit_type')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'In-person' => 'success',
                                'Video Call' => 'info',
                                'Phone Call' => 'warning',
                                'Messaging' => 'gray',
                                default => 'gray',
                            }),
                        TextEntry::make('meeting_link')
                            ->label('Meeting Link')
                            ->url(fn ($record) => $record->meeting_link)
                            ->visible(fn ($record) => $record->visit_type === 'Video Call'),
                        TextEntry::make('messaging_platform')
                            ->label('Messaging Platform')
                            ->visible(fn ($record) => $record->visit_type === 'Messaging'),
                        TextEntry::make('location')
                            ->placeholder('-'),
                        TextEntry::make('user.name')
                            ->label('Sales Representative'),
                    ]),

                Section::make('Strategic Intent (Pre-Visit)')
                    ->columns(1)
                    ->schema([
                        TextEntry::make('purpose')
                            ->label('Purpose of Visit'),
                        TextEntry::make('expectations')
                            ->label('Expectations'),
                        TextEntry::make('targets')
                            ->label('Specific Targets'),
                    ]),

                Section::make('Visit Outcome (Post-Visit)')
                    ->columns(1)
                    ->schema([
                        TextEntry::make('summary_notes')
                            ->label('Actual Result / Summary')
                            ->placeholder('No summary notes provided.'),
                        TextEntry::make('confidence_level')
                            ->label('Confidence Level')
                            ->numeric()
                            ->suffix('%'),
                    ]),

                Section::make('Stakeholder Review')
                    ->columns(1)
                    ->schema([
                        TextEntry::make('stakeholder_feedback')
                            ->label('Manager/Stakeholder Feedback')
                            ->placeholder('No feedback yet.'),
                        IconEntry::make('is_worth_keeping')
                            ->label('Is this customer worth keeping?')
                            ->boolean(),
                    ])
                    ->visible(fn () => Auth::user()?->hasAnyRole(['Super Admin', 'Board of Director'])),
            ]);
    }
}
