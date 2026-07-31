<?php

namespace App\Filament\Resources\Leads\Tables;

use App\Filament\Traits\HasVisibilityScope;
use App\Models\Activity;
use App\Models\Lead;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class LeadsTable
{
    use HasVisibilityScope;

    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $user = auth()->user();

                // Territory-based scope: if user has territory, scope to territory hierarchy
                if ($user && ! $user->hasRole('Super Admin') && $user->territory_id) {
                    $territoryUserIds = User::where('territory_id', $user->territory_id)
                        ->pluck('id')
                        ->toArray();

                    $query->whereIn('created_by', array_merge($territoryUserIds, [$user->id]));
                } else {
                    self::applyVisibilityScope($query, 'created_by');
                }

                // Sort by latest activity on the lead (most recently worked leads first)
                return $query->orderByDesc(
                    Activity::query()
                        ->whereColumn('activities.lead_id', 'leads.id')
                        ->selectRaw('MAX(performed_at)')
                );
            })
            ->columns([
                TextColumn::make('lead_code')
                    ->label('Lead Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('customer_name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('contactPerson.name')
                    ->label('Contact Person')
                    ->searchable(),

                TextColumn::make('creator.name')
                    ->label('Creator')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('assignedCollaborators')
                    ->label('Assigned To')
                    ->searchable(query: fn (Builder $query, string $search): Builder => $query->whereHas('collaborators', fn ($q) => $q->where('name', 'like', "%{$search}%")))
                    ->getStateUsing(fn ($record) => $record->collaborators->pluck('name')->join(', ')),

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
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('estimated_revenue')
                    ->label('Expected Revenue')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('estimated_completion_date')
                    ->label('Est. Finish')
                    ->date('M Y')
                    ->formatStateUsing(fn ($state) => $state ? strtoupper(Carbon::parse($state)->translatedFormat('M Y')) : '-')
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

                TextColumn::make('customer.name')
                    ->label('Linked Customer')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('latestActivity.performed_at')
                    ->label('Last Contact')
                    ->date('d M Y')
                    ->description(fn (Lead $record): ?string => $record->latestActivity?->subject)
                    ->formatStateUsing(fn ($state) => $state ? strtoupper(Carbon::parse($state)->translatedFormat('d M Y')) : '-')
                    ->sortable(),

                TextColumn::make('converted_at')
                    ->label('Converted')
                    ->date('d M Y')
                    ->formatStateUsing(fn ($state) => $state ? strtoupper(Carbon::parse($state)->translatedFormat('d M Y')) : '-')
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
                EditAction::make()
                    ->visible(fn (Lead $record) => self::canModifyRecord($record, 'created_by')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasAnyBaseRole(['Super Admin', 'Staff', 'Supervisor', 'Regional Sales Manager', 'Area Sales Manager'])),
                    ForceDeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasRole('Super Admin')),
                    RestoreBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasRole('Super Admin')),
                ]),
            ]);
    }
}
