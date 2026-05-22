<?php

namespace App\Filament\Resources\Activities\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;

class ActivityInfolist
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
                                TextEntry::make('project.project_code')
                                    ->label('Project Code'),
                                TextEntry::make('customer.name')
                                    ->label('Customer')
                                    ->weight('bold'),
                                TextEntry::make('contact.name')
                                    ->label('Contact Person'),
                                TextEntry::make('type')
                                    ->badge()
                                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                                TextEntry::make('subject')
                                    ->weight('bold'),
                                TextEntry::make('user.name')
                                    ->label('Sales Rep'),
                                TextEntry::make('performed_at')
                                    ->label('Date & Time')
                                    ->dateTime('d M Y H:i')
                                    ->formatStateUsing(fn ($state) => $state ? strtoupper(Carbon::parse($state)->translatedFormat('d M Y H:i')) : '-'),
                            ]),

                        Section::make('Interaction Details')
                            ->columns(4)
                            ->schema([
                                TextEntry::make('location')
                                    ->placeholder('-'),
                                TextEntry::make('meeting_link')
                                    ->label('Meeting Link')
                                    ->url(fn ($record) => $record->meeting_link)
                                    ->openUrlInNewTab()
                                    ->placeholder('-'),
                                TextEntry::make('messaging_platform')
                                    ->placeholder('-'),
                                TextEntry::make('outcome')
                                    ->badge()
                                    ->color(fn ($state): string => match ($state) {
                                        'Interested' => 'success',
                                        'Not Interested' => 'danger',
                                        'No Answer' => 'warning',
                                        'Need more info' => 'info',
                                        default => 'gray',
                                    }),
                                TextEntry::make('visit_started_at')
                                    ->label('Started At')
                                    ->dateTime('H:i'),
                                TextEntry::make('visit_ended_at')
                                    ->label('Ended At')
                                    ->dateTime('H:i'),
                                TextEntry::make('duration_minutes')
                                    ->label('Duration')
                                    ->suffix(' min'),
                            ]),

                        Section::make('Follow-up')
                            ->columns(3)
                            ->schema([
                                TextEntry::make('next_contact_date')
                                    ->label('Next Contact Date')
                                    ->date('d M Y'),
                                TextEntry::make('is_worth_keeping')
                                    ->label('Worth Keeping')
                                    ->formatStateUsing(fn ($state) => $state ? 'Yes' : 'No')
                                    ->badge()
                                    ->color(fn ($state) => $state ? 'success' : 'danger'),
                                TextEntry::make('follow_up_notes')
                                    ->label('Follow-up Notes')
                                    ->columnSpanFull(),
                            ]),

                        Section::make('Notes & Feedback')
                            ->columns(4)
                            ->schema([
                                TextEntry::make('description')
                                    ->label('Summary')
                                    ->markdown()
                                    ->columnSpanFull(),

                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('purpose'),
                                        TextEntry::make('expectations'),
                                        TextEntry::make('targets'),
                                        TextEntry::make('stakeholder_feedback')
                                            ->label('Feedback'),
                                    ]),
                            ]),
                    ]),
            ]);
    }
}
