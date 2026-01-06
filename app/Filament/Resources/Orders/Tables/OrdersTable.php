<?php

namespace App\Filament\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('tahun')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('bulan')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('department.name')
                    ->searchable(),
                TextColumn::make('headPosition.name')
                    ->searchable(),
                TextColumn::make('pmJpmPePosition.name')
                    ->searchable(),
                TextColumn::make('rsmAsmPosition.name')
                    ->searchable(),
                TextColumn::make('spvPosition.name')
                    ->searchable(),
                TextColumn::make('srPosition.name')
                    ->searchable(),
                TextColumn::make('area_city_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('end_customer_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('customerGroup.name')
                    ->searchable(),
                TextColumn::make('cd_ncd_type')
                    ->searchable(),
                TextColumn::make('ncd_subtype')
                    ->searchable(),
                TextColumn::make('segment.name')
                    ->searchable(),
                TextColumn::make('principal.name')
                    ->searchable(),
                TextColumn::make('reg_inst')
                    ->searchable(),
                TextColumn::make('salesType.name')
                    ->searchable(),
                TextColumn::make('item.name')
                    ->searchable(),
                TextColumn::make('qty_hna')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_hna_gross_sales')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('discount_on')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('net_sales_total')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('subSegment.name')
                    ->searchable(),
                TextColumn::make('jual_kso')
                    ->searchable(),
                TextColumn::make('distributor.name')
                    ->searchable(),
                TextColumn::make('order_number')
                    ->searchable(),
                TextColumn::make('originalCustomer.id')
                    ->searchable(),
                TextColumn::make('lead.id')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('subtotal')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tax_amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('discount_amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('order_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('expected_delivery_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('actual_delivery_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('payment_method')
                    ->searchable(),
                TextColumn::make('payment_status')
                    ->badge(),
                TextColumn::make('created_by')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
