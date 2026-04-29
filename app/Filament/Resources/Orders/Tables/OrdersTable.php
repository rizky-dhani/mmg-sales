<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Filament\Traits\HasVisibilityScope;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OrdersTable
{
    use HasVisibilityScope;

    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                return self::applyVisibilityScope($query, 'created_by');
            })
            ->columns([
                TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('order_date')
                    ->label('Date')
                    ->date('d M Y')
                    ->formatStateUsing(fn ($state) => strtoupper(Carbon::parse($state)->translatedFormat('d M Y')))
                    ->sortable(),

                TextColumn::make('customer.name')
                    ->label('End Customer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('territory.name')
                    ->label('Area/City')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('item.name')
                    ->label('Product Item')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('qty_hna')
                    ->label('Qty')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('net_sales_total')
                    ->label('Net Sales')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                        'returned' => 'Returned',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'processing' => 'info',
                        'shipped' => 'primary',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                        'returned' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('payment_status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Pending',
                        'partial' => 'Partial',
                        'paid' => 'Paid',
                        'overdue' => 'Overdue',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'partial' => 'info',
                        'paid' => 'success',
                        'overdue' => 'danger',
                        default => 'gray',
                    }),

                // Metadata & Toggleable columns
                TextColumn::make('tahun')
                    ->label('Year')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('bulan')
                    ->label('Month')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('department.name')
                    ->label('Department')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('principal.name')
                    ->label('Principal')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('distributor.name')
                    ->label('Distributor')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('salesType.name')
                    ->label('Sales Type')
                    ->toggleable(isToggledHiddenByDefault: true),

                // Sales Hierarchy (Hidden by default)
                TextColumn::make('headPosition.name')
                    ->label('Head')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('pmJpmPePosition.name')
                    ->label('PM/JPM/PE')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('rsmAsmPosition.name')
                    ->label('RSM/ASM')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('spvPosition.name')
                    ->label('Supervisor')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('srPosition.name')
                    ->label('SR')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                        'returned' => 'Returned',
                    ]),
                SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'partial' => 'Partial',
                        'paid' => 'Paid',
                        'overdue' => 'Overdue',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (Order $record) => self::canModifyRecord($record, 'created_by')),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()?->hasRole('Super Admin')),
                ]),
            ]);
    }
}
