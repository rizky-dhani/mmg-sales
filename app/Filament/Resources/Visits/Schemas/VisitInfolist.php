<?php

namespace App\Filament\Resources\Visits\Schemas;

use Illuminate\Support\Facades\Auth;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;

class VisitInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Visit Logistics')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('company.facility_name')
                            ->label('Company')
                            ->weight('bold')
                            ->url(fn ($record) => $record->company_id ? "/admin/companies/{$record->company_id}" : null),
                        TextEntry::make('contact')
                            ->label('Contact Person')
                            ->formatStateUsing(fn ($record) => $record->contact ? "{$record->contact->first_name} {$record->contact->last_name}" : '-'),
                        TextEntry::make('visit_started_at')
                            ->label('Start Visit')
                            ->dateTime('d M Y H:i'),
                        TextEntry::make('visit_ended_at')
                            ->label('End Visit')
                            ->dateTime('d M Y H:i'),
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
                    ]),

                Section::make('Stakeholder Review')
                    ->columns(1)
                    ->schema([
                        TextEntry::make('stakeholder_feedback')
                            ->label('Manager/Stakeholder Feedback')
                            ->placeholder('No feedback yet.'),
                        IconEntry::make('is_worth_keeping')
                            ->label('Is this company worth keeping?')
                            ->boolean(),
                    ])
                    ->visible(fn () => Auth::user()?->hasAnyRole(['Super Admin', 'Board of Director'])),
            ]);
    }
}
