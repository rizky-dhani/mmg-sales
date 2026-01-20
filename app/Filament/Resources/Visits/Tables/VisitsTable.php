<?php

namespace App\Filament\Resources\Visits\Tables;

use App\Exports\VisitsExport;
use App\Exports\VisitsMultiSheetExport;
use App\Models\Visit;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class VisitsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('visit_started_at')
                    ->label('Date & Time')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('duration')
                    ->label('Duration')
                    ->getStateUsing(function ($record) {
                        if (! $record->visit_started_at || ! $record->visit_ended_at) {
                            return '-';
                        }

                        return $record->visit_started_at->diffForHumans($record->visit_ended_at, true);
                    }),
                TextColumn::make('user.name')
                    ->label('Sales Rep')
                    ->sortable(),
                TextColumn::make('company.facility_name')
                    ->label('Company')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('purpose')
                    ->limit(30)
                    ->searchable(),
                IconColumn::make('is_worth_keeping')
                    ->label('Worth it?')
                    ->boolean()
                    ->placeholder('Pending Review'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('company_id')
                    ->label('Company')
                    ->relationship('company', 'facility_name')
                    ->searchable()
                    ->preload(),
                Filter::make('visit_started_at')
                    ->form([
                        DatePicker::make('from')->label('Date from'),
                        DatePicker::make('until')->label('Date until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('visit_started_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('visit_started_at', '<=', $date),
                            );
                    }),
            ])
            ->headerActions([
                Action::make('export')
                    ->label('Export')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn ($livewire, $data) => static::performExport($livewire->getFilteredSortedTableQuery(), $data['type']))
                    ->form([
                        \Filament\Forms\Components\Select::make('type')
                            ->options([
                                'standard' => 'Standard (Single Sheet)',
                                'by_rep' => 'Grouped by Representative (Multiple Sheets)',
                                'by_company' => 'Grouped by Company (Multiple Sheets)',
                            ])
                            ->default('standard')
                            ->required(),
                    ]),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    BulkAction::make('export_selected')
                        ->label('Export Selected')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(fn ($records, $data) => static::performExport(Visit::whereIn('id', $records->pluck('id')), $data['type']))
                        ->form([
                            \Filament\Forms\Components\Select::make('type')
                                ->options([
                                    'standard' => 'Standard (Single Sheet)',
                                    'by_rep' => 'Grouped by Representative (Multiple Sheets)',
                                    'by_company' => 'Grouped by Company (Multiple Sheets)',
                                ])
                                ->default('standard')
                                ->required(),
                        ]),
                ]),
            ]);
    }

    protected static function performExport(Builder $query, string $type)
    {
        $filename = 'visits-export-'.now()->format('Y-m-d').'.xlsx';

        return match ($type) {
            'by_rep' => Excel::download(new VisitsMultiSheetExport($query, 'user'), $filename),
            'by_company' => Excel::download(new VisitsMultiSheetExport($query, 'company'), $filename),
            default => Excel::download(new VisitsExport($query), $filename),
        };
    }
}
