<?php

namespace App\Filament\Resources\Leads\Schemas;

use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class LeadInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Lead Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('company_name')
                            ->label('Company')
                            ->weight('bold'),
                        TextEntry::make('contact_person')
                            ->label('Contact Person'),
                        TextEntry::make('status')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => ucfirst($state))
                            ->color(fn (string $state): string => match ($state) {
                                'won' => 'success',
                                'lost' => 'danger',
                                'new' => 'gray',
                                default => 'info',
                            }),
                        TextEntry::make('priority')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                        TextEntry::make('email')
                            ->copyable(),
                        TextEntry::make('phone')
                            ->copyable(),
                        TextEntry::make('estimated_value')
                            ->money('IDR'),
                        TextEntry::make('assignedUser.name')
                            ->label('Sales Rep'),
                    ]),

                Section::make('Activities History')
                    ->schema([
                        RepeatableEntry::make('activities')
                            ->label('')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('performed_at')
                                            ->label('Date')
                                            ->dateTime('d M Y H:i'),
                                        TextEntry::make('type')
                                            ->badge()
                                            ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                                        TextEntry::make('outcome')
                                            ->badge()
                                            ->color('success'),
                                    ]),
                                TextEntry::make('subject')
                                    ->weight('bold'),
                                TextEntry::make('description')
                                    ->markdown(),
                            ])
                            // Sorting logic typically happens at the relationship level
                            // or via getEloquentQuery in the Page.
                            ->columns(1),
                    ]),
            ]);
    }
}
