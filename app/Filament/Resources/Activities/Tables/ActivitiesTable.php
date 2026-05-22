<?php

namespace App\Filament\Resources\Activities\Tables;

use App\Exports\ActivitiesExport;
use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Traits\HasVisibilityScope;
use App\Models\Activity;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class ActivitiesTable
{
    use HasVisibilityScope;

    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return self::applyVisibilityScope($query, 'user_id');
            })
            ->columns([
                TextColumn::make('activity_code')
                    ->label('Activity Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('performed_at')
                    ->label('Date')
                    ->date('d M Y')
                    ->formatStateUsing(fn ($state) => strtoupper(Carbon::parse($state)->translatedFormat('d M Y')))
                    ->sortable(),

                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->color(fn ($state) => match ($state) {
                        'Online Meeting', 'In-person Meeting' => 'success',
                        'Call', 'Messaging' => 'info',
                        'Demo', 'Presentation' => 'warning',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('project.title')
                    ->label('Project')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('subject')
                    ->label('Subject')
                    ->searchable(),

                TextColumn::make('user.name')
                    ->label('Sales Rep')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('outcome')
                    ->badge()
                    ->color(fn ($state): string => match ($state) {
                        'Interested' => 'success',
                        'Not Interested' => 'danger',
                        'No Answer' => 'warning',
                        'Need more info' => 'info',
                        default => 'gray',
                    }),

                TextColumn::make('confidence_level')
                    ->label('Confidence')
                    ->suffix('%')
                    ->numeric()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('customer')
                    ->label('Customer')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->headerActions([
                Action::make('exportExcel')
                    ->label('Export to Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function ($query) {
                        return Excel::download(
                            new ActivitiesExport($query),
                            'activities-export-'.now()->format('Y-m-d').'.xlsx'
                        );
                    }),
            ])
            ->recordActions([
                ProjectResource::getChecklistAction(Action::class)
                    ->visible(fn ($record) => $record->project_id),
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (Activity $record) => self::canModifyRecord($record, 'user_id')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasRole('Super Admin')),
                ]),
            ]);
    }
}
