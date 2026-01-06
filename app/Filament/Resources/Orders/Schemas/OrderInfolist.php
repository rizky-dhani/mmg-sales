<?php

namespace App\Filament\Resources\Orders\Schemas;

use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('General Information')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('order_number')
                            ->label('Order #')
                            ->weight('bold'),
                        TextEntry::make('order_date')
                            ->label('Date')
                            ->date('d M Y')
                            ->formatStateUsing(fn ($state) => strtoupper(\Carbon\Carbon::parse($state)->translatedFormat('d M Y'))),
                        TextEntry::make('status')
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
                        TextEntry::make('payment_status')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => ucfirst($state))
                            ->color(fn (string $state): string => match ($state) {
                                'paid' => 'success',
                                'pending' => 'warning',
                                'overdue' => 'danger',
                                default => 'info',
                            }),
                        TextEntry::make('payment_method')
                            ->label('Payment Method'),
                    ]),

                Section::make('Customer & Logistics')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('customer.facility_name')
                            ->label('End Customer')
                            ->weight('bold'),
                        TextEntry::make('territory.name')
                            ->label('Area/City'),
                        TextEntry::make('customerGroup.name')
                            ->label('Customer Group'),
                        TextEntry::make('distributor.name')
                            ->label('Distributor'),
                        TextEntry::make('shipping_address')
                            ->label('Shipping Address')
                            ->columnSpanFull(),
                    ]),

                Section::make('Product Details')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('item.name')
                            ->label('Product Item')
                            ->weight('bold')
                            ->columnSpan(2),
                        TextEntry::make('qty_hna')
                            ->label('Quantity'),
                        TextEntry::make('principal.name')
                            ->label('Principal'),
                        TextEntry::make('segment.name')
                            ->label('Segment'),
                        TextEntry::make('subSegment.name')
                            ->label('Sub-Segment'),
                        TextEntry::make('subtotal')
                            ->label('Gross Sales')
                            ->money('IDR'),
                        TextEntry::make('discount_on')
                            ->label('Discount (%)')
                            ->suffix('%'),
                        TextEntry::make('net_sales_total')
                            ->label('Net Sales')
                            ->money('IDR')
                            ->weight('bold')
                            ->color('success'),
                    ]),

                Section::make('Organizational Details')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('department.name')
                            ->label('Department'),
                        TextEntry::make('headPosition.name')
                            ->label('Head Position'),
                        TextEntry::make('rsmAsmPosition.name')
                            ->label('RSM/ASM'),
                        TextEntry::make('spvPosition.name')
                            ->label('Supervisor'),
                        TextEntry::make('srPosition.name')
                            ->label('Sales Rep'),
                        TextEntry::make('creator.name')
                            ->label('Created By'),
                    ]),

                Section::make('Additional Notes')
                    ->schema([
                        TextEntry::make('notes')
                            ->hiddenLabel()
                            ->placeholder('No additional notes.'),
                    ]),
            ]);
    }
}
