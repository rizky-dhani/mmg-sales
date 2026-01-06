<?php

namespace App\Filament\Resources\Activities\Schemas;

use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ActivityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Activity Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('lead.company_name')
                            ->label('Lead Company')
                            ->weight('bold'),
                        TextEntry::make('user.name')
                            ->label('Sales Rep'),
                        TextEntry::make('type')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                        TextEntry::make('subject')
                            ->weight('bold'),
                        TextEntry::make('performed_at')
                            ->label('Performed At')
                            ->dateTime('d M Y H:i')
                            ->formatStateUsing(fn ($state) => strtoupper(\Carbon\Carbon::parse($state)->translatedFormat('d M Y H:i'))),
                        TextEntry::make('duration_minutes')
                            ->label('Duration')
                            ->suffix(' minutes'),
                        TextEntry::make('outcome')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Interested' => 'success',
                                'Not Interested' => 'danger',
                                'No Answer' => 'warning',
                                default => 'gray',
                            }),
                        TextEntry::make('description')
                            ->columnSpanFull()
                            ->markdown(),
                    ]),
            ]);
    }
}
