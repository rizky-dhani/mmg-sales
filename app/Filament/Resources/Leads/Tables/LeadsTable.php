<?php

namespace App\Filament\Resources\Leads\Tables;

use App\Models\Lead;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('contact_person')
                    ->label('Contact Person')
                    ->searchable(),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'new' => 'gray',
                        'contacted' => 'info',
                        'qualified' => 'primary',
                        'proposal' => 'warning',
                        'negotiation' => 'warning',
                        'won' => 'success',
                        'lost' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('priority')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'low' => 'gray',
                        'medium' => 'info',
                        'high' => 'warning',
                        'urgent' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('estimated_value')
                    ->label('Estimated Value')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('source')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => str_replace('_', ' ', ucfirst($state)))
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('assignedUser.name')
                    ->label('Assigned To')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('company.facility_name')
                    ->label('Linked Company')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('latestActivity.performed_at')
                    ->label('Last Contact')
                    ->date('d M Y')
                    ->description(fn (Lead $record): ?string => $record->latestActivity?->subject)
                    ->formatStateUsing(fn ($state) => $state ? strtoupper(\Carbon\Carbon::parse($state)->translatedFormat('d M Y')) : '-')
                    ->sortable(),

                TextColumn::make('converted_at')
                    ->label('Converted')
                    ->date('d M Y')
                    ->formatStateUsing(fn ($state) => $state ? strtoupper(\Carbon\Carbon::parse($state)->translatedFormat('d M Y')) : '-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
